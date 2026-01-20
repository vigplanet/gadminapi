<template>
    <div>
        <div class="page-heading">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{__('product_requests')}}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">{{__('dashboard')}}</router-link>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('product_requests')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 order-md-1 order-last">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{__('product_requests')}}</h4>
                        </div>
                        <div class="card-body">
                        <b-row class="mb-2">
                            <b-col md="3">
                                <h6 class="box-title">{{ __('filter_by_status') }}</h6>
                                <select v-model="statusFilter" class="form-control form-select" @change="getRequests()">
                                    <option value="">{{__('all_statuses')}}</option>
                                    <option value="pending">{{__('pending')}}</option>
                                    <option value="accepted">{{__('accepted')}}</option>
                                    <option value="rejected">{{__('rejected')}}</option>
                                </select>
                            </b-col>
                            <b-col md="3" offset-md="5">
                                <h6 class="box-title">{{ __('search') }}</h6>
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="Search"
                                ></b-form-input>
                            </b-col>
                            <b-col md="1" class="text-center">
                                <button class="btn btn-primary btn_refresh" v-b-tooltip.hover :title="__('refresh')" @click="getRequests()">
                                    <i class="fa fa-refresh" aria-hidden="true"></i>
                                </button>
                            </b-col>
                        </b-row>
                        <div class="table-responsive">
                            <b-table
                                :items="filteredRequests"
                                :fields="fields"
                                :current-page="currentPage"
                                :per-page="perPage"
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
                                <template #cell(customer_name)="row">
                                    <span v-if="row.item.customer">{{ row.item.customer.name }}</span>
                                    <span v-else class="text-muted">-</span>
                                </template>
                                <template #cell(description)="row">
                                    <div v-if="row.item.description" class="text-truncate" style="max-width: 200px;">
                                        {{ row.item.description }}
                                    </div>
                                    <span v-else class="text-muted">-</span>
                                </template>
                                <template #cell(image)="row">
                                    <img v-if="row.item.image_url" :src="row.item.image_url" 
                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;"
                                         @click="openImageModal(row.item.image_url)">
                                    <span v-else class="text-muted">-</span>
                                </template>
                                <template #cell(status)="row">
                                    <span class="badge" :class="getStatusVariant(row.item.status)">
                                        {{ getStatusText(row.item.status) }}
                                    </span>
                                </template>
                                <template #cell(product)="row">
                                    <span v-if="row.item.product">{{ row.item.product.name }}</span>
                                    <span v-else class="text-muted">-</span>
                                </template>
                                <template #cell(action)="row">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info" title="View" @click="viewRequestDetails(row.item)">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <class v-if="$can('product_request_update')">
                                        <button v-if="row.item.status === 'pending'" 
                                                class="btn btn-sm btn-success ml-1" title="Accept"
                                                @click="acceptRequest(row.item)">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button v-if="row.item.status === 'pending'" title="Reject"
                                                class="btn btn-sm btn-danger" 
                                                @click="rejectRequest(row.item)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        </class>
                                    </div>
                                </template>
                            </b-table>
                        </div>
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
                            <b-col md="4" class="my-1" offset-md="6">
                                <label>{{__('total_records')}}:- {{ totalRows }}</label>
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

        <!-- Request Details Modal -->
        <b-modal v-model="showDetailsModal" size="lg" :title="__('request_details')">
            <div v-if="selectedRequest">
                <div class="row">
                    <div class="col-md-6">
                        <h6>{{__('request_information')}}</h6>
                        <p><strong>{{__('product_request_status')}}:</strong> 
                            <span class="badge" :class="getStatusVariant(selectedRequest.status)">
                                {{ getStatusText(selectedRequest.status) }}
                            </span>
                        </p>
                        <p><strong>{{__('date')}}:</strong> {{ formatDate(selectedRequest.created_at) }}</p>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>{{__('product_request_description')}}</h6>
                        <p v-if="selectedRequest.description">{{ selectedRequest.description }}</p>
                        <p v-else class="text-muted">No description provided</p>
                    </div>
                </div>

                <div class="row mt-3" v-if="selectedRequest.image_url">
                    <div class="col-12">
                        <h6>{{__('product_request_image')}}</h6>
                        <img :src="selectedRequest.image_url" class="img-fluid" style="max-width: 300px;">
                    </div>
                </div>

                <div class="row mt-3" v-if="selectedRequest.product">
                    <div class="col-12">
                        <h6>{{__('requested_product')}}</h6>
                        <p><strong>{{__('name')}}:</strong> {{ selectedRequest.product.name }}</p>
                    </div>
                </div>
            </div>
        </b-modal>

        <!-- Accept Request Modal -->
        <b-modal v-model="showAcceptModal" size="lg" :title="__('accept_request')">
            <span class="text-danger">{{ __('add_product_before_accepting_request') }}</span>
            <div v-if="selectedRequest">
                <div class="form-group">
                    <label>{{__('select_product')}} <span class="text-danger">*</span></label>
                    <multiselect v-model="selectedProduct"
                                 :options="productOptions"
                                 :placeholder="__('select_and_search_products')"
                                 label="name"
                                 track-by="id"
                                 required>
                        <template slot="singleLabel" slot-scope="props">
                            <span class="option__desc">
                                <span class="option__title">{{ props.option.name }}</span>
                            </span>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="option__desc">
                                <span class="option__title">{{ props.option.name }}</span>
                            </div>
                        </template>
                    </multiselect>
                </div>
            </div>
            <template #modal-footer>
                <b-button variant="secondary" @click="showAcceptModal = false">{{__('cancel')}}</b-button>
                <b-button variant="success" @click="confirmAcceptRequest" :disabled="!selectedProduct">
                    {{__('accept_request')}}
                </b-button>
            </template>
        </b-modal>

        <!-- Reject Request Modal -->
        <b-modal v-model="showRejectModal" :title="__('reject_request')">
            <div v-if="selectedRequest">
                <div class="form-group">
                    <label>{{__('admin_notes')}} <span class="text-danger">*</span></label>
                    <b-form-textarea v-model="adminNotes" :placeholder="__('enter_admin_notes')" rows="3" required></b-form-textarea>
                </div>
            </div>
            <template #modal-footer>
                <b-button variant="secondary" @click="showRejectModal = false">{{__('cancel')}}</b-button>
                <b-button variant="danger" @click="confirmRejectRequest" :disabled="!adminNotes">
                    {{__('reject_request')}}
                </b-button>
            </template>
        </b-modal>

        <!-- Image Modal -->
        <b-modal v-model="showImage" size="lg" :title="__('product_request_image')">
            <div class="text-center">
                <img :src="modalImageUrl" class="img-fluid">
            </div>
        </b-modal>
    </div>
