<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogsApiController extends Controller
{
    public function getBlogCategories(Request $request)
    {
        try {
            $limit = $request->input('limit');
            $offset = $request->input('offset');
            $search = $request->input('search', '');
            $include_inactive = (bool) $request->input('include_inactive', false);
            
            $query = BlogCategory::query();

            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            if (!$include_inactive) {
                $query->active();
            }

            $totalQuery = BlogCategory::query();
            if (!empty($search)) {
                $totalQuery->where('name', 'like', '%' . $search . '%');
            }
            if (!$include_inactive) {
                $totalQuery->active();
            }
            $total = $totalQuery->count();

            $query = $query->withCount(['activeBlogs'])->orderBy('id', 'desc');

            if (!empty($limit)) {
                $query->limit($limit);
                if (!empty($offset)) {
                    $query->offset($offset);
                }
            }

            $categories = $query->get();

            return CommonHelper::responseWithData($categories, $total);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function createBlogCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => 'string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return CommonHelper::responseError($validator->errors()->first());
            }

            $slug = $request->slug ?: preg_replace('/\s+/', '-', trim(
                preg_replace('/[^A-Za-z0-9 ]/', '', $request->name)
            ));

            $count = BlogCategory::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-{$count}";
            }

            $category = BlogCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'meta_title' => $request->meta_title,
                'meta_keywords' => $request->meta_keywords,
                'meta_description' => $request->meta_description,
                'status' => $request->status
            ]);

            return CommonHelper::responseSuccessWithData(__('category_created_successfully'), $category);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function updateBlogCategory(Request $request, $id)
    {
        try {
            $category = BlogCategory::find($id);
            if (!$category) {
                return CommonHelper::responseError(__('category_not_found'));
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => '|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return CommonHelper::responseError($validator->errors()->first());
            }

            $newSlug = $request->slug ?: preg_replace('/\s+/', '-', trim(
                preg_replace('/[^A-Za-z0-9 ]/', '', $request->name)
            ));
            
            $count = BlogCategory::where('slug', 'LIKE', "{$newSlug}%")->where('id', '!=', $category->id)->count();
            if ($count > 0) {
                $newSlug = "{$newSlug}-{$count}";
            }

            $category->update([
                'name' => $request->name,
                'slug' => $newSlug,
                'meta_title' => $request->meta_title,
                'meta_keywords' => $request->meta_keywords,
                'meta_description' => $request->meta_description,
                'status' => $request->status
            ]);

            return CommonHelper::responseSuccessWithData(__('category_updated_successfully'), $category);

        } catch (\Exception $e) {
            return CommonHelper::responseError(($e->getMessage()));
        }
    }

    public function deleteBlogCategory($id)
    {
        try {
            $category = BlogCategory::find($id);
            if (!$category) {
                return CommonHelper::responseError(__('category_not_found'));
            }

            if ($category->blogs()->count() > 0) {
                return CommonHelper::responseError(__('cannot_delete_category_with_blogs'));
            }

            $category->delete();

            return CommonHelper::responseSuccess(__('category_deleted_successfully'));

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function getBlogs(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);
            $search = $request->input('search', '');
            $category_id = $request->input('category_id', '');
            $slug = $request->input('slug', '');
            $include_inactive = (bool) $request->input('include_inactive', false);
            
            $query = Blog::with('category');

            if (!$include_inactive) {
                $query->whereHas('category', function($q) {
                    $q->where('status', 1);
                });
            }

            if (!empty($search)) {
                $query->search($search);
            }
            if (!empty($slug)) {
                $query->where('slug', $slug);
            }

            if (!empty($category_id)) {
                $query->byCategory($category_id);
            }

            if (!$include_inactive) {
                $query->active();
            }

            $totalQuery = Blog::query();
            
            if (!$include_inactive) {
                $totalQuery->whereHas('category', function($q) {
                    $q->where('status', 1);
                });
            }
            
            if (!empty($search)) {
                $totalQuery->search($search);
            }
            if (!empty($slug)) {
                $totalQuery->where('slug', $slug);
            }
            if (!empty($category_id)) {
                $totalQuery->byCategory($category_id);
            }
            if (!$include_inactive) {
                $totalQuery->active();
            }
            $total = $totalQuery->count();

            $blogs = $query->orderBy('id', 'desc')
                          ->limit($limit)
                          ->offset($offset)
                          ->get();

            return CommonHelper::responseWithData($blogs, $total);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function createBlog(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:blogs,slug',
                'category_id' => 'required|exists:blog_categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return CommonHelper::responseError($validator->errors()->first());
            }

            $slug = $request->slug ?: preg_replace('/\s+/', '-', trim(
                preg_replace('/[^A-Za-z0-9 ]/', '', $request->title)
            ));

            $count = Blog::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-{$count}";
            }

            $blogData = [
                'title' => $request->title,
                'slug' => $slug,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_keywords' => $request->meta_keywords,
                'meta_description' => $request->meta_description,
                'status' => $request->status
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('blogs', $imageName, 'public');
                $blogData['image'] = $imagePath;
            }

            $blog = Blog::create($blogData);
            $blog->load('category');

            return CommonHelper::responseSuccessWithData(__('blog_created_successfully'), $blog);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function updateBlog(Request $request, $id)
    {
        try {
            $blog = Blog::find($id);
            if (!$blog) {
                return CommonHelper::responseError(__('blog_not_found'));
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $id,
                'category_id' => 'required|exists:blog_categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return CommonHelper::responseError($validator->errors()->first());
            }

            $newSlug = $request->slug ?: preg_replace('/\s+/', '-', trim(
                preg_replace('/[^A-Za-z0-9 ]/', '', $request->title)
            ));
            
            $count = Blog::where('slug', 'LIKE', "{$newSlug}%")->where('id', '!=', $blog->id)->count();
            if ($count > 0) {
                $newSlug = "{$newSlug}-{$count}";
            }

            $blogData = [
                'title' => $request->title,
                'slug' => $newSlug,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_keywords' => $request->meta_keywords,
                'meta_description' => $request->meta_description,
                'status' => $request->status
            ];

            if ($request->hasFile('image')) {
                if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                    Storage::disk('public')->delete($blog->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('blogs', $imageName, 'public');
                $blogData['image'] = $imagePath;
            }

            $blog->update($blogData);
            $blog->load('category');

            return CommonHelper::responseSuccessWithData(__('blog_updated_successfully'), $blog);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function deleteBlog($id)
    {
        try {
            $blog = Blog::find($id);
            if (!$blog) {
                return CommonHelper::responseError(__('blog_not_found'));
            }

            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }

            $blog->delete();

            return CommonHelper::responseSuccess(__('blog_deleted_successfully'));

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function getBlogCategoriesForDropdown()
    {
        try {
            $categories = BlogCategory::active()
                                   ->select('id', 'name')
                                   ->orderBy('name', 'asc')
                                   ->get();

            return CommonHelper::responseSuccessWithData(__('success'), $categories);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

    public function trackBlogView(Request $request)
    {
        try {
            $blog_id = $request->input('blog_id');
            if (empty($blog_id)) {
                return CommonHelper::responseError(__('blog_id_is_required'));
            }
            $blog = Blog::find($blog_id);
            if (!$blog) {
                return CommonHelper::responseError(__('blog_not_found'));
            }

            $ipAddress = $request->ip();
            
            $existingView = BlogView::where('blog_id', $blog_id)
                                  ->where('ip_address', $ipAddress)
                                  ->first();

            if (!$existingView) {
                BlogView::create([
                    'blog_id' => $blog_id,
                    'ip_address' => $ipAddress
                ]);

                $blog->increment('views_count');
            }

            $totalViews = $blog->fresh()->views_count;

            return CommonHelper::responseSuccessWithData(__('view_tracked_successfully'), [
                'blog_id' => $blog_id,
                'total_views' => $totalViews
            ]);

        } catch (\Exception $e) {
            return CommonHelper::responseError($e->getMessage());
        }
    }

    public function getMostViewedBlogs(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);

            $blogs = Blog::active()
                        ->with('category')->whereHas('category', function($q) {
                            $q->where('status', 1);
                        })
                        ->orderBy('views_count', 'desc')
                        ->limit($limit)
                        ->get();

            return CommonHelper::responseSuccessWithData(__('success'), $blogs);

        } catch (\Exception $e) {
            return CommonHelper::responseError(__('something_went_wrong'));
        }
    }

}
