<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Seller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductVariant;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Tax;
use App\Models\Tag;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ProductApisController extends Controller
{
    public function getProducts(Request $request)
    {
        $limit = $request->input('per_page'); // Default items per page
        $offset = (($request->input('page')) - 1) * $limit; // Default page
        $filter = $request->input('filter', ''); // Filter query

        if (!isset($request->type)) {
            $sellers = Seller::where('status', 1)->select('id', 'name')->orderBy('id', 'DESC')->get()->makeHidden(['logo_url', 'national_identity_card_url', 'address_proof_url', 'categories_array'])->toArray();
        }
        $categories = Category::where('status', 1)
            ->select('id', 'name') // Only select specific columns
            ->orderBy('id', 'DESC')
            ->get()
            ->makeHidden(['image_url', 'has_child', 'has_active_child'])
            ->toArray();

        // Initialize an array to hold the where conditions
        $where = [];

        if (isset($request->is_approved) && $request->is_approved !== "") {
            $where[] = ['p.is_approved', '=', $request->is_approved];
        }

        if (isset($request->seller) && $request->seller !== "") {
            $where[] = ['p.seller_id', '=', $request->seller];
            // Get the assigned categories from the seller table
            $assignedCategories = Seller::where('id', $request->seller)->value('categories');

            // Convert the assigned categories into an array
            $categoryIds = explode(',', $assignedCategories);

            // Query the categories based on the assigned categories from the seller
            $categories = Category::whereIn('id', $categoryIds)->orderBy('id', 'DESC')->get()->toArray();
        }

        if (isset($request->category) && $request->category !== "") {
            $where[] = ['p.category_id', '=', $request->category];
        }

        // Packet products
        if (isset($request->type) && $request->type === 'packet_products') {
            $where[] = ['p.type', '=', 'packet'];
        }

        // Loose products
        if (isset($request->type) && $request->type === 'loose_products') {
            $where[] = ['p.type', '=', 'loose'];
        }

        // Sold Out
        if (isset($request->type) && $request->type === 'sold_out') {

            $where[] = ['pv.stock', '<=', 0];
            $where[] = ['pv.status', '=', 0];

            $where[] = ['p.is_unlimited_stock', '=', 0];
        }

        // Low Stock
        if (isset($request->type) && $request->type === 'low_stock') {
            $low_stock_limit = Setting::where('variable', 'low_stock_limit')->first();
            if ($low_stock_limit) {
                $where[] = ['pv.stock', '<=', $low_stock_limit['value']];
                $where[] = ['pv.status', '=', '1'];
                $where[] = ['p.is_unlimited_stock', '!=', '1'];
            }
        }

        $products  = \DB::table('products as p')->select(
            'p.id as id',
            'p.id as product_id',
            'p.name',
            'p.seller_id',
            'p.status',
            'p.tax_id',
            'p.image',
            's.name as seller_name',
            's.id as seller_id',
            'p.indicator',
            'p.is_approved',
            'p.manufacturer',
            'p.made_in',
            'p.return_status',
            'p.cancelable_status',
            'p.till_status',
            'pv.id as product_variant_id',
            'pv.price',
            'pv.discounted_price',
            'pv.measurement',
            'pv.status as pv_status',
            'pv.stock',
            'pv.stock_unit_id',
            'u.short_code',
            \DB::raw('(select short_code from units where units.id = pv.stock_unit_id) as stock_unit')
        )
            ->join('sellers as s', 'p.seller_id', '=', 's.id')
            ->join('product_variants as pv', 'p.id', '=', 'pv.product_id')
            ->join('units as u', 'pv.stock_unit_id', '=', 'u.id');

        // Add where conditions if any
        if (!empty($where)) {
            foreach ($where as $condition) {
                $products->where($condition[0], $condition[1], $condition[2]);
            }
        }
        $products = $products->orderBy('pv.id', 'desc');

        // Apply filter to all columns in all joined tables
        if ($filter) {
            $columns = [
                'p.id',
                'pv.id',
                'p.name',
                's.name',
                'pv.price',
                'pv.discounted_price',
                'pv.measurement',
                'pv.stock',
            ];

            $products = $products->where(function ($query) use ($filter, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$filter}%");
                }
            });
        }
        $total = $products->count();
        if (isset($limit)) {
            $products->limit($limit)->offset($offset);
        }
        $products = $products->get();
        $data = array(
            "categories" => $categories,
            "products" => $products,

        );
        if (!isset($request->type)) {
            $data["sellers"] = $sellers;
        }

        return CommonHelper::responseWithData($data, $total);
    }

    public function getProducts_sellerapp(Request $request)
    {
        try {

            $currency = Setting::get_value('currency');
            $user_id = $request->user('api-customers') ? $request->user('api-customers')->id : '';

            $limit = ($request->limit) ?? 10;
            $offset = ($request->offset) ?? 0;

            $sort = ($request->sort) ?? 'row_order';
            $order = ($request->order) ?? 'asc';

            if ($sort == 'new') {
                $sort = 'created_at DESC';
                $price = 'MIN(discounted_price)';
                $price_sort = 'pv.discounted_price  ASC';
            } elseif ($sort == 'old') {
                $sort = 'created_at ASC';
                $price = 'MIN(discounted_price)';
                $price_sort = 'pv.discounted_price  ASC';
            } elseif ($sort == 'high') {

                $sort = 'max_price DESC';

                $price = 'MAX(if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price))';
                $price_sort = 'if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price) DESC';
            } elseif ($sort == 'low') {
                $sort = 'min_price ASC';

                $price = 'MIN(if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price))';
                $price_sort = 'if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price) ASC';
            } elseif ($sort == 'discount') {
                $sort = 'cal_discount_percentage DESC';
                $price = 'MIN(if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price))';
                $price_sort = 'cal_discount_percentage DESC';
            } elseif ($sort == 'popular') {
                $sort = 'order_counter DESC';
                $price = 'MIN(pv.discounted_price)';
                $price_sort = 'order_counter DESC';
            } else {
                $sort = 'p.row_order ASC';
                $price = 'MIN(pv.discounted_price)';
                $price_sort = 'pv.id  ASC';
            }

            $category_id = $request->get('category_id');


            $seller_id = auth()->user()->seller->id;
            $brand_id = $request->get('brand_id');
            $seller_slug = '';
            $where = "";

            if (isset($request['search']) && $request['search'] != '') {
                $search = $request['search'];
                $where .= " AND ( p.`name` like '%" . $search . "%' OR p.`slug` like '%" . $search . "%' OR p.`tags` like '%" . $search . "%') ";
            }

            if (isset($request->section_id) && $request->section_id != "") {
                $section_id = $request->section_id;
                $section = Section::select("*")->where("id", "=", $section_id)->first();

                $product_ids = CommonHelper::getProductIdsSection($section);
                if ($product_ids !== "") {
                    $where .= "AND p.id IN  ($product_ids)";
                }
            }

            if (isset($request['seller_slug']) && !empty($request['seller_slug'])) {
                $seller_slug = $request['seller_slug'];
                if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                    $seller_category = Seller::where('slug', $seller_slug)->first(['categories']);
                    if (!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $data = explode(",", $category);
                        $search = (in_array($category_id, $data, TRUE)) ? 1 : 2;
                        if ($search == 2) {
                            return CommonHelper::responseError(__('no_products_found'));
                        } else {
                            $where .= " AND s.`slug` = '$seller_slug' AND p.`category_id` IN (" . $category_id . ") ";
                        }
                    } else {
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                } else {
                    $seller_category = Seller::where('slug', $seller_slug)->first(['categories']);
                    if (!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $where .= " AND s.`slug` =  '$seller_slug' AND p.category_id IN (" . $category . " )";
                    } else {
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                }
            }

            if (isset($request['slug']) && !empty($request['slug'])) {
                $slug = $request['slug'];
                $where .= " AND p.`slug` =  '$slug' ";
            }

            if (isset($seller_id) && !empty($seller_id) && is_numeric($seller_id)) {

                if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {

                    $seller_category = Seller::where('id', $seller_id)->first(['categories']);
                    if (!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $data = explode(",", $category);
                        $search = (in_array($category_id, $data, TRUE)) ? 1 : 2;
                        if ($search == 2) {
                            return CommonHelper::responseError(__('no_products_found'));
                        } else {
                            $where .= " AND p.`seller_id` = " . $seller_id . " AND p.`category_id` IN (" . $category_id . ") ";
                        }
                    } else {
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                } else {

                    $seller_category = Seller::where('id', $seller_id)->first(['categories']);
                    if (!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $where .= " AND p.`seller_id` = " . $seller_id . " AND p.category_id IN (" . $category . " )";
                    } else {
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                }
            }

            if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                if (!isset($seller_id) && empty($seller_id) && !isset($request['seller_slug']) && empty($request['seller_slug'])) {
                    $where .= " AND p.`category_id`=" . $category_id;
                }
            }


            if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                $where .= " AND p.`category_id`=" . $category_id;
            }

            if (isset($request['brand_id']) && !empty($request['brand_id']) && is_numeric($request['brand_id'])) {
                $where .= " AND p.`brand_id`=" . $brand_id;
            }

            $seller_id =  $seller_id;

            $products = array();
            $i = 0;

            $products = Product::select('p.*', 'p.type as d_type', 's.store_name as seller_name', 's.slug as seller_slug', 's.status as seller_status')
                ->from('products as p')
                ->leftJoin('sellers as s', 'p.seller_id', '=', 's.id')
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                ->whereIn('s.status', [1, 3])
                ->where('p.seller_id', $seller_id)

                ->groupBy("p.id");

            if (isset($request->min_price) && isset($request->max_price) && intval($request->max_price)) {
                $products = $products->havingRaw(" min_price > " . intval(intval($request->min_price) - 1) . " and max_price < " . intval(intval($request->max_price) + 1));
            }
            if (isset($request->search) && $request->search != '') {
                $search = $request->search;

                $products = $products->where(function ($query) use ($search) {
                    $query->where('p.name', 'like', '%' . $search . '%')
                        ->orWhere('p.slug', 'like', '%' . $search . '%')
                        ->orWhere('p.tags', 'like', '%' . $search . '%');
                });
            }

            if (isset($request->brand_ids) && $request->brand_ids != "") {
                $brand_ids = explode(",", $request->brand_ids);
                $products = $products->whereIn('p.brand_id', $brand_ids);
            }
            if (isset($request->sizes) && $request->sizes != "" && isset($request->unit_ids) && $request->unit_ids != "") {
                $sizes = explode(",", $request->sizes);
                $unit_ids = explode(",", $request->unit_ids);
                $products = $products->whereIn('pv.measurement', $sizes)->whereIn('pv.stock_unit_id', $unit_ids);
            }
            if (isset($request->is_approved) && $request->is_approved !== "") {
                $products = $products->where('p.is_approved', $request->is_approved);
            }

            $products_total = $products->get()->count();

            $products = $products->orderByRaw($sort)->skip($offset)->take($limit)->get();


            $products = $products->makeHidden([
                'seller_id',
                'row_order',
                'return_status',
                'cancelable_status',
                'till_status',
                'description',
                'status',
                'return_days',
                'pincodes',
                'cod_allowed',
                'pickup_location',
                'tags',
                'd_type',
                'seller_name',
                'seller_slug',
                'seller_status',
                'created_at',
                'updated_at',
                'deleted_at',
                'image',
                'other_images'
            ]);

            $i = 0;

            foreach ($products as $row) {

                $sql = ProductVariant::select(
                    '*',
                    DB::raw("(SELECT short_code FROM units u WHERE u.id=pv.stock_unit_id) as stock_unit_name")
                )
                    ->from('product_variants as pv')
                    ->where('pv.product_id', '=', $row['id'])
                    ->orderBy('pv.status', 'ASC');
                $variants = $sql->get();
                $variants = $variants->makeHidden(['product_id', 'status', 'measurement_unit_id', 'stock_unit_id', 'deleted_at']);
                if (empty($variants)) {
                    continue;
                }


                CommonHelper::getProductDetails($row['id'], $user_id, false);
                $variantArray = array();
                for ($k = 0; $k < count($variants); $k++) {
                    array_push($variantArray, CommonHelper::getProductVariant($variants[$k]['id'], $user_id));
                }
                $products[$i]['variants'] = $variantArray;
                $i++;
            }

            $productSql = Product::from('products as p')->select(
                DB::raw('COUNT(p.id) AS total'),
                DB::raw('MIN((select MIN(if(discounted_price > 0, discounted_price, price)) from product_variants where product_variants.product_id = p.id)) as min_price'),
                DB::raw('MAX((select MAX(if(discounted_price > 0, discounted_price, price)) from product_variants where product_variants.product_id = p.id)) as max_price')
            )->leftJoin('product_variants as pv', 'pv.product_id', '=', 'p.id')->where('p.seller_id', $seller_id);


            if (isset($request->min_price) && isset($request->max_price) && intval($request->max_price)) {
                $productSql = $productSql->havingRaw(" min_price > " . intval(intval($request->min_price) - 1) . " and max_price < " . intval(intval($request->max_price) + 1));
            }

            if (isset($request->brand_ids) && $request->brand_ids != "") {
                $brand_ids = explode(",", $request->brand_ids);
                $productSql = $productSql->whereIn('p.brand_id', $brand_ids);
            }
            if (isset($request->sizes) && $request->sizes != "" && isset($request->unit_ids) && $request->unit_ids != "") {
                $sizes = explode(",", $request->sizes);
                $unit_ids = explode(",", $request->unit_ids);
                $productSql = $productSql->whereIn('pv.measurement', $sizes)->whereIn('pv.stock_unit_id', $unit_ids);
            }

            if ($where != "") {
                $productSql = $productSql->whereRaw(substr($where, 4));
            }


            $productResult = $productSql->first();
            $total_min_price = $productResult->min_price;
            $total_max_price = $productResult->max_price;
            $total =  $productResult->total;

            if (!empty($products)) {
                $brands = CommonHelper::getBrandsHavingProducts();
                $sizes = CommonHelper::getProductVariantsSize();
                return Response::json(array(
                    'status' => 1,
                    'message' => 'success',
                    'total' => $products_total,
                    'data' => $products
                ));
            } else {
                return CommonHelper::responseError(__('no_products_found'));
            }
        } catch (\Exception $e) {
            Log::info("Products Error : " . $e->getMessage());
            throw $e;
            return CommonHelper::responseError("Something Went Wrong!");
        }
    }

    public function getActiveProducts()
    {
        $query = \DB::table('products as p')
            ->select(
                'p.id as id',
                'p.id as product_id',
                'p.name',
                'p.seller_id',
                'p.status',
                'p.tax_id',
                'p.image',
                's.name as seller_name',
                's.id as seller_id',
                'p.indicator',
                'p.manufacturer',
                'p.made_in',
                'p.return_status',
                'p.cancelable_status',
                'p.till_status',
                'pv.id as product_variant_id',
                'pv.price',
                'pv.discounted_price',
                'pv.measurement',
                'pv.status as pv_status',
                'pv.stock',
                'pv.stock_unit_id',
                'u.short_code',
                \DB::raw('(select short_code from units where units.id = pv.stock_unit_id) as stock_unit')
            )
            ->join('sellers as s', 'p.seller_id', '=', 's.id')
            ->join('product_variants as pv', 'p.id', '=', 'pv.product_id')
            ->join('units as u', 'pv.stock_unit_id', '=', 'u.id')
            ->where('p.status', 1);

        // Check role and filter by seller if applicable
        if (auth()->user()->role_id == Role::$roleSeller) {
            $query->where('p.seller_id', auth()->user()->seller->id);
        }

        $products = $query->orderBy('p.id', 'DESC')->get();

        return CommonHelper::responseWithData($products);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($request) {
                    $query->where('seller_id', $request->seller_id);
                })
            ],
            'seller_id' => 'required',

            'id' => 'nullable|integer',
            'image' => $request->id ? 'nullable' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required',

            'type' => 'required',
            'is_unlimited_stock' => 'required',

            'packet_measurement.*' =>  ['required_if:type,packet', 'numeric', Rule::notIn([0]),],
            'packet_price.*' =>  ['required_if:type,packet', 'numeric'],
            'packet_stock.*' =>  [
                'required_if:type,packet',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $status = $request->input("packet_status.{$index}", 1);

                    if ($request->input('is_unlimited_stock') == 0 && $value == 0 && $request->input('type') == 'packet' && $status != 0) {
                        $fail($attribute . ' must be greater than 0 when is_unlimited_stock is 0 and status is not "Sold Out".');
                    }
                },
            ],
            'packet_stock_unit_id.*' =>  ['required_if:type,packet', 'numeric'],

            'loose_measurement.*' =>  ['required_if:type,loose', 'numeric', Rule::notIn([0]),],
            'loose_price.*' =>  ['required_if:type,loose', 'numeric'],
            'loose_stock.*' =>  [
                'required_if:type,loose',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $status = $request->input('status', $request->input("loose_status.{$index}", 1));

                    if ($request->input('is_unlimited_stock') == 0 && strval($value) === '0' && $request->input('type') == 'loose' && intval($status) !== 0) {
                        $fail($attribute . ' must be greater than 0 when is_unlimited_stock is 0 and status is not "Sold Out".');
                    }
                },
            ],
            'loose_stock_unit_id' => ['required_if:type,loose', 'nullable', 'numeric'],
            'category_id' => 'required',
            'barcode' => 'nullable|unique:products,barcode',
        ], [
            'name.unique' => 'The product name has already been taken.',
            'seller_id.required' => 'The seller name field is required.',
            'is_unlimited_stock.required' => 'The Stock Limit field is required.',
            'category_id.required' => 'The Category name field is required.',
            'packet_measurement.*.required_if' => 'The Packet Measurement is required when the type is "Packet".',
            'packet_measurement.*.numeric' => 'The Packet Measurement  must be a number.',
            'packet_measurement.*.not_in' => 'The Packet Measurement must not be zero.',
            'packet_stock.*.required_if' => 'The Packet Stock is required when the type is "Packet".',
            'packet_stock.*.not_in' => 'The Packet Stock must not be zero.',
            'packet_stock_unit_id.*.required_if' => 'The Packet Stock Unit is required when the type is "Packet".',

            'loose_measurement.*.required_if' => 'The Loose Measurement is required when the type is "Loose".',
            'loose_measurement.*.numeric' => 'The Loose Measurement  must be a number.',
            'loose_measurement.*.not_in' => 'The Loose Measurement must not be zero.',
            'loose_stock_unit_id.required_if' => 'The Loose Stock Unit is required when the type is "Loose".',
            'loose_stock_unit_id.numeric' => 'The Loose Stock Unit must be a number.',

        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $variations = array();
        if ($request->type == "packet") {
            foreach ($request->packet_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->packet_measurement[$index];
                $data['price'] = $request->packet_price[$index];
                $data['discounted_price'] = $request->discounted_price[$index];
                $data['status'] = $request->packet_status[$index];
                $data['stock'] = ($request->is_unlimited_stock == 0) ? $request->packet_stock[$index] : 0;

                $data['stock_unit_id'] = $request->packet_stock_unit_id[$index];
                $variations[] = $data;
            }
        } else {
            foreach ($request->loose_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->loose_measurement[$index];
                $data['price'] = $request->loose_price[$index];
                $data['discounted_price'] = $request->loose_discounted_price[$index];
                $variations[] = $data;
            }
        }
        if (count($variations) !== count(array_unique($variations, SORT_REGULAR))) {
            return CommonHelper::responseError("Variations are duplicate!");
        }

        if ($request->max_allowed_quantity == "" || $request->max_allowed_quantity == 0) {
            $max_allowed_quantity = Setting::get_value('max_cart_items_count');
            if ($max_allowed_quantity == "" || $max_allowed_quantity == 0) {
                return CommonHelper::responseError("Maximum items allowed in cart in empty in store settings.");
            }
        } else {
            $max_allowed_quantity = $request->max_allowed_quantity;
        }

        DB::beginTransaction();

        try {
            $slug = $request->slug ?: preg_replace(
                '/\s+/',
                '-',
                trim(
                    preg_replace('/[^\p{L}\p{N} ]/u', '', $request->name)
                )
            );

            $count = Product::where('slug', 'LIKE', "{$slug}%")->count();

            $row_order = Product::max('row_order') + 1;
            $product = new Product();
            $product->name = $request->name;
            $product->slug = $count ? "{$slug}-{$count}" : $slug;
            $product->row_order = $row_order;
            $product->tax_id = $request->tax_id ?? "";
            $product->brand_id = $request->brand_id ?? "";
            $product->seller_id = $request->seller_id;
            $product->tags = $request->tags ?? "";
            $product->type = $request->type;
            $product->category_id = $request->category_id;
            $product->indicator = $request->product_type;
            $product->manufacturer = $request->manufacturer;
            $product->made_in = $request->made_in;
            $product->tax_included_in_price = $request->tax_included_in_price;
            $product->return_status = $request->return_status;
            $product->return_days = $request->return_days;
            $product->cancelable_status = $request->cancelable_status;
            $product->till_status = $request->till_status;
            $product->cod_allowed = $request->cod_allowed_status;
            $product->total_allowed_quantity = $max_allowed_quantity;
            $product->description = $request->description;
            $product->is_unlimited_stock = $request->is_unlimited_stock;
            $require_products_approval = Seller::where('id', $product->seller_id)->pluck('require_products_approval')->first();
            if ($require_products_approval == 1) {
                $product->is_approved = 0;
            } elseif ($require_products_approval == 0) {
                $product->is_approved = 1;
            }
            $product->status = 1;
            $product->brand_id = $request->brand_id;
            $product->fssai_lic_no = $request->fssai_lic_no ?? "";
            if ($request->fssai_lic_no != null) {
                $pattern = '/^[0-9]{14}$/';
                // Check if the FSSAI number matches the pattern
                if (preg_match($pattern, $request->fssai_lic_no)) {
                } else {
                    return CommonHelper::responseError("Please enter valid FSSAI no.");
                }
            }
            $product->barcode = $request->barcode ?? "";
            if ($request->barcode != null) {
                $pattern = '/^[a-zA-Z0-9-]+$/';
                if (preg_match($pattern, $request->barcode)) {
                } else {
                    return CommonHelper::responseError("Please enter valid Barcode");
                }
            }
            $product->meta_title = $request->meta_title ?? "";
            $product->meta_keywords = $request->meta_keywords ?? "";
            $product->schema_markup = $request->schema_markup ?? "";
            $product->meta_description = $request->meta_description ?? "";
            $image = '';
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . rand(1111, 99999) . '.' . $file->getClientOriginalExtension();
                $image = Storage::disk('public')->putFileAs('products', $file, $fileName);
            } else {
                $image = $request->image;
            }
            $product->image = $image;
            $product->save();

            if ($request->hasFile('other_images')) {
                CommonHelper::uploadProductImages($request->file('other_images'), $product->id);
            }

            /*Variance*/
            if ($request->type == "packet") {

                foreach ($request->packet_measurement as $index => $item) {

                    $data = array();
                    $data['product_id'] = $product->id;
                    $data['type'] = $request->type;
                    $data['measurement'] = $request->packet_measurement[$index];
                    $data['price'] = $request->packet_price[$index];
                    $data['discounted_price'] = isset($request->discounted_price[$index]) ? $request->discounted_price[$index] : 0;
                    $data['status'] = $request->packet_status[$index] ?? 1;
                    $data['stock'] = ($request->is_unlimited_stock == 0) ? $request->packet_stock[$index] : 0;
                    $data['stock_unit_id'] = isset($request->packet_stock_unit_id[$index]) ? $request->packet_stock_unit_id[$index] : 0;

                    ProductVariant::insert($data);
                    $variant_id = DB::getPdo()->lastInsertId();
                    if ($request->hasFile('packet_variant_images_' . $index)) {
                        CommonHelper::uploadProductImages($request->file('packet_variant_images_' . $index), $product->id, $variant_id);
                    }
                }
            }

            if ($request->type == "loose") {
                foreach ($request->loose_measurement as $index => $item) {

                    $data = array();
                    $data['product_id'] = $product->id;
                    $data['type'] = $request->type;
                    $data['stock'] = ($request->is_unlimited_stock == 0) ? $request->loose_stock[$index] : 0;
                    $data['stock_unit_id'] = $request->loose_stock_unit_id;
                    $data['status'] = $request->status;
                    $data['measurement'] = $request->loose_measurement[$index];
                    $data['price'] = $request->loose_price[$index];

                    $data['discounted_price'] = isset($request->loose_discounted_price[$index]) ? $request->loose_discounted_price[$index] : 0;

                    ProductVariant::insert($data);
                    $variant_id = DB::getPdo()->lastInsertId();
                    if ($request->hasFile('loose_variant_images_' . $index)) {
                        CommonHelper::uploadProductImages($request->file('loose_variant_images_' . $index), $product->id, $variant_id);
                    }
                }
            }
            $tagIds = array_filter(array_map('trim', explode(',', $request->tag_ids)), function ($value) {
                return $value !== '';
            });

            $product = Product::find($product->id);

            if ($product) {
                $existingTagIds = [];
                $newTagNames = [];

                // Separate integer IDs (existing tags) from string names (new tags)
                foreach ($tagIds as $tagId) {
                    if (is_numeric($tagId)) {
                        $existingTagIds[] = (int)$tagId;
                    } else {
                        $newTagNames[] = $tagId;
                    }
                }

                // Create new tags and get their IDs
                $newTagIds = [];
                foreach ($newTagNames as $tagName) {
                    $newTag = Tag::firstOrCreate(['name' => $tagName]);
                    $newTagIds[] = $newTag->id;
                }

                // Combine existing and new tag IDs
                $allTagIds = array_merge($existingTagIds, $newTagIds);

                // Sync the tags with the product
                $product->tags()->sync($allTagIds);
            }

            DB::commit();
        } catch (\Exception $e) {
            Log::info("Error : " . $e->getMessage());
            DB::rollBack();
            // throw $e;
            return CommonHelper::responseError($e->getMessage());
        }

        return CommonHelper::responseSuccess("Product Saved Successfully!");
    }

    public function edit($id)
    {
        $product = Product::with('seller', 'images', 'variants.images', 'variants.unit', 'category', 'tax', 'madeInCountry', 'tags')
            ->where('id', $id)->first();
        if (!$product) {
            return CommonHelper::responseError("Product Not found!");
        }
        return CommonHelper::responseWithData($product);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($request) {
                    $query->where('seller_id', $request->seller_id)->where('id', '!=', $request->id);
                })
            ],

            'seller_id' => 'required',
            'description' => 'required',

            'type' => 'required',
            'is_unlimited_stock' => 'required',

            'packet_measurement.*' =>  ['required_if:type,packet', 'numeric', Rule::notIn([0]),],
            'packet_price.*' =>  ['required_if:type,packet', 'numeric'],
            'packet_stock.*' => [
                'required_if:type,packet',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $status = $request->input("packet_status.{$index}", 1);

                    if ($request->input('type') === 'packet' && $request->input('is_unlimited_stock') == 0 && $value == 0 && $status != 0) {
                        $fail('The Packet Stock must be greater than 0 when Limited Stock Limit and status is not "Sold Out".');
                    }
                },
            ],
            'packet_stock_unit_id.*' =>  ['required_if:type,packet', 'numeric'],

            'loose_measurement.*' =>  ['required_if:type,loose', 'numeric', Rule::notIn([0]),],
            'loose_price.*' =>  ['required_if:type,loose', 'numeric'],
            'loose_stock.*' =>  [
                'required_if:type,loose',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $status = $request->input('status', $request->input("loose_status.{$index}", 1));

                    if ($request->input('is_unlimited_stock') == 0 && strval($value) === '0' && $request->input('type') == 'loose' && intval($status) !== 0) {
                        $fail($attribute . ' must be greater than 0 when is_unlimited_stock is 0 and status is not "Sold Out".');
                    }
                },
            ],
            'loose_stock_unit_id' => ['required_if:type,loose', 'nullable', 'numeric'],

            'category_id' => 'required',

        ], [
            'name.unique' => 'The product name has already been taken.',
            'seller_id.required' => 'The seller name field is required.',
            'is_unlimited_stock.required' => 'The Stock Limit field is required.',
            'category_id.required' => 'The Category name field is required.',
            'packet_measurement.*.required_if' => 'The Packet Measurement is required when the type is "Packet".',
            'packet_measurement.*.numeric' => 'The Packet Measurement  must be a number.',
            'packet_measurement.*.not_in' => 'The Packet Measurement must not be zero.',
            'packet_stock.*.required_if' => 'The Packet Stock is required when the type is "Packet".',
            'packet_stock.*.not_in' => 'The Packet Stock must not be zero.',
            'packet_stock_unit_id.*.required_if' => 'The Packet Stock Unit is required when the type is "Packet".',

            'loose_measurement.*.required_if' => 'The Loose Measurement is required when the type is "Loose".',
            'loose_measurement.*.numeric' => 'The Loose Measurement  must be a number.',
            'loose_measurement.*.not_in' => 'The Loose Measurement must not be zero.',
            'loose_stock_unit_id.required_if' => 'The Loose Stock Unit is required when the type is "Loose".',
            'loose_stock_unit_id.numeric' => 'The Loose Stock Unit must be a number.',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $variations = array();
        if ($request->type == "packet") {
            foreach ($request->packet_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->packet_measurement[$index];
                $data['price'] = $request->packet_price[$index];
                $data['discounted_price'] = $request->discounted_price[$index];
                $data['status'] = $request->packet_status[$index];
                $data['stock'] = $request->packet_stock[$index];
                $data['stock_unit_id'] = $request->packet_stock_unit_id[$index];
                $variations[] = $data;
            }
        } else {
            foreach ($request->loose_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->loose_measurement[$index];
                $data['price'] = $request->loose_price[$index];
                $data['discounted_price'] = $request->loose_discounted_price[$index];
                $variations[] = $data;
            }
        }
        if (count($variations) !== count(array_unique($variations, SORT_REGULAR))) {
            return CommonHelper::responseError("Variations are duplicate!");
        }

        if ($request->max_allowed_quantity == "" || $request->max_allowed_quantity == 0) {
            $max_allowed_quantity = Setting::get_value('max_cart_items_count');
            if ($max_allowed_quantity == " " || $max_allowed_quantity == 0) {
                return CommonHelper::responseError("Maximum items allowed in cart in empty in store settings.");
            }
        } else {
            $max_allowed_quantity = $request->max_allowed_quantity;
        }

        DB::beginTransaction();
        try {
            $product_image_ids = json_decode($request->deleteImageIds);
            if (count($product_image_ids) !== 0) {
                foreach ($product_image_ids as $index => $product_image_id) {
                    $image = ProductImages::find($product_image_id);
                    if ($image) {
                        $image->delete();
                    }
                }
            }

            $product = Product::find($request->id);
            $row_order = Product::max('row_order') + 1;

            if ($product->name !== $request->name) {
                $slug = $request->slug ?: preg_replace(
                    '/\s+/',
                    '-',
                    trim(
                        preg_replace('/[^\p{L}\p{N} ]/u', '', $request->name)
                    )
                );
                $count = Product::where('slug', 'LIKE', "{$slug}%")->where('id', '!=', $request->id)->count();
                $product->slug = $count ? "{$slug}-{$count}" : $slug;
            }

            $product->name = $request->name;

            $product->row_order = $row_order;
            $product->tax_id = $request->tax_id;
            $product->brand_id = $request->brand_id;
            $product->seller_id = $request->seller_id;
            $product->type = $request->type;
            $product->category_id = $request->category_id;
            $product->indicator = $request->product_type;
            $product->manufacturer = $request->manufacturer;
            $product->made_in = $request->made_in;
            $product->tax_included_in_price = $request->tax_included_in_price;
            $product->return_status = $request->return_status;
            $product->return_days = $request->return_days;
            $product->cancelable_status = $request->cancelable_status;
            $product->till_status = $request->till_status;
            $product->cod_allowed = $request->cod_allowed_status;
            $product->total_allowed_quantity = $max_allowed_quantity;
            $product->description = $request->description;
            $product->is_unlimited_stock = $request->is_unlimited_stock;
            if (isset($request->is_approved)) {
                $product->is_approved = $request->is_approved;
            }
            $product->fssai_lic_no = $request->fssai_lic_no ?? "";
            if ($request->fssai_lic_no != null) {
                $pattern = '/^[0-9]{14}$/';
                // Check if the FSSAI number matches the pattern
                if (preg_match($pattern, $request->fssai_lic_no)) {
                } else {
                    return CommonHelper::responseError("Please enter valid FSSAI no.");
                }
            }
            $product->barcode = $request->barcode ?? "";
            if ($request->barcode != null) {
                $pattern = '/^[a-zA-Z0-9-]+$/';
                // Check if the FSSAI number matches the pattern
                if (preg_match($pattern, $request->barcode)) {
                } else {
                    return CommonHelper::responseError("Please enter valid Barcode");
                }
            }
            $product->meta_title = $request->meta_title ?? "";
            $product->meta_keywords = $request->meta_keywords ?? "";
            $product->schema_markup = $request->schema_markup ?? "";
            $product->meta_description = $request->meta_description ?? "";

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . rand(1111, 99999) . '.' . $file->getClientOriginalExtension();
                $image = Storage::disk('public')->putFileAs('products', $file, $fileName);
                $product->image = $image;
            }
            if ($request->hasFile('other_images')) {
                CommonHelper::uploadProductImages($request->file('other_images'), $request->id);
            }



            if ($request->type == 'loose') {
                if ($request->status == 0) {
                    $product->status = 0; // here status 0 => "Sold Out" & 1 => "Available"

                } else {
                    $product->status = 1; // here status 0 => "Sold Out" & 1 => "Available"

                }
            }

            $product->save();

            //Variance
            if ($request->type == "packet") {
                foreach ($request->packet_measurement as $index => $item) {
                    $variant = ProductVariant::find($request->variant_id[$index]);
                    if (!$variant) {
                        $variant = new ProductVariant();
                    }
                    $variant->product_id = $product->id;
                    $variant->type = $request->type;
                    $variant->measurement = $request->packet_measurement[$index];
                    $variant->price = $request->packet_price[$index];
                    $variant->discounted_price = isset($request->discounted_price[$index]) ? $request->discounted_price[$index] : 0;
                    $variant->status = $request->packet_status[$index];
                    $variant->stock = ($request->is_unlimited_stock == 0) ? $request->packet_stock[$index] : 0;
                    $variant->stock_unit_id = isset($request->packet_stock_unit_id[$index]) ? $request->packet_stock_unit_id[$index] : 0;
                    $variant->save();
                    if ($request->hasFile('packet_variant_images_' . $index)) {
                        CommonHelper::uploadProductImages($request->file('packet_variant_images_' . $index), $product->id, $variant->id);
                    }
                }
            }

            if ($request->type == "loose") {
                foreach ($request->loose_measurement as $index => $item) {
                    $variant = ProductVariant::find($request->variant_id[$index]);
                    if (!$variant) {
                        $variant = new ProductVariant();
                    }
                    $variant->product_id = $product->id;
                    $variant->type = $request->type;
                    $variant->stock = ($request->is_unlimited_stock == 0) ? $request->loose_stock : 0;
                    $variant->stock_unit_id = $request->loose_stock_unit_id;
                    $variant->status = $request->status;
                    $variant->measurement = $request->loose_measurement[$index];
                    $variant->price = $request->loose_price[$index];
                    $variant->discounted_price = isset($request->loose_discounted_price[$index]) ? $request->loose_discounted_price[$index] : 0;
                    $variant->save();
                    if ($request->hasFile('loose_variant_images_' . $index)) {
                        CommonHelper::uploadProductImages($request->file('loose_variant_images_' . $index), $product->id, $variant->id);
                    }
                }
            }
            $tagIds = array_filter(array_map('trim', explode(',', $request->tag_ids)), function ($value) {
                return $value !== '';
            });

            $product = Product::find($product->id);

            if ($product) {
                $existingTagIds = [];
                $newTagNames = [];

                // Separate integer IDs (existing tags) from string names (new tags)
                foreach ($tagIds as $tagId) {
                    if (is_numeric($tagId)) {
                        $existingTagIds[] = (int)$tagId;
                    } else {
                        $newTagNames[] = $tagId;
                    }
                }

                // Create new tags and get their IDs
                $newTagIds = [];
                foreach ($newTagNames as $tagName) {
                    $newTag = Tag::firstOrCreate(['name' => $tagName]);
                    $newTagIds[] = $newTag->id;
                }

                // Combine existing and new tag IDs
                $allTagIds = array_merge($existingTagIds, $newTagIds);

                // Sync the tags with the product
                $product->tags()->sync($allTagIds);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("Error : " . $e->getMessage());
            throw $e;
            return CommonHelper::responseError("Something Went Wrong!");
        }
        return CommonHelper::responseSuccess("Product Updated Successfully!");
    }

    public function delete(Request $request)
    {
        if (isset($request->id)) {
            $productVariant = ProductVariant::find($request->id);

            if ($productVariant) {
                // Check if the product variant exists in order_items
                $orderItemExists = OrderItem::where('product_variant_id', $productVariant->id)->exists();

                if ($orderItemExists) {
                    return CommonHelper::responseError("This product variant cannot be deleted as it exists in orders.");
                }

                $product_id = $productVariant->product_id;

                $variantDeleteStatus = $productVariant->delete();
                $variants = ProductVariant::where('product_id', $product_id)->get();

                if ($variantDeleteStatus == true && $variants->count() == 0) {
                    $product = Product::find($product_id);
                    if ($product) {
                        $product->delete();
                    }
                }

                return CommonHelper::responseSuccess("Product Deleted Successfully!");
            } else {
                return CommonHelper::responseError("Product Already Deleted!");
            }
        }

        return CommonHelper::responseError("Invalid request!");
    }


    public function multipleDelete(Request $request)
    {
        if (isset($request->ids)) {
            $ids = explode(',', $request->ids);
            $productVariants = ProductVariant::with('images')->whereIn('id', $ids)->get();
            foreach ($productVariants as $productVariant) {
                $product_id = $productVariant->product_id;
                foreach ($productVariant->images as $image) {
                    @Storage::disk('public')->delete($image->image);
                    $image->delete();
                }
                $productVariant->delete();

                //If All variant deleted remove main product
                $product = Product::with('variants', 'images')->where('id', $product_id)->first();
                if ($product && count($product->variants) == 0) {
                    foreach ($productVariant->images as $image) {
                        @Storage::disk('public')->delete($image->image);
                        $image->delete();
                    }
                    @Storage::disk('public')->delete($product->image);
                    $product->delete();
                }
            }
            return CommonHelper::responseSuccess("Selected all Product Deleted Successfully!");
        }
    }

    public function changeStatus(Request $request)
    {
        if (isset($request->id)) {
            $product = Product::find($request->id);
            if ($product) {
                $product->status = ($product->status == 1) ? 0 : 1;
                $product->save();
                return CommonHelper::responseSuccess("Products Status Updated Successfully!");
            } else {
                return CommonHelper::responseSuccess("Products Record not found!");
            }
        }
    }

    public function getProductsOrderList(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $products = Product::where('category_id', $request->category_id)->orderBy('row_order', 'ASC')->get();
        return CommonHelper::responseWithData($products);
    }
    public function updateProductsOrder(Request $request)
    {
        $products = $request->all();
        foreach ($products as $key => $product) {
            $data = Product::find($product["id"]);
            $data->row_order = $product["row_order"];
            $data->save();
        }
        return CommonHelper::responseSuccess("Product Order Updated Successfully!");
    }

    public function bulkUpload(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls'
            ]);
            if ($validator->fails()) {
                return CommonHelper::responseError($validator->errors()->first());
            }

            $filename = $_FILES["file"]["tmp_name"];
            $fileExtension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

            // Validate file extension (only Excel files allowed)
            if ($fileExtension != 'xlsx' && $fileExtension != 'xls') {
                return CommonHelper::responseError('Only Excel files (.xlsx, .xls) are allowed.');
            }

            if ($_FILES["file"]["size"] > 0) {
                $errors = [];

                $dataRows = [];
                // Read Excel file
                if ($fileExtension == 'xlsx' || $fileExtension == 'xls') {
                    $spreadsheet = IOFactory::load($filename);
                    $sheet = $spreadsheet->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();

                    $headerRow = [];
                    $highestColumnIndex = $sheet->getHighestColumn();
                    $highestColumnNum = Coordinate::columnIndexFromString($highestColumnIndex);

                    // Read header row (row 1)
                    for ($colIndex = 1; $colIndex <= $highestColumnNum; $colIndex++) {
                        $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                        $cellValue = $sheet->getCell($colLetter . 1)->getValue();
                        $headerRow[] = $cellValue !== null ? (string)$cellValue : '';
                    }
                    $expectedColumnCount = count($headerRow);
                    $dataRows[] = $headerRow;

                    // Note: Only read rows that actually have data (at least Product Name should be filled)
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $rowData = [];
                        for ($colIndex = 1; $colIndex <= $expectedColumnCount; $colIndex++) {
                            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                            $cellValue = $sheet->getCell($colLetter . $row)->getValue();
                            $rowData[] = $cellValue !== null ? (string)$cellValue : '';
                        }
                        while (count($rowData) < $expectedColumnCount) {
                            $rowData[] = '';
                        }

                        // If Product Name is empty, the row is considered empty and should be skipped
                        if (!empty(trim($rowData[0]))) {
                            $dataRows[] = $rowData;
                        } else {
                            // If we encounter an empty row, stop reading further rows
                            break;
                        }
                    }
                } else {
                    return CommonHelper::responseError('Only Excel files (.xlsx, .xls) are allowed.');
                }

                $count = 0;
                foreach ($dataRows as $products) {
                    if ($count != 0) {
                        if (!is_array($products)) {
                            $errors[] = [
                                'row' => $count,
                                'errors' => ['Invalid row format.']
                            ];
                            $count++;
                            continue;
                        }

                        // Pad the array to ensure we have at least the minimum required columns (18 base columns)
                        while (count($products) < 18) {
                            $products[] = '';
                        }

                        $rowErrors = [];
                        if (empty($products[0])) {
                            $rowErrors[] = 'Product Name is empty';
                        }
                        if (empty($products[1])) {
                            $rowErrors[] = 'Category ID is empty or invalid';
                        }
                        if (empty($products[10])) {
                            $rowErrors[] = 'Seller ID is empty or invalid';
                        }
                        if (!empty($products[10])) {
                            $seller = Seller::select('name', 'categories', 'require_products_approval')->where('id', $products[10])->first();
                            if (empty($seller)) {
                                $rowErrors[] = 'Seller does not exist (check seller_id)';
                            } else {
                                // Sellers have parent categories assigned, but child categories should also be allowed
                                if (!empty($products[1])) {
                                    $categoryId = (int)$products[1];
                                    $sellerCategoryIds = explode(',', (string)$seller->categories);
                                    $sellerCategoryIds = array_map('trim', $sellerCategoryIds);
                                    $sellerCategoryIds = array_filter($sellerCategoryIds);

                                    // Check if category is directly assigned to seller
                                    $isCategoryValid = in_array((string)$categoryId, $sellerCategoryIds, true);

                                    // If not directly assigned, check if it's a child category with assigned parent
                                    if (!$isCategoryValid) {
                                        $category = Category::find($categoryId);
                                        if ($category) {
                                            $currentCategory = $category;
                                            while ($currentCategory && $currentCategory->parent_id != 0) {
                                                $parentCategory = Category::find($currentCategory->parent_id);
                                                if ($parentCategory && in_array((string)$parentCategory->id, $sellerCategoryIds, true)) {
                                                    $isCategoryValid = true;
                                                    break;
                                                }
                                                $currentCategory = $parentCategory;
                                                if (!$currentCategory || $currentCategory->id == $categoryId) {
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    if (!$isCategoryValid) {
                                        $rowErrors[] = 'Category ID is not assigned to seller (category or its parent category must be assigned)';
                                    }
                                }
                            }
                        }
                        // Validate Is Returnable - accept numeric (0,1) or descriptive (No, Yes)
                        if (isset($products[5]) && $products[5] !== '' && $products[5] !== null) {
                            $returnValue = is_numeric($products[5]) ? (int)$products[5] : strtolower(trim($products[5]));
                            if (!in_array($returnValue, [0, 1, 'no', 'yes'], true)) {
                                $rowErrors[] = 'Is Returnable must be 0/1 or No/Yes';
                            }
                        }

                        // Validate Is cancel-able - accept numeric (0,1) or descriptive (No, Yes)
                        if (isset($products[6]) && $products[6] !== '' && $products[6] !== null) {
                            $cancelValue = is_numeric($products[6]) ? (int)$products[6] : strtolower(trim($products[6]));
                            if (!in_array($cancelValue, [0, 1, 'no', 'yes'], true)) {
                                $rowErrors[] = 'Is cancel-able must be 0/1 or No/Yes';
                            }
                        }

                        // Till status validation: accept numeric values 1-5 or descriptive labels
                        if (isset($products[6]) && !empty($products[6])) {
                            $cancelValue = is_numeric($products[6]) ? (int)$products[6] : strtolower(trim($products[6]));
                            $isCancelable = in_array($cancelValue, [1, 'yes'], true);

                            if ($isCancelable) {
                                $tillStatus = isset($products[7]) ? $products[7] : '';
                                $tillStatusLower = is_string($tillStatus) ? strtolower(trim($tillStatus)) : $tillStatus;
                                $validTillStatus = [
                                    1,
                                    2,
                                    3,
                                    4,
                                    5,
                                    'payment pending',
                                    'received',
                                    'processed',
                                    'shipped',
                                    'out for delivery'
                                ];

                                if (empty($tillStatus) || !in_array($tillStatusLower, $validTillStatus, true)) {
                                    $rowErrors[] = 'Till status is empty or invalid (must be: Payment Pending, Received, Processed, Shipped, or Out for Delivery)';
                                }
                            }
                        }
                        if (isset($products[6]) && empty($products[6]) && isset($products[7]) && !empty($products[7])) {
                            $rowErrors[] = 'Till status provided but Is cancel-able is empty';
                        }
                        if (!isset($products[8]) || empty($products[8])) {
                            $rowErrors[] = 'Description is empty';
                        }
                        if (!isset($products[9]) || empty($products[9])) {
                            $rowErrors[] = 'Image is empty';
                        } else {
                            $imgPathOriginal = isset($products[9]) ? trim($products[9]) : '';
                            $imgPathNormalized = str_replace(' ', '-', strtolower($imgPathOriginal));
                            $isUrl = preg_match('/^https?:\/\//i', $imgPathNormalized);
                            if (!$isUrl) {
                                $existsOnDisk = Storage::disk('public')->exists($imgPathNormalized);
                                $existsInPublic = file_exists(public_path('storage/' . ltrim($imgPathNormalized, '/')));
                                if (!$existsOnDisk && !$existsInPublic) {
                                    $rowErrors[] = 'Image file not found: ' . $imgPathOriginal;
                                }
                            }
                        }
                        if (isset($products[11]) && !empty($products[11]) && $products[11] !== '') {
                            $approvedValue = is_numeric($products[11]) ? (int)$products[11] : strtolower(trim($products[11]));
                            if (!in_array($approvedValue, [0, 1, 'no', 'yes'], true)) {
                                $rowErrors[] = 'Is_approved must be 0/1 or No/Yes';
                            }
                        }

                        if (isset($products[14]) && !empty($products[14])) {
                            $tax = Tax::where('id', $products[14])->first();
                            if (empty($tax)) {
                                $rowErrors[] = 'Tax ID is invalid';
                            }
                        }

                        $columnsPerVariant = 9; // Excel format always has 9 columns per variant
                        $index1 = 17;
                        $total_variants = 0;
                        for ($j = 0; $j < 50; $j++) {
                            if (isset($products[$index1]) && $products[$index1] !== '') {
                                $total_variants++;
                            }
                            $index1 = $index1 + $columnsPerVariant;
                        }
                        if ($total_variants == 0) {
                            $rowErrors[] = 'At least one variant required';
                        }

                        $ids = Unit::select('id')->orderBy('id', 'ASC')->get();
                        $validUnitIds = $ids->pluck('id')->all();
                        $index1 = 17; // Starting after Barcode column (index 16, was 17 before removing Deliverable_Type)

                        // Collect all variant types to validate they are all the same
                        $variantTypes = [];
                        $columnsPerVariant = 9; // Excel format always has 9 columns per variant

                        for ($z = 0; $z < $total_variants; $z++) {
                            // type - check if index exists before accessing
                            if (!isset($products[$index1]) || empty($products[$index1]) || ($products[$index1] != 'packet' && $products[$index1] != 'loose')) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Type is empty or invalid';
                            } else {
                                // Collect variant type for validation
                                $variantTypes[] = strtolower(trim($products[$index1]));
                            }
                            $index1 = $index1 + 1;
                            // measurement
                            if (!isset($products[$index1]) || $products[$index1] === '' || !is_numeric($products[$index1])) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Measurement is empty or invalid';
                            }
                            $index1 = $index1 + 1;
                            // measurement unit id (required in Excel format)
                            if (!isset($products[$index1]) || $products[$index1] === '' || !in_array((int)$products[$index1], $validUnitIds, true)) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Measurement Unit ID is empty or invalid';
                            }
                            $index1 = $index1 + 1;
                            // price
                            if (!isset($products[$index1]) || $products[$index1] === '' || !is_numeric($products[$index1])) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Price is empty or invalid';
                            }
                            $index1 = $index1 + 1;
                            // discounted price
                            if (!isset($products[$index1]) || $products[$index1] === '' || !is_numeric($products[$index1])) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Discounted Price is empty or invalid';
                            }
                            $index1 = $index1 + 1;
                            // serve_for / status: accept numeric (0,1) or descriptive (Available, Sold Out)
                            if (isset($products[$index1]) && $products[$index1] !== '' && $products[$index1] !== null) {
                                $serveForValue = is_numeric($products[$index1]) ? (int)$products[$index1] : strtolower(trim($products[$index1]));
                                $validServeForValues = [0, 1, 'available', 'sold out'];
                                if (!in_array($serveForValue, $validServeForValues, true)) {
                                    $rowErrors[] = 'Variant ' . ($z + 1) . ': Serve For is invalid (must be 0/1 or Available/Sold Out)';
                                }
                            } else {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Serve For is empty or invalid (Available|Sold Out)';
                            }
                            $index1 = $index1 + 1;
                            // stock
                            if (!isset($products[$index1]) || $products[$index1] === '' || !is_numeric($products[$index1])) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Stock is empty or invalid';
                            }
                            $index1 = $index1 + 1;
                            // stock unit id
                            if (!isset($products[$index1]) || $products[$index1] === '' || !in_array((int)$products[$index1], $validUnitIds, true)) {
                                $rowErrors[] = 'Variant ' . ($z + 1) . ': Stock Unit ID is empty or invalid';
                            }
                            $index1 = $index1 + 1;
                        }

                        // Validate that all variants have the same type
                        // Product type will be taken from first variant, so all variants must match
                        if (!empty($variantTypes) && count(array_unique($variantTypes)) > 1) {
                            $uniqueTypes = array_unique($variantTypes);
                            $rowErrors[] = 'All variants must have the same type (either all "packet" or all "loose"). Found mixed types: ' . implode(', ', $uniqueTypes);
                        }

                        if (!empty($rowErrors)) {
                            $errors[] = [
                                'row' => $count,
                                'errors' => $rowErrors
                            ];
                        }
                    }
                    $count++;
                }

                // If any validation errors, return them all at once
                if (!empty($errors)) {
                    return CommonHelper::responseErrorWithData('Validation failed for some rows.', $errors);
                }

                // Proceed with insert when no errors - reuse the same dataRows array
                $count1 = 0;
                foreach ($dataRows as $emapData) {
                    if ($count1 != 0) {
                        // Ensure seller_id exists before querying
                        $seller_id_check = isset($emapData[10]) ? $emapData[10] : null;
                        $seller = null;
                        if ($seller_id_check) {
                            $seller = Seller::select('name', 'categories', 'require_products_approval')->where('id', $seller_id_check)->first();
                        }
                        $is_approved_by_seller = ($seller && isset($seller->require_products_approval) && $seller->require_products_approval == 0) ? 1 : 0;

                        $product_name = isset($emapData[0]) ? $emapData[0] : '';
                        $category_id = isset($emapData[1]) ? $emapData[1] : '';

                        // Convert Indicator from descriptive label to numeric value
                        // Column index 2: Indicator
                        $indicatorValue = isset($emapData[2]) ? $emapData[2] : 0;
                        if (is_string($indicatorValue)) {
                            $indicatorMap = ['empty' => 0, 'veg' => 1, 'non-veg' => 2, 'non veg' => 2];
                            $indicator = isset($indicatorMap[strtolower(trim($indicatorValue))]) ? $indicatorMap[strtolower(trim($indicatorValue))] : $indicatorValue;
                        } else {
                            $indicator = $indicatorValue;
                        }

                        $manufacturer = isset($emapData[3]) ? $emapData[3] : '';
                        $made_in = isset($emapData[4]) ? $emapData[4] : '';

                        // Convert Is Returnable from descriptive label to numeric value
                        // Column index 5: Is Returnable?
                        $returnValue = isset($emapData[5]) ? $emapData[5] : 0;
                        if (is_string($returnValue)) {
                            $returnMap = ['no' => 0, 'yes' => 1];
                            $return_status = isset($returnMap[strtolower(trim($returnValue))]) ? $returnMap[strtolower(trim($returnValue))] : $returnValue;
                        } else {
                            $return_status = !empty($returnValue) ? $returnValue : 0;
                        }

                        // Convert Is cancel-able from descriptive label to numeric value
                        // Column index 6: Is cancel-able
                        $cancelValue = isset($emapData[6]) ? $emapData[6] : 0;
                        if (is_string($cancelValue)) {
                            $cancelMap = ['no' => 0, 'yes' => 1];
                            $cancel_status = isset($cancelMap[strtolower(trim($cancelValue))]) ? $cancelMap[strtolower(trim($cancelValue))] : $cancelValue;
                        } else {
                            $cancel_status = !empty($cancelValue) ? $cancelValue : 0;
                        }

                        // Convert Till which status from descriptive label to numeric value
                        // Column index 7: Till which status
                        $tillStatusValue = isset($emapData[7]) ? $emapData[7] : '';
                        if (is_string($tillStatusValue)) {
                            $tillStatusMap = [
                                'payment pending' => 1,
                                'received' => 2,
                                'processed' => 3,
                                'shipped' => 4,
                                'out for delivery' => 5
                            ];
                            $till_status = isset($tillStatusMap[strtolower(trim($tillStatusValue))]) ? $tillStatusMap[strtolower(trim($tillStatusValue))] : $tillStatusValue;
                        } else {
                            $till_status = $tillStatusValue;
                        }

                        $description = isset($emapData[8]) ? $emapData[8] : '';
                        $image = isset($emapData[9]) ? str_replace(' ', '-', strtolower($emapData[9])) : '';
                        $seller_id = isset($emapData[10]) ? $emapData[10] : '';

                        // Convert Is_approved from descriptive label to numeric value
                        // Column index 11: Is_approved?
                        $approvedValue = isset($emapData[11]) ? $emapData[11] : '';
                        if (is_string($approvedValue) && $approvedValue != "") {
                            $approvedMap = ['no' => 0, 'yes' => 1];
                            $is_approved = isset($approvedMap[strtolower(trim($approvedValue))]) ? $approvedMap[strtolower(trim($approvedValue))] : $approvedValue;
                        } else {
                            $is_approved = (!empty($approvedValue) && $approvedValue != "") ? $approvedValue : $is_approved_by_seller;
                        }

                        // Column indices shifted by -1 after removing Deliverable_Type
                        // Column index 12: Brand_id (was 13)
                        $brand_id = (isset($emapData[12]) && $emapData[12] != "") ? $emapData[12] : 0;
                        $return_days = (isset($emapData[13]) && $emapData[13] != "") ? $emapData[13] : "0";
                        $tax_id = (isset($emapData[14]) && $emapData[14] != "") ? $emapData[14] : "0";
                        $fssai_lic_no = (isset($emapData[15]) && $emapData[15] != "") ? $emapData[15] : "";
                        $barcode = (isset($emapData[16]) && $emapData[16] != "") ? $emapData[16] : "";
                        $type = (isset($emapData[17]) && $emapData[17] != "") ? strtolower(trim($emapData[17])) : "";

                        $row_order = Product::max('row_order') + 1;

                        $product = new Product();
                        $product->name = $product_name;
                        $product->row_order = $row_order;
                        $slug = preg_replace(
                            '/\s+/',
                            '-',
                            trim(
                                preg_replace('/[^\p{L}\p{N} ]/u', '', $emapData[0])
                            )
                        );
                        $count = Product::where('slug', 'LIKE', "{$slug}%")->count();
                        $product->slug = $count ? "{$slug}-{$count}" : $slug;
                        $product->tags = $emapData[0];
                        $product->status = 1;
                        $product->cod_allowed = 1;
                        $product->total_allowed_quantity = 3;
                        $product->category_id = $category_id;
                        $product->indicator = $indicator;
                        $product->manufacturer = $manufacturer;
                        $product->made_in = $made_in;
                        $product->return_status = $return_status;
                        $product->cancelable_status = $cancel_status;
                        $product->till_status = $till_status;
                        $product->description = $description;
                        $product->image = $image;
                        $product->seller_id = $seller_id;
                        $product->is_approved = $is_approved;
                        $product->brand_id = $brand_id;
                        $product->return_days = $return_days;
                        $product->tax_id = $tax_id;
                        $product->fssai_lic_no = $fssai_lic_no;
                        $product->barcode = $barcode;
                        $product->type = $type;
                        $product->save();

                        $index1 = 17;
                        $total_variants = 0;
                        $columnsPerVariant = 9;
                        for ($j = 0; $j < 50; $j++) {
                            if (!empty($emapData[$index1])) {
                                $total_variants++;
                            }
                            $index1 = $index1 + $columnsPerVariant;
                        }

                        // Insert variants
                        $index = 17;
                        for ($i = 0; $i < $total_variants; $i++) {
                            // Check if we have enough columns in the row
                            if (!isset($emapData[$index])) {
                                break; // Not enough columns, skip remaining variants
                            }

                            $variant = new ProductVariant();
                            $variant->product_id = $product->id;
                            // Use product type for all variants (product type is from first variant, all variants must match)
                            $variant->type = $type; // Use the product type instead of reading from each variant column
                            $index++; // Skip the type column in data (we're using product type instead)
                            $variant->measurement = isset($emapData[$index]) ? $emapData[$index] : 0;
                            $index++;
                            // Skip Measurement Unit ID column (Excel format has it)
                            $index++; // Skip Measurement Unit ID column
                            $variant->price = isset($emapData[$index]) ? $emapData[$index] : 0;
                            $index++;
                            $variant->discounted_price = isset($emapData[$index]) ? $emapData[$index] : 0;
                            $index++;
                            $serveForValue = isset($emapData[$index]) ? $emapData[$index] : 1;
                            if (is_string($serveForValue)) {
                                $serveForMap = ['available' => 1, 'sold out' => 0];
                                $serveForLower = strtolower(trim($serveForValue));
                                $variant->status = isset($serveForMap[$serveForLower]) ? $serveForMap[$serveForLower] : 1;
                            } else {
                                $variant->status = !empty($serveForValue) ? $serveForValue : 0;
                            }
                            $index++;
                            $variant->stock = isset($emapData[$index]) ? $emapData[$index] : 0;
                            $index++;
                            $variant->stock_unit_id = isset($emapData[$index]) ? $emapData[$index] : 0;
                            $index++;

                            $variant->save();
                        }
                    }
                    $count1++;
                }
                // commit transaction
                DB::commit();
                return CommonHelper::responseSuccess("All Products Imported Successfully!");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return CommonHelper::responseError($e->getMessage());
        }
    }
    public function getProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $product_id = $request->product_id;
        $product = Product::with('seller', 'images', 'variants.images', 'variants.unit', 'category', 'tax', 'madeInCountry', 'brand', 'tags')->where('id', $product_id)->first();
        if (!$product) {
            return CommonHelper::responseError("Product Not found!");
        }
        return CommonHelper::responseWithData($product);
    }

    /**
     * Download sample Excel file for bulk upload (template with dropdowns)
     * This is for users to download a blank template to fill in
     */
    public function downloadSampleFileExcel(Request $request)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Base header fields matching the Excel template structure
        $header = ['Product Name', 'Category ID', 'Indicator', 'Manufacturer', 'Made in', 'Is Returnable?', 'Is cancel-able', 'Till which status', 'Description', 'image', 'Seller_id', 'Is_approved?', 'Brand_id', 'return_days', 'tax_id', 'Fssai No', 'Barcode'];

        // Add variant columns (3 variants as per Excel template)
        for ($i = 1; $i <= 3; $i++) {
            $header[] = "Type";
            $header[] = "Measurement";
            $header[] = "Measurement Unit ID";
            $header[] = "Price";
            $header[] = "Discounted Price";
            $header[] = "Serve For";
            $header[] = "Stock";
            $header[] = "Stock Unit ID";
        }

        // Set headers in first row
        $colIndex = 1;
        foreach ($header as $headerValue) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '1', $headerValue);
            $colIndex++;
        }

        // Style header row
        $sheet->getStyle('A1:' . Coordinate::stringFromColumnIndex(count($header)) . '1')
            ->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Add one example row (matching the Excel template) with descriptive labels for dropdown columns
        $exampleRow = [
            'Patanjali Biscuits', // Product Name
            30, // Category ID
            'Empty', // Indicator (will show as Empty, Veg, or Non-veg)
            'Patanjali', // Manufacturer
            'India', // Made in
            'No', // Is Returnable? (will show as No or Yes)
            'Yes', // Is cancel-able (will show as No or Yes)
            'Received', // Till which status (will show descriptive name)
            'Made with Pure Wheat Flour', // Description
            'upload/images/slider_(12).jpg', // image
            1, // Seller_id
            'Yes', // Is_approved? (will show as No or Yes)
            // 1, // Deliverable_Type
            0, // Brand_id
            2, // return_days
            0, // tax_id
            '12345678901234', // Fssai No
            'B123C456', // Barcode
            // Variant 1
            'packet', // Type
            1, // Measurement
            1, // Measurement Unit ID
            100, // Price
            91, // Discounted Price
            'Available', // Serve For (will show as Available or Sold Out)
            10, // Stock
            5, // Stock Unit ID
            // Variant 2
            'packet', // Type
            2, // Measurement
            2, // Measurement Unit ID
            200, // Price
            92, // Discounted Price
            'Available', // Serve For (will show as Available or Sold Out)
            10, // Stock
            5, // Stock Unit ID
            // Variant 3
            'packet', // Type
            3, // Measurement
            3, // Measurement Unit ID
            300, // Price
            93, // Discounted Price
            'Available', // Serve For (will show as Available or Sold Out)
            13, // Stock
            3, // Stock Unit ID
        ];

        $rowIndex = 2;
        $colIndex = 1;
        foreach ($exampleRow as $cellValue) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . $rowIndex, $cellValue);
            $colIndex++;
        }

        $this->addDropdownValidation($sheet, 'C', 'C', "Empty,Veg,Non-veg", 1000);
        $this->addDropdownValidation($sheet, 'F', 'F', "No,Yes", 1000);
        $this->addDropdownValidation($sheet, 'G', 'G', "No,Yes", 1000);
        $this->addDropdownValidation($sheet, 'H', 'H', "Payment Pending,Received,Processed,Shipped,Out for Delivery", 1000);
        $this->addDropdownValidation($sheet, 'L', 'L', "No,Yes", 1000);
        $typeCols = [18, 26, 34]; // Column indices for Type in 3 variants (1-based Excel indices)
        foreach ($typeCols as $idx => $colIndex) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $this->addDropdownValidation($sheet, $colLetter, $colLetter, "packet,loose", 1000);
        }

        $serveForCols = [23, 31, 39]; // Column indices for Serve For in 3 variants (1-based Excel indices)
        foreach ($serveForCols as $colIndex) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $this->addDropdownValidation($sheet, $colLetter, $colLetter, "Available,Sold Out", 1000);
        }

        // Auto-size columns
        $lastColumnIndex = count($header);
        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Create Excel writer and save to temporary file
        $writer = new Xlsx($spreadsheet);
        $filename = "products_sample.xlsx";

        // Save to a temporary file first, then return as download response
        $tempFile = tempnam(sys_get_temp_dir(), 'products_sample_');
        $writer->save($tempFile);

        // Return the file as a download response
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download existing products data for bulk update
     * This downloads all existing products with their data
     */
    public function downloadProductDataExcel(Request $request)
    {
        // Get products data
        $products = Product::with('variants')->get();
        if (auth()->user()->role_id == Role::$roleSeller) {
            $products = Product::with('variants')->where('seller_id', auth()->user()->seller->id)->get();
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Base header fields (column indices: A=1, B=2, C=3, D=4, etc.)
        $header =  ['ID', 'Product Name', 'Category ID', 'Indicator', 'Manufacturer', 'Made in', 'Is Returnable?', 'Is cancel-able', 'Till which status', 'Description', 'image', 'Seller_id', 'Is_approved?', 'Brand_id', 'return_days', 'tax_id', 'Fssai No', 'Barcode'];

        // Determine the maximum number of variants across all products
        $maxVariants = $products->map(function ($product) {
            return $product->variants->count();
        })->max();

        // Generate dynamic headers for variants
        for ($i = 1; $i <= $maxVariants; $i++) {
            $header[] = "Product Variant ID $i";
            $header[] = "Type $i";
            $header[] = "Measurement $i";
            $header[] = "Measurement Unit ID $i";
            $header[] = "Price $i";
            $header[] = "Discounted Price $i";
            $header[] = "Serve For $i";
            $header[] = "Stock $i";
            $header[] = "Stock Unit ID $i";
        }

        // Set headers in first row
        $colIndex = 1;
        foreach ($header as $headerValue) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '1', $headerValue);
            $colIndex++;
        }

        // Style header row
        $lastColLetter = Coordinate::stringFromColumnIndex(count($header));
        $sheet->getStyle('A1:' . $lastColLetter . '1')
            ->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Generate rows for each product
        $rowIndex = 2;
        foreach ($products as $product) {
            $description = str_replace(["\r", "\n"], ' ', $product->description);

            $colIndex = 1;
            // Base product fields
            $indicatorLabel = '';
            switch ((int)$product->indicator) {
                case 0:
                    $indicatorLabel = 'Empty';
                    break;
                case 1:
                    $indicatorLabel = 'Veg';
                    break;
                case 2:
                    $indicatorLabel = 'Non-veg';
                    break;
                default:
                    $indicatorLabel = $product->indicator;
            }
            // Is Returnable: 0=No, 1=Yes
            $returnLabel = ((int)$product->return_status == 1) ? 'Yes' : 'No';
            // Is cancel-able: 0=No, 1=Yes
            $cancelLabel = ((int)$product->cancelable_status == 1) ? 'Yes' : 'No';
            // Till which status: 1=Payment Pending, 2=Received, 3=Processed, 4=Shipped, 5=Out for Delivery
            $tillStatusLabels = [
                1 => 'Payment Pending',
                2 => 'Received',
                3 => 'Processed',
                4 => 'Shipped',
                5 => 'Out for Delivery'
            ];
            $tillStatusLabel = isset($tillStatusLabels[(int)$product->till_status]) ? $tillStatusLabels[(int)$product->till_status] : $product->till_status;
            // Is_approved: 0=No, 1=Yes
            $approvedLabel = ((int)$product->is_approved == 1) ? 'Yes' : 'No';

            $rowData = [
                $product->id,
                $product->name,
                $product->category_id,
                $indicatorLabel,
                $product->manufacturer,
                $product->made_in,
                $returnLabel,
                $cancelLabel,
                $tillStatusLabel,
                $description,
                $product->image,
                $product->seller_id,
                $approvedLabel,
                $product->brand_id,
                $product->return_days,
                $product->tax_id,
                $product->fssai_lic_no,
                $product->barcode,
            ];

            foreach ($rowData as $cellValue) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $cellValue);
                $colIndex++;
            }

            // Add variant data to the row
            foreach ($product->variants as $variant) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->id);
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->type);
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->measurement);
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, ''); // Measurement Unit ID
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->price);
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->discounted_price);
                $colIndex++;

                $serveForLabel = ((int)$variant->status == 1) ? 'Available' : 'Sold Out';
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $serveForLabel);
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->stock);
                $colIndex++;

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $variant->stock_unit_id);
                $colIndex++;
            }

            $remainingColumns = ($maxVariants - $product->variants->count()) * 9;
            for ($i = 0; $i < $remainingColumns; $i++) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, '');
                $colIndex++;
            }

            $rowIndex++;
        }

        $this->addDropdownValidation($sheet, 'D', 'D', "Empty,Veg,Non-veg", 1000);
        $this->addDropdownValidation($sheet, 'G', 'G', "No,Yes", 1000);
        $this->addDropdownValidation($sheet, 'H', 'H', "No,Yes", 1000);
        $this->addDropdownValidation($sheet, 'I', 'I', "Payment Pending,Received,Processed,Shipped,Out for Delivery", 1000);
        $this->addDropdownValidation($sheet, 'M', 'M', "No,Yes", 1000);

        $typeColStart = 20;
        $typeColStep = 9;
        for ($i = 0; $i < $maxVariants; $i++) {
            $typeColLetter = Coordinate::stringFromColumnIndex($typeColStart + ($i * $typeColStep));
            $this->addDropdownValidation($sheet, $typeColLetter, $typeColLetter, "packet,loose", 1000);
        }

        $serveForColStart = 25;
        $serveForColStep = 9;
        for ($i = 0; $i < $maxVariants; $i++) {
            $serveForColLetter = Coordinate::stringFromColumnIndex($serveForColStart + ($i * $serveForColStep));
            $this->addDropdownValidation($sheet, $serveForColLetter, $serveForColLetter, "Available,Sold Out", 1000);
        }

        // Auto-size columns
        $lastColumnIndex = count($header);
        $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumnIndex);

        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Create Excel writer and save to temporary file
        $writer = new Xlsx($spreadsheet);
        $filename = "products_sample.xlsx";

        // Save to a temporary file first, then return as download response
        $tempFile = tempnam(sys_get_temp_dir(), 'products_');
        $writer->save($tempFile);

        // Return the file as a download response
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Helper method to add dropdown validation to Excel cells
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param string $colStart Column letter (e.g., 'D')
     * @param string $colEnd Column letter (e.g., 'D')
     * @param string $formulaList Comma-separated list of values (e.g., "0,1,2")
     * @param int $maxRow Maximum row number to apply validation (default: 1000)
     */
    private function addDropdownValidation($sheet, $colStart, $colEnd, $formulaList, $maxRow = 1000)
    {
        // Convert comma-separated values to Excel formula format: "value1","value2","value3"
        $values = explode(',', $formulaList);
        $formula = '"' . implode('","', $values) . '"';

        // Apply validation to all rows from row 2 to maxRow (skip header row 1)
        // This ensures dropdowns work even when users add new rows
        for ($row = 2; $row <= $maxRow; $row++) {
            // Handle single column or column range
            if ($colStart == $colEnd) {
                // Single column - apply validation directly to each cell
                $cell = $sheet->getCell($colStart . $row);
                $validation = $cell->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1($formula);
            } else {
                // Column range - apply to each column in the range
                $startColIndex = Coordinate::columnIndexFromString($colStart);
                $endColIndex = Coordinate::columnIndexFromString($colEnd);

                for ($col = $startColIndex; $col <= $endColIndex; $col++) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $cell = $sheet->getCell($colLetter . $row);
                    $validation = $cell->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($formula);
                }
            }
        }
    }
    public function bulkUpdate(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls'
            ]);
            if ($validator->fails()) {
                return CommonHelper::responseError($validator->errors()->first());
            }

            $filename = $_FILES["file"]["tmp_name"];
            $fileExtension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

            // Validate file extension (only Excel files allowed)
            if ($fileExtension != 'xlsx' && $fileExtension != 'xls') {
                return CommonHelper::responseError('Only Excel files (.xlsx, .xls) are allowed.');
            }

            if ($_FILES["file"]["size"] > 0) {
                // Read data from Excel file
                $dataRows = [];
                if ($fileExtension == 'xlsx' || $fileExtension == 'xls') {
                    // Read Excel file
                    $spreadsheet = IOFactory::load($filename);
                    $sheet = $spreadsheet->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();
                    $highestColumnIndex = $sheet->getHighestColumn();
                    $highestColumnNum = Coordinate::columnIndexFromString($highestColumnIndex);

                    // Read header row (row 1)
                    $headerRow = [];
                    for ($colIndex = 1; $colIndex <= $highestColumnNum; $colIndex++) {
                        $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                        $cellValue = $sheet->getCell($colLetter . 1)->getValue();
                        $headerRow[] = $cellValue !== null ? (string)$cellValue : '';
                    }
                    $expectedColumnCount = count($headerRow);
                    $dataRows[] = $headerRow;

                    // Read data rows
                    // Note: Only read rows that actually have data (at least Product ID should be filled)
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $rowData = [];
                        for ($colIndex = 1; $colIndex <= $expectedColumnCount; $colIndex++) {
                            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                            $cellValue = $sheet->getCell($colLetter . $row)->getValue();
                            $rowData[] = $cellValue !== null ? (string)$cellValue : '';
                        }
                        // Ensure row has same number of columns as header
                        while (count($rowData) < $expectedColumnCount) {
                            $rowData[] = '';
                        }

                        // Skip empty rows - check if ID (first column) is empty
                        if (!empty(trim($rowData[0]))) {
                            $dataRows[] = $rowData;
                        } else {
                            // If we encounter an empty row, stop reading further rows
                            break;
                        }
                    }
                } else {
                    return CommonHelper::responseError('Only Excel files (.xlsx, .xls) are allowed.');
                }

                // Get headers from the first row
                $headers = array_shift($dataRows);

                // Process each data row
                foreach ($dataRows as $row) {
                    if (count($headers) !== count($row)) {
                        continue;
                    }
                    $rowData = array_combine($headers, $row);

                    // Skip if ID is empty
                    if (empty($rowData['ID'])) {
                        continue;
                    }

                    // Convert descriptive values to numeric for Indicator
                    $indicatorValue = isset($rowData['Indicator']) ? $rowData['Indicator'] : 0;
                    if (is_string($indicatorValue)) {
                        $indicatorMap = ['empty' => 0, 'veg' => 1, 'non-veg' => 2, 'non veg' => 2];
                        $indicator = isset($indicatorMap[strtolower(trim($indicatorValue))]) ? $indicatorMap[strtolower(trim($indicatorValue))] : $indicatorValue;
                    } else {
                        $indicator = !empty($indicatorValue) ? $indicatorValue : 0;
                    }

                    // Convert descriptive values for Is Returnable
                    $returnValue = isset($rowData['Is Returnable?']) ? $rowData['Is Returnable?'] : 0;
                    if (is_string($returnValue)) {
                        $returnMap = ['no' => 0, 'yes' => 1];
                        $return_status = isset($returnMap[strtolower(trim($returnValue))]) ? $returnMap[strtolower(trim($returnValue))] : $returnValue;
                    } else {
                        $return_status = !empty($returnValue) ? $returnValue : 0;
                    }

                    // Convert descriptive values for Is cancel-able
                    $cancelValue = isset($rowData['Is cancel-able']) ? $rowData['Is cancel-able'] : 0;
                    if (is_string($cancelValue)) {
                        $cancelMap = ['no' => 0, 'yes' => 1];
                        $cancel_status = isset($cancelMap[strtolower(trim($cancelValue))]) ? $cancelMap[strtolower(trim($cancelValue))] : $cancelValue;
                    } else {
                        $cancel_status = !empty($cancelValue) ? $cancelValue : 0;
                    }

                    // Convert descriptive values for Till which status
                    $tillStatusValue = isset($rowData['Till which status']) ? $rowData['Till which status'] : '';
                    if (is_string($tillStatusValue)) {
                        $tillStatusMap = [
                            'payment pending' => 1,
                            'received' => 2,
                            'processed' => 3,
                            'shipped' => 4,
                            'out for delivery' => 5
                        ];
                        $till_status = isset($tillStatusMap[strtolower(trim($tillStatusValue))]) ? $tillStatusMap[strtolower(trim($tillStatusValue))] : $tillStatusValue;
                    } else {
                        $till_status = !empty($tillStatusValue) ? $tillStatusValue : '';
                    }

                    // Convert descriptive values for Is_approved
                    $approvedValue = isset($rowData['Is_approved?']) ? $rowData['Is_approved?'] : 0;
                    if (is_string($approvedValue) && $approvedValue != "") {
                        $approvedMap = ['no' => 0, 'yes' => 1];
                        $is_approved = isset($approvedMap[strtolower(trim($approvedValue))]) ? $approvedMap[strtolower(trim($approvedValue))] : $approvedValue;
                    } else {
                        $is_approved = (!empty($approvedValue) && $approvedValue != "") ? $approvedValue : 0;
                    }

                    // Update product data
                    $product = Product::updateOrCreate(
                        ['id' => $rowData['ID']],
                        [
                            'name' => $rowData['Product Name'],
                            'category_id' => $rowData['Category ID'],
                            'indicator' => $indicator,
                            'manufacturer' => $rowData['Manufacturer'] ?? '',
                            'made_in' => $rowData['Made in'] ?? '',
                            'return_status' => $return_status,
                            'cancelable_status' => $cancel_status,
                            'till_status' => $till_status,
                            'description' => $rowData['Description'] ?? '',
                            'image' => $rowData['image'] ?? '',
                            'seller_id' => $rowData['Seller_id'],
                            'is_approved' => $is_approved,
                            'brand_id' => $rowData['Brand_id'] ?? 0,
                            'return_days' => $rowData['return_days'] ?? 0,
                            'tax_id' => $rowData['tax_id'] ?? 0,
                            'fssai_lic_no' => $rowData['Fssai No'] ?? '',
                            'barcode' => $rowData['Barcode'] ?? '',
                        ]
                    );

                    // Get product type from first variant
                    // Product type should be taken from first variant, and all variants must have the same type
                    $productType = '';
                    $variantIndex = 1;
                    $variantTypes = [];

                    // First pass: collect all variant types to validate they are all the same
                    while (isset($rowData["Product Variant ID {$variantIndex}"]) && !empty($rowData["Product Variant ID {$variantIndex}"])) {
                        $id = isset($rowData["Product Variant ID {$variantIndex}"]) ? (int)$rowData["Product Variant ID {$variantIndex}"] : null;
                        if ($id === null || $id <= 0) {
                            $variantIndex++;
                            continue;
                        }

                        $variantType = isset($rowData["Type {$variantIndex}"]) ? strtolower(trim($rowData["Type {$variantIndex}"])) : '';
                        if (!empty($variantType)) {
                            $variantTypes[] = $variantType;
                            // Get product type from first variant (Type 1)
                            if ($variantIndex == 1 && empty($productType)) {
                                $productType = $variantType;
                            }
                        }
                        $variantIndex++;
                    }

                    // Validate that all variants have the same type
                    if (!empty($variantTypes) && count(array_unique($variantTypes)) > 1) {
                        $uniqueTypes = array_unique($variantTypes);
                        DB::rollBack();
                        return CommonHelper::responseError('All variants must have the same type (either all "packet" or all "loose"). Found mixed types: ' . implode(', ', $uniqueTypes) . ' for product ID: ' . $rowData['ID']);
                    }

                    // Update product type if we have a valid type from first variant
                    if (!empty($productType) && ($productType == 'packet' || $productType == 'loose')) {
                        $product->type = $productType;
                        $product->save();
                    }

                    // Update product variants
                    $variantsData = [];
                    $variantIndex = 1;

                    while (isset($rowData["Product Variant ID {$variantIndex}"]) && !empty($rowData["Product Variant ID {$variantIndex}"])) {
                        // Fetch the variant data from $rowData and assign default values if necessary
                        $id = isset($rowData["Product Variant ID {$variantIndex}"]) ? (int)$rowData["Product Variant ID {$variantIndex}"] : null;
                        // Use product type for all variants (ensures consistency)
                        $type = !empty($productType) ? $productType : (isset($rowData["Type {$variantIndex}"]) ? strtolower(trim($rowData["Type {$variantIndex}"])) : '');
                        $measurement = isset($rowData["Measurement {$variantIndex}"]) ? $rowData["Measurement {$variantIndex}"] : '';
                        $price = isset($rowData["Price {$variantIndex}"]) ? (float)$rowData["Price {$variantIndex}"] : 0;
                        $discounted_price = isset($rowData["Discounted Price {$variantIndex}"]) ? (float)$rowData["Discounted Price {$variantIndex}"] : 0;

                        // Convert Serve For from descriptive label to numeric value
                        $serveForValue = isset($rowData["Serve For {$variantIndex}"]) ? $rowData["Serve For {$variantIndex}"] : 1;
                        if (is_string($serveForValue)) {
                            $serveForMap = ['available' => 1, 'sold out' => 0];
                            $serveForLower = strtolower(trim($serveForValue));
                            $status = isset($serveForMap[$serveForLower]) ? $serveForMap[$serveForLower] : 1;
                        } else {
                            $status = !empty($serveForValue) ? $serveForValue : 0;
                        }

                        $stock = isset($rowData["Stock {$variantIndex}"]) ? (int)$rowData["Stock {$variantIndex}"] : 0;
                        $stock_unit_id = isset($rowData["Stock Unit ID {$variantIndex}"]) ? (int)$rowData["Stock Unit ID {$variantIndex}"] : 1;

                        // Skip if ID is null or invalid
                        if ($id === null || $id <= 0) {
                            $variantIndex++;
                            continue;
                        }

                        // Add the validated data to the variantsData array
                        $variantsData[] = [
                            'id' => $id,
                            'product_id' => $product->id,
                            'type' => $type, // Use product type (from first variant)
                            'measurement' => $measurement,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'status' => $status,
                            'stock' => $stock,
                            'stock_unit_id' => $stock_unit_id,
                        ];

                        $variantIndex++;
                    }

                    foreach ($variantsData as $variantData) {
                        ProductVariant::updateOrCreate(
                            ['id' => $variantData['id']],
                            $variantData
                        );
                    }
                }

                DB::commit();
                return CommonHelper::responseSuccess("Products and variants updated successfully!");
            } else {
                return CommonHelper::responseError("File is empty!");
            }
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            return CommonHelper::responseError("Products and variants not updated! Error: " . $e->getMessage());
        }
    }
    public function getProductVariants(Request $request)
    {
        // Set default values for pagination
        $limit = $request->input('per_page', 10);
        $page = $request->input('page', 1); // Default to the first page if not provided
        $offset = ($page - 1) * $limit;

        if ($request->has('limit')) {
            $limit = $request->limit;
            $offset = $request->offset;
        }

        // Step 1: Query the database and retrieve the raw data
        $rawProducts = \DB::table('products as p')
            ->select(
                'p.id as id',
                'p.id as product_id',
                'p.name',
                'p.seller_id',
                'p.status',
                'p.tax_id',
                'p.image',
                's.name as seller_name',
                's.id as seller_id',
                'p.indicator',
                'p.manufacturer',
                'p.made_in',
                'pv.id as product_variant_id',
                'pv.type',
                'pv.price',
                'pv.discounted_price',
                'pv.measurement',
                'pv.status as pv_status',
                'pv.stock',
                'pv.stock_unit_id',
                'u.short_code',
                \DB::raw('(select short_code from units where units.id = pv.stock_unit_id) as stock_unit')
            )
            ->join('sellers as s', 'p.seller_id', '=', 's.id')
            ->join('product_variants as pv', 'p.id', '=', 'pv.product_id')
            ->join('units as u', 'pv.stock_unit_id', '=', 'u.id')
            ->where('p.is_unlimited_stock', 0);

        if ($request->has('search')) {
            $search = $request->input('search');
            $rawProducts = $rawProducts->where(function ($query) use ($search) {
                $query->where('p.name', 'LIKE', "%$search%")
                    ->orWhere('p.description', 'LIKE', "%$search%")
                    ->orWhere('s.name', 'LIKE', "%$search%")
                    ->orWhere('pv.id', 'LIKE', "%$search%")
                    ->orWhere('pv.stock', 'LIKE', "%$search%");
            });
        }

        if (auth()->user()->role_id == Role::$roleSeller) {
            $rawProducts = $rawProducts->where('p.seller_id', auth()->user()->seller->id);
        }

        $rawProducts = $rawProducts->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        // Step 2: Group the products and handle loose variants
        $groupedProducts = [];
        foreach ($rawProducts as $product) {
            // Manually append the image_url using the product model accessor
            $productModel = Product::find($product->product_id); // Fetch the product model
            if ($productModel) {
                $product->image_url = $productModel->image_url;
            } else {
                $product->image_url = null;
            }

            if (strtolower($product->type) == 'loose') {
                // Group loose type products into a single entry by product_id
                if (isset($groupedProducts[$product->product_id])) {
                    // Concatenate fields for loose products when multiple variants exist
                    $groupedProducts[$product->product_id]['measurement'] .= ', ' . $product->measurement . ' ' . $product->stock_unit;
                    $groupedProducts[$product->product_id]['price'] .= ',' . $product->price;
                    $groupedProducts[$product->product_id]['discounted_price'] .= ',' . $product->discounted_price;
                    $groupedProducts[$product->product_id]['stock_unit_id'] .= ',' . $product->stock_unit_id;
                    $groupedProducts[$product->product_id]['stock'] += $product->stock;
                } else {
                    // Initialize the first loose variant entry
                    $groupedProducts[$product->product_id] = [
                        'id' => $product->product_id,
                        'product_id' => $product->product_id,
                        'name' => $product->name,
                        'seller_id' => $product->seller_id,
                        'seller_name' => $product->seller_name,
                        'status' => $product->status,
                        'tax_id' => $product->tax_id,
                        'image' => $product->image,
                        'image_url' => $product->image_url,
                        'indicator' => $product->indicator,
                        'manufacturer' => $product->manufacturer,
                        'made_in' => $product->made_in,
                        'product_variant_id' => $product->product_variant_id,
                        'type' => $product->type,
                        'price' => $product->price,
                        'discounted_price' => $product->discounted_price,
                        'measurement' => $product->measurement . ' ' . $product->stock_unit,
                        'pv_status' => $product->pv_status,
                        'stock' => $product->stock,
                        'stock_unit_id' => $product->stock_unit_id,
                        'short_code' => $product->short_code,
                        'stock_unit' => $product->stock_unit
                    ];
                }
            } else {
                // For non-loose variants (packet type), add them as they are
                $groupedProducts[$product->product_variant_id] = [
                    'id' => $product->product_variant_id,
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'seller_id' => $product->seller_id,
                    'seller_name' => $product->seller_name,
                    'status' => $product->status,
                    'tax_id' => $product->tax_id,
                    'image' => $product->image,
                    'image_url' => $product->image_url,
                    'indicator' => $product->indicator,
                    'manufacturer' => $product->manufacturer,
                    'made_in' => $product->made_in,
                    'product_variant_id' => $product->product_variant_id,
                    'type' => $product->type,
                    'price' => $product->price,
                    'discounted_price' => $product->discounted_price,
                    'measurement' => $product->measurement . ' ' . $product->stock_unit,
                    'pv_status' => $product->pv_status,
                    'stock' => $product->stock,
                    'stock_unit_id' => $product->stock_unit_id,
                    'short_code' => $product->short_code,
                    'stock_unit' => $product->stock_unit
                ];
            }
        }

        // Step 3: Apply limit and offset after grouping
        $groupedProducts = array_values($groupedProducts); // Ensure it's indexed
        $totalCount = count($groupedProducts); // Get the total count

        // Apply pagination (limit and offset)
        $groupedProducts = array_slice($groupedProducts, $offset, $limit);

        return CommonHelper::responseWithData($groupedProducts, $totalCount);
    }

    public function updateVariantStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:product_variants,id',
            'stock' => 'required|integer|min:0|max:100000',
        ], [
            'id.required' => 'The product variant ID is required.',
            'id.integer' => 'The product variant ID must be a valid integer.',
            'id.exists' => 'The selected product variant does not exist.',

            'stock.required' => 'The stock quantity must be a valid integer.',
            'stock.integer' => 'The stock quantity must be a valid integer.',
            'stock.min' => 'The stock quantity cannot be less than 0.'
        ]);

        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $variant = ProductVariant::findOrFail($request->id);
        if ($variant->type == 'packet') {

            $variant->stock = $request->stock;
            $variant->save();

            if ($variant->stock <= 0) {
                $variant->status = 0; // here status 0 => "Sold Out" & 1 => "Available"
                $variant->save();
            } else {
                $variant->status = 1; // here status 0 => "Sold Out" & 1 => "Available"
                $variant->save();
            }
        } else if ($variant->type == 'loose') {
            // Update stock value
            $product_id = $variant->product_id;
            $product = Product::find($product_id); // Use find() for a single record

            // Check if product is found
            if ($product) {
                // Update product status based on request stock
                $product->status = $request->stock <= 0 ? 0 : 1; // 0 => "Sold Out", 1 => "Available"
                $product->save(); // Save the product status

                // Fetch all loose variants for the product
                $loose_variants = ProductVariant::where('product_id', $product_id)->get();

                foreach ($loose_variants as $loose_variant) {
                    // Update stock for each loose variant
                    $loose_variant->stock = $request->stock;
                    $loose_variant->status = $request->stock <= 0 ? 0 : 1; // Set status based on stock
                    $loose_variant->save(); // Save each loose variant
                }
            }
        }

        return CommonHelper::responseSuccess('Stock updated successfully');
    }
}