</template>

<script>
import axios from 'axios';
import Multiselect from 'vue-multiselect';

export default {
    components: {
        Multiselect
    },
    data() {
        return {
            fields: [
                { key: 'customer_name', label: __('customer_name'), class: 'text-center' },
                { key: 'description', label: __('product_request_description'), class: 'text-center' },
                { key: 'image', label: __('product_request_image'), class: 'text-center' },
                { key: 'status', label: __('product_request_status'), sortable: true, class: 'text-center' },
                { key: 'product', label: __('requested_product'), class: 'text-center' },
                { key: 'action', label: __('action'), class: 'text-center' }
            ],
            requests: [],
            isLoading: false,
            currentPage: 1,
            perPage: this.$perPage || 10,
            pageOptions: this.$pageOptions || [5, 10, 15, 20, 25, 50],
            totalRows: 0,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
            statusFilter: '',
            
            // Modals
            showDetailsModal: false,
            showImage: false,
            showAcceptModal: false,
            showRejectModal: false,
            
            // Selected data
            selectedRequest: null,
            modalImageUrl: '',
            selectedProduct: null, // Changed from selectedProductId to selectedProduct (object)
            adminNotes: '',
            productOptions: [], // Array of product objects with name and id
            allProducts: [],
            productsPage: 1,
            productsPerPage: 50,
            productsTotal: 0,
            isLoadingProducts: false
        }
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        },
        filteredRequests() {
            if (!this.statusFilter) {
                return this.requests;
            }
            return this.requests.filter(request => request.status === this.statusFilter);
        }
    },
    watch: {
        statusFilter() {
            this.totalRows = this.filteredRequests.length;
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.requests.length
    },
    created() {
        this.getRequests();
        this.getProducts();
    },
    methods: {
        getRequests() {
            this.isLoading = true;
            
            axios.get(this.$apiUrl + '/user_product_requests')
                .then((response) => {
                    this.isLoading = false;
                    if (response.data.status === 1) {
                        this.requests = response.data.data.data;
                        this.totalRows = this.requests.length;
                    } else {
                        this.showMessage("error", response.data.message);
                    }
                })
                .catch(() => {
                    this.isLoading = false;
                    this.showMessage("error", __('something_went_wrong'));
                });
        },

        viewRequestDetails(request) {
            this.selectedRequest = request;
            this.showDetailsModal = true;
        },

        getProducts() {
            this.isLoadingProducts = true;
            const params = {
                page: this.productsPage,
                per_page: this.productsPerPage
            };

            axios.get(this.$apiUrl + '/products/get_product_variants', { params })
                .then((response) => {
                    this.isLoadingProducts = false;
                    if (response.data.status === 1) {
                        // Add new products to existing array
                        this.allProducts = [...this.allProducts, ...response.data.data];
                        this.productsTotal = response.data.total;
                        
                        // Update dropdown options
                        this.updateProductOptions();
                        
                        // If there are more products to load, load next page
                        if (this.allProducts.length < this.productsTotal) {
                            this.productsPage++;
                            this.getProducts();
                        }
                    }
                })
                .catch((error) => {
                    this.isLoadingProducts = false;
                    console.error('Error fetching products:', error);
                });
        },

        updateProductOptions() {
            // Transform products for multiselect component
            // Each product needs id and name properties
            this.productOptions = this.allProducts.map(product => ({
                id: product.id,
                name: `${product.name} (${product.seller_name})`
            }));
        },

        acceptRequest(request) {
            this.selectedRequest = request;
            this.selectedProduct = null; // Reset selected product when opening modal
            this.showAcceptModal = true;
        },

        rejectRequest(request) {
            this.selectedRequest = request;
            this.adminNotes = '';
            this.showRejectModal = true;
        },

        confirmAcceptRequest() {
            if (!this.selectedProduct || !this.selectedProduct.id) {
                this.showMessage("error", __('select_product'));
                return;
            }

            this.updateRequestStatus('accepted');
        },

        confirmRejectRequest() {
            if (!this.adminNotes) {
                this.showMessage("error", __('enter_admin_notes'));
                return;
            }

            this.updateRequestStatus('rejected');
        },

        updateRequestStatus(status) {
            const data = {
                id: this.selectedRequest.id,
                status: status
            };

            if (status === 'accepted') {
                data.product_id = this.selectedProduct.id;
            }

            if (status === 'rejected') {
                data.admin_notes = this.adminNotes;
            }

            axios.post(this.$apiUrl + '/user_product_requests/update-status', data)
                .then((response) => {
                    if (response.data.status === 1) {
                        this.showMessage("success", response.data.message);
                        this.showAcceptModal = false;
                        this.showRejectModal = false;
                        this.getRequests();
                    } else {
                        this.showMessage("error", response.data.message);
                    }
                })
                .catch((error) => {
                    this.showMessage("error", __('something_went_wrong'));
                });
        },

        openImageModal(imageUrl) {
            this.modalImageUrl = imageUrl;
            this.showImage = true;
        },

        getStatusVariant(status) {
            switch (status) {
                case 'pending':
                    return 'bg-warning';
                case 'accepted':
                    return 'bg-success';
                case 'rejected':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        },

        getStatusText(status) {
            switch (status) {
                case 'pending':
                    return __('pending');
                case 'accepted':
                    return __('accepted');
                case 'rejected':
                    return __('rejected');
                default:
                    return status;
            }
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString();
        }
    }
}
</script>

<style scoped>
@import "../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css";
</style>
