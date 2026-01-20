<template>
    <div>
        <div class="page-heading">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ __('blogs') }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><router-link to="/dashboard">{{ __('dashboard') }}</router-link></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('blogs') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 order-md-1 order-last">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('blogs') }}</h4>
                            <span class="pull-right">
                                <button class="btn btn-primary" @click="openAddModal" v-b-tooltip.hover :title="__('add_new_blog')" v-if="$can('blog_create')">{{ __('add_blog') }}</button>
                            </span>
                        </div>
                        <div class="card-body">
                            <b-row class="mb-2">
                                <b-col md="2">
                                    <h6 class="box-title">{{ __('category') }}</h6>
                                    <b-form-select v-model="selectedCategory" @change="getBlogs()" class="form-control form-select">
                                        <option value="">{{ __('all_categories') }}</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                    </b-form-select>
                                </b-col>
                                <b-col md="3" offset-md="5">
                                    <h6 class="box-title">{{ __('search') }}</h6>
                                    <b-form-input
                                        id="filter-input"
                                        v-model="filter"
                                        type="search"
                                        :placeholder="__('search')"
                                    ></b-form-input>
                                </b-col>
                                <b-col md="1" class="text-center">
                                    <button class="btn btn-primary btn_refresh" v-b-tooltip.hover :title="__('refresh')" @click="getBlogs()">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </button>
                                </b-col>
                            </b-row>
                            <b-table
                                :items="blogs"
                                :fields="fields"
                                :filter="filter"
                                :filter-included-fields="filterOn"
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                :sort-direction="sortDirection"
                                :bordered="true"
                                :busy="isLoading"
                                stacked="md"
                                show-empty
                                small>

                                <template #table-busy>
                                    <div class="text-center text-black my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>{{ __('loading') }}...</strong>
                                    </div>
                                </template>

                                <template #cell(image)="row">
                                    <img v-if="row.item.image_url" :src="row.item.image_url" height="50" />
                                    <span v-else class="text-muted">{{ __('no_image') }}</span>
                                </template>

                                <template #cell(category)="row">
                                    <span>{{ row.item.category ? row.item.category.name : '-' }}</span>
                                </template>

                                <template #cell(status)="row">
                                    <span class='badge bg-success' v-if="row.item.status == 1">{{ __('active') }}</span>
                                    <span class='badge bg-danger' v-if="row.item.status == 0">{{ __('deactive') }}</span>
                                </template>

                                <template #cell(actions)="row">
                                    <button class="btn btn-sm btn-primary" @click="edit_record = row.item" v-if="$can('blog_update')" v-b-tooltip.hover :title="__('edit')"><i class="fa fa-pencil-alt"></i></button>
                                    <button class="btn btn-sm btn-danger" @click="deleteBlog(row.index,row.item.id)" v-if="$can('blog_delete')" v-b-tooltip.hover :title="__('delete')"><i class="fa fa-trash"></i></button>
                                </template>

                            </b-table>
                            <b-row>
                                <b-col md="2" class="my-1">
                                    <label>
                                        <b-form-group
                                            :label="__('per_page')"
                                            label-for="per-page-select"
                                            label-align-sm="right"
                                            label-size="sm"
                                            class="mb-0">
                                            <b-form-select
                                                id="per-page-select"
                                                v-model="perPage"
                                                :options="pageOptions"
                                                size="sm"
                                                class="form-control form-select"
                                            ></b-form-select>
                                        </b-form-group>
                                    </label>
                                </b-col>
                                <b-col md="2" class="my-1" offset-md="8">
                                    <b-pagination
                                        v-model="currentPage"
                                        :total-rows="totalRows"
                                        :per-page="perPage"
                                        align="fill"
                                        size="sm"
                                        class="my-0"
                                    ></b-pagination>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Blog Modal -->
        <b-modal v-model="create_new" :title="edit_record.id ? __('edit_blog') : __('add_blog')" size="xl" :hide-footer="true" @hide="resetForm">
            <form @submit.prevent="saveBlog" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">{{ __('title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" v-model="form.title" :placeholder="__('enter_blog_title')" required @keyup="createSlug">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">{{ __('slug') }}</label>
                            <input type="text" class="form-control" id="slug" v-model="form.slug" :placeholder="__('enter_slug')">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">{{ __('category') }} <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-control form-select" v-model="form.category_id" required>
                                <option value="">{{ __('select_category') }}</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('status') }} <span class="text-danger">*</span></label>
                            <div class="col-md-9 text-left mt-1">
                                <b-form-radio-group
                                    v-model="form.status"
                                    :options="[
                                        { text: ' Deactivated', 'value': 0 },
                                        { text: ' Activated', 'value': 1 },
                                    ]"
                                    buttons
                                    button-variant="outline-primary"
                                    required
                                ></b-form-radio-group>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image">{{ __('image') }}</label> <span class="text-danger">*</span>
                            <input type="file" class="form-control" id="image" @change="handleImageUpload" accept="image/*" required>
                            <small class="text-muted">{{ __('supported_formats') }}: JPG, PNG, GIF ({{ __('max_size') }}: 2MB)</small>
                            <div v-if="form.image_url" class="mt-2">
                                <img :src="form.image_url" height="100" class="img-thumbnail">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">{{ __('description') }} <span class="text-danger">*</span></label>
                            <editor
                                v-model="form.description"
                                :init="{
                                    height: 300,
                                    plugins: this.$editorPlugins,
                                    toolbar: this.$editorToolbar,
                                    font_size_formats: this.$editorFont_size_formats,
                                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
                                }"
                                :placeholder="__('enter_blog_description')"
                            />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="meta_title">{{ __('meta_title') }}</label>
                            <input type="text" class="form-control" id="meta_title" v-model="form.meta_title" :placeholder="__('enter_meta_title')">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="meta_keywords">{{ __('meta_keywords') }}</label>
                            <textarea class="form-control" id="meta_keywords" v-model="form.meta_keywords" rows="3" :placeholder="__('enter_meta_keywords')"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="meta_description">{{ __('meta_description') }}</label>
                            <textarea class="form-control" id="meta_description" v-model="form.meta_description" rows="3" :placeholder="__('enter_meta_description')"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="button" class="btn btn-secondary" @click="create_new=false; resetForm()">{{ __('cancel') }}</button>
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span v-if="isSubmitting">{{ __('saving') }}...</span>
                        <span v-else>{{ __('save') }}</span>
                    </button>
                </div>
            </form>
        </b-modal>
    </div>
