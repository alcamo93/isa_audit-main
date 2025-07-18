<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" :opened="true">
          <filters
            @filterSelected="setFilters"
          />
        </filter-area>
      </div>
      <b-card>
        <b-row>
            <b-col>
              <register-modal 
                @successfully="getCustomers"
                :is-new="true"
              />
            </b-col>
          </b-row>
        <b-row>
          <b-col>
            <b-table 
              responsive 
              striped 
              hover 
              show-empty
              empty-text="No hay registros que mostrar"
              :fields="headerTable" 
              :items="items"
            >
              <template #cell(logo)="data">
                <modal-image 
                  :record="data.item"
                  @successfully="getCustomers"
                />
              </template>
              <template #cell(cust_trademark)="data">
                <span> {{ data.item.cust_trademark }} </span>
              </template>
              <template #cell(actions)="data">
                <!-- button corporates -->
                <b-button
                  variant="link" 
                  class="go-to-process" 
                  v-b-tooltip.hover
                  title="Ir a Plantas"
                  @click="showCorporates(data.item)"
                >
                  <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
                   Mostrar Plantas
                </b-button>
                <!-- button update -->
                <register-modal 
                  @successfully="getCustomers"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-if="canDeleteCustomer(data.item)"
                  v-b-tooltip.hover.left
                  title="Eliminar cliente"
                  variant="danger"
                  class="btn-link"
                  @click="alertRemove(data.item)"
                >
                  <b-icon icon="x-lg" aria-hidden="true"></b-icon>
                </b-button>
              </template>
            </b-table>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <app-paginator
              :data-list="paginate"
              @pagination-data="changePaginate"
            />
          </b-col>
        </b-row>
      </b-card>
    </b-container>
  </fragment>
</template>

<script>
import FilterArea from '../components/slots/FilterArea'
import AppPaginator from '../components/app-paginator/AppPaginator'
import Filters  from './Filters'
import RegisterModal from './Modal'
import ModalImage from './ModalImage'
import { getCustomers, deleteCustomer } from '../../../services/customerService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Clientes`
    this.getCustomers()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    ModalImage,
  },
  data() {
    return {
      items: [],
      filters: {
        customer_name: null,
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
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'logo',
          label: 'Logo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'cust_trademark',
          label: 'Razón social',
          sortable: false,
        },
        {
          key: 'actions',
          label: 'Acciones',
          class: 'text-center td-actions',
          sortable: false,
        }
      ]
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
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    canDeleteCustomer({ owner }) {
      return !Boolean(owner)
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ customer_name }) {
      this.filters.customer_name = customer_name
      await this.getCustomers()
    },    
    async alertRemove({ id_customer, cust_trademark }) {
      try {
        const question = `¿Estás seguro de eliminar al cliente: '${cust_trademark}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteCustomer(id_customer)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getCustomers()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    showCorporates({ id_customer }) {
      window.location.href = `/v2/customer/${id_customer}/corporate/view`
    }
  }
}
</script>

<style></style>
