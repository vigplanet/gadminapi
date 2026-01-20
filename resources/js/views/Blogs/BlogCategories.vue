<template>
    <div>
        <div class="page-heading">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ __('blog_categories') }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><router-link to="/dashboard">{{ __('dashboard') }}</router-link></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('blog_categories') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 order-md-1 order-last">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('blog_categories') }}</h4>
                            <span class="pull-right">
                                <button class="btn btn-primary" @click="create_new=true" v-b-tooltip.hover :title="__('add_new_category')" v-if="$can('blog_category_create')">{{ __('add_category') }}</button>
                            </span>
                        </div>
                        <div class="card-body">
                            <b-row class="mb-2">
                                <b-col md="3" offset-md="8">
                                    <h6 class="box-title">{{ __('search') }}</h6>
                                    <b-form-input
                                        id="filter-input"
                                        v-model="filter"
                                        type="search"
                                        :placeholder="__('search')"
                                    ></b-form-input>
                                </b-col>
                                <b-col md="1" class="text-center">
                                    <button class="btn btn-primary btn_refresh" v-b-tooltip.hover :title="__('refresh')" @click="getBlogCategories()">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </button>
                                </b-col>
                            </b-row>
                            <b-table
                                :items="categories"
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

                                <template #cell(status)="row">
                                    <span class='badge bg-success' v-if="row.item.status == 1">{{ __('active') }}</span>
                                    <span class='badge bg-danger' v-if="row.item.status == 0">{{ __('deactive') }}</span>
                                </template>

                                <template #cell(blogs_count)="row">
                                    <span class="badge bg-info">{{ row.item.active_blogs_count || 0 }}</span>
                                </template>

                                <template #cell(actions)="row">
                                    <button class="btn btn-sm btn-primary" @click="edit_record = row.item" v-if="$can('blog_category_update')" v-b-tooltip.hover :title="__('edit')"><i class="fa fa-pencil-alt"></i></button>
                                    <button class="btn btn-sm btn-danger" @click="deleteCategory(row.index,row.item.id)" v-if="$can('blog_category_delete')" v-b-tooltip.hover :title="__('delete')"><i class="fa fa-trash"></i></button>
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
                                    <label>{{__('total_records')}}:- {{ totalRows }}</label>,
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

        <!-- Create/Edit Category Modal -->
        <b-modal v-model="create_new" :title="edit_record.id ? __('edit_category') : __('add_category')" size="lg" :hide-footer="true">
            <form @submit.prevent="saveCategory">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('category_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" v-model="form.name" :placeholder="__('enter_category_name')" required @keyup="createSlug">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">{{ __('slug') }}</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control" id="slug" v-model="form.slug" :placeholder="__('enter_slug')" required>
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

                <div class="row">
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
import axios from 'axios';

export default {
    name: 'BlogCategories',
    data() {
        return {
            categories: [],
            create_new: false,
            edit_record: {},
            form: {
                name: '',
                slug: '',
                meta_title: '',
                meta_keywords: '',
                meta_description: '',
                status: 1
            },
            isLoading: false,
            isSubmitting: false,
            filter: '',
            filterOn: ['name', 'slug'],
            sortBy: 'id',
            sortDesc: true,
            sortDirection: 'desc',
            fields: [
                { key: 'id', label: __('id'), sortable: true, class: 'text-center' },
                { key: 'name', label: __('name'), sortable: true, class: 'text-center' },
                { key: 'slug', label: __('slug'), class: 'text-center' },
                { key: 'blogs_count', label: __('blogs_count'), class: 'text-center' },
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
        this.getBlogCategories();
    },
    methods: {
        async getBlogCategories() {
            this.isLoading = true;
            try {
                const params = {
                    offset: (this.currentPage - 1) * this.perPage,
                    limit: this.perPage,
                    include_inactive: 1
                };

                const response = await axios.get(this.$apiUrl + '/blog_categories', { params });
                if (response.data.status === 1) {
                    this.categories = response.data.data;
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

        createSlug() {
            if (this.form.name !== "") {
                let slug = this.form.name.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                this.form.slug = slug;
            }
        },

        async saveCategory() {
            this.isSubmitting = true;
            try {
                let response;
                if (this.edit_record.id) {
                    response = await axios.post(this.$apiUrl + `/blog_categories/update/${this.edit_record.id}`, this.form);
                } else {
                    response = await axios.post(this.$apiUrl + '/blog_categories/save', this.form);
                }

                if (response.data.status === 1) {
                    this.showMessage("success", response.data.message);
                    this.create_new = false;
                    this.resetForm();
                    this.getBlogCategories();
                } else {
                    this.showMessage("error", response.data.message);
                }
            } catch (error) {
                this.showMessage("error", __('something_went_wrong'));
            } finally {
                this.isSubmitting = false;
            }
        },

        deleteCategory(index, id) {
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
                    axios.post(this.$apiUrl + `/blog_categories/delete/${id}`)
                        .then((response) => {
                            this.isLoading = false;
                            if (response.data.status === 1) {
                                this.showMessage('success', response.data.message);
                                this.getBlogCategories();
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
                name: '',
                slug: '',
                meta_title: '',
                meta_keywords: '',
                meta_description: '',
                status: 1
            };
            this.edit_record = {};
        },

        createSlug() {
            if (this.form.name !== "") {
                let slug = this.form.name.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                this.form.slug = slug;
            }
        }
    },
    watch: {
        edit_record: {
            handler(newVal) {
                if (newVal.id) {
                    this.form = {
                        name: newVal.name || '',
                        slug: newVal.slug || '',
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
        
        currentPage() {
            this.getBlogCategories();
        },
        
        perPage() {
            this.currentPage = 1;
            this.getBlogCategories();
        }
    }
}
</script>