</template>

<script>
import Editor from '@tinymce/tinymce-vue'
import axios from 'axios';

export default {
    name: 'Blogs',
    components: {
        Editor
    },
    data() {
        return {
            blogs: [],
            categories: [],
            create_new: false,
            edit_record: {},
            form: {
                title: '',
                slug: '',
                category_id: '',
                image: null,
                image_url: '',
                description: '',
                meta_title: '',
                meta_keywords: '',
                meta_description: '',
                status: 1
            },
            isLoading: false,
            isSubmitting: false,
            filter: '',
            filterOn: ['title', 'description'],
            sortBy: 'id',
            sortDesc: true,
            sortDirection: 'desc',
            selectedCategory: '',
            fields: [
                { key: 'id', label: __('id'), sortable: true, class: 'text-center' },
                { key: 'image', label: __('image'), class: 'text-center' },
                { key: 'title', label: __('title'), sortable: true, class: 'text-center' },
                { key: 'category', label: __('category'), class: 'text-center' },
                { key: 'status', label: __('status'), class: 'text-center' },
                { key: 'actions', label: __('actions'), class: 'text-center' }
            ],
            perPage: 10,
            currentPage: 1,
            totalRows: 0,
            pageOptions: [5, 10, 15, 20, 25, 50, 100]
        }
    },
    mounted() {
        this.getBlogs();
        this.getCategories();
    },
    methods: {
        // Get all blogs
        async getBlogs() {
            this.isLoading = true;
            try {
                const params = {
                    limit: this.perPage,
                    offset: (this.currentPage - 1) * this.perPage,
                    include_inactive: 1
                };
                
                if (this.selectedCategory) {
                    params.category_id = this.selectedCategory;
                }

                const response = await axios.get(this.$apiUrl + '/blogs/', { params });
                if (response.data.status === 1) {
                    this.blogs = response.data.data;
                    this.totalRows = response.data.total;
                } else {
                    this.showMessage("error", response.data.message);
                }
            } catch (error) {
                this.showMessage("error", __('something_went_wrong'));
            } finally {
                this.isLoading = false;
            }
        },

        async getCategories() {
            try {
                const response = await axios.get(this.$apiUrl + '/blog_categories/dropdown');
                if (response.data.status === 1) {
                    this.categories = response.data.data;
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.image = file;
                this.form.image_url = URL.createObjectURL(file);
            }
        },

        async saveBlog() {
            this.isSubmitting = true;
            try {
                const formData = new FormData();
                formData.append('title', this.form.title);
                formData.append('slug', this.form.slug);
                formData.append('category_id', this.form.category_id);
                formData.append('description', this.form.description);
                formData.append('meta_title', this.form.meta_title);
                formData.append('meta_keywords', this.form.meta_keywords);
                formData.append('meta_description', this.form.meta_description);
                formData.append('status', this.form.status);

                if (this.form.image) {
                    formData.append('image', this.form.image);
                }

                let response;
                if (this.edit_record.id) {
                    response = await axios.post(this.$apiUrl + `/blogs/update/${this.edit_record.id}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                } else {
                    response = await axios.post(this.$apiUrl + '/blogs/save', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                }

                if (response.data.status === 1) {
                    this.showMessage("success", response.data.message);
                    this.create_new = false;
                    this.resetForm();
                    this.getBlogs();
                } else {
                    this.showMessage("error", response.data.message);
                }
            } catch (error) {
                this.showMessage("error", __('something_went_wrong'));
            } finally {
                this.isSubmitting = false;
            }
        },

        async deleteBlog(index, id) {
           this.$swal.fire({
                title: "Are you Sure?",
                text: "You want be able to revert this",
                confirmButtonText: "Yes, Sure",
                cancelButtonText: "Cancel",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#37a279',
                cancelButtonColor: '#d33',
            }).then(result => {
                if (result.value) {
                    this.isLoading = true;
                    axios.post(this.$apiUrl + `/blogs/delete/${id}`)
                        .then((response) => {
                            this.isLoading = false;
                            if (response.data.status === 1) {
                                this.showMessage('success', response.data.message);
                                this.getBlogs();
                            } else {
                                this.showMessage('error', response.data.message);
                            }
                        })
                        .catch(error => {
                            this.isLoading = false;
                            this.showMessage('error', __('something_went_wrong'));
                        });
                }
            });
        },

        resetForm() {
            this.form = {
                title: '',
                slug: '',
                category_id: '',
                image: null,
                image_url: '',
                description: '',
                meta_title: '',
                meta_keywords: '',
                meta_description: '',
                status: 1
            };
            this.edit_record = {};
        },

        // Create slug from title
        createSlug() {
            if (this.form.title !== "") {
                let slug = this.form.title.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                this.form.slug = slug;
            }
        },
        
        // Open add modal - reset form first
        openAddModal() {
            this.resetForm();
            this.create_new = true;
        }
    },
    watch: {
        // Watch for edit_record changes to populate form
        edit_record: {
            handler(newVal) {
                if (newVal.id) {
                    this.form = {
                        title: newVal.title || '',
                        slug: newVal.slug || '',
                        category_id: newVal.category_id || '',
                        image: null,
                        image_url: newVal.image_url || '',
                        description: newVal.description || '',
                        meta_title: newVal.meta_title || '',
                        meta_keywords: newVal.meta_keywords || '',
                        meta_description: newVal.meta_description || '',
                        status: newVal.status
                    };
                    this.create_new = true;
                }
            },
            deep: true
        },
        
        // Watch for pagination changes
        currentPage() {
            this.getBlogs();
        },
        
        // Watch for per page changes
        perPage() {
            this.currentPage = 1; // Reset to first page when changing per page
            this.getBlogs();
        }

    }
}
</script>
