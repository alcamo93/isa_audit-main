<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area
          class="flex-fill"
          title="Filtros" 
          :opened="true"
        >          
          <label>Cliente</label>
          <b-form-input
            v-model="filters.customer_name"
            placeholder="Búsqueda por nombre"
            debounce="500"
          ></b-form-input>
        </filter-area>
      </div>
      <div class="d-flex flex-wrap justify-content-sm-center justify-content-md-around justify-content-lg-start justify-content-xl-start" 
        v-if="customers.length"
      >
        <div class="flex-fill flex-sm-fill flex-md-grow-0 flex-lg-grow-0 m-0 flex-xl-grow-0 m-1"
          v-for="customer in customers"
          :key="customer.id_customer"
          v-b-tooltip.hover.left
          :title="customer.cust_tradename_format"     
        >
          <b-card class="text-center">
            <template #header>
              <image-item
                :item="customer"
                type="customer"
              />
            </template>
            {{ customer.cust_tradename_format }}
            <template #footer>
              <b-button
                variant="primary"
                size="sm" 
                @click="showCustomer(customer.id_customer)"
              >
                Mostrar información
                <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon>
              </b-button>
            </template>
          </b-card>
        </div>
      </div>
      <div class="d-flex" v-else>
        <b-card class="flex-fill text-center">
          <h5>No se cuenta con evaluaciones de cumplimiento legal</h5>
        </b-card>
      </div>
      <b-row>
        <b-col>
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
        </b-col>
      </b-row>
    </b-container>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import AppPaginator from '../../components/app-paginator/AppPaginator'
import ImageItem from '../../components/customers/ImageItem'
import { getCustomers } from '../../../../services/dashboardService'

export default {
  components: {
    FilterArea,
    AppPaginator,
    ImageItem
  },
  mounted() {
    this.getCustomers()
  },
  data() {
    return {
      loading: false,
      owner: false,
      profileLevel: null,
      id_customer: null,
      corporateId: null,
      views: [],
      customers: [],
      corporates: [],
      corporate: [],
      filters: {
        customer_name: null
      },
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getCustomers()
    },
    filters: {
      handler() {
        this.getCustomers()
      },
      deep: true
    }
  },
  methods: {
    async getCustomers() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getCustomers(params, this.filters)
        this.customers = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    showCustomer(idCustomer) {
      window.location.href = `/v2/dashboard/customers/${idCustomer}/view`
    }
  },
}
</script>

<style scoped>

.text-footer {
  font-size: 12px;
  font-weight: 600;
}
.dashboard-card {
  box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px;
  width: 100%;
  height: 280px;
}
.card-title{
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 12px;
}
.custom-text {
  font-size: 12px;
}
.p-card {
  margin: 0;
}
.dashboard-card:hover {
  -webkit-transform: scale(1.05);
  transform: scale(1.05);
  filter: saturate(150%);
  transition-duration: 250ms;
  box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
}
.custom-color {
  color: #4E84F3;
}
.redirect-text{
  font-size: 14px;
}
</style>