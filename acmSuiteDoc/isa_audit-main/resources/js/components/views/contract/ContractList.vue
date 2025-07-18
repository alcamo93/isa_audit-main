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
              @successfully="getContracts"
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
              <template #cell(corp_tradename)="data">
                <span> {{ data.item.corporate.corp_tradename }} </span>
              </template>
              <template #cell(contract)="data">
                <span> {{ data.item.contract }} </span>
              </template>
              <template #cell(in_range_date)="data">
                <b-badge pill :variant="data.item.in_range_date ? 'success' : 'danger'">
                  {{ data.item.start_date_format }} - {{ data.item.end_date_format }}
                </b-badge>
              </template>
              <template #cell(status)="data">
                <b-form-checkbox 
                  v-model="data.item.id_status" 
                  v-b-tooltip.hover.left
                  :title="labelStatus(data.item.id_status)"
                  :value="1"
                  :unchecked-value="2"
                  switch size="lg"
                  @change="changeStatus(data.item)"
                ></b-form-checkbox>
              </template>
              <template #cell(actions)="data">
                <!-- button contact -->
                <modal-contact
                  @successfully="getContracts"
                  :id-customer="data.item.id_customer"
                  :id-corporate="data.item.id_corporate"
                  :id-contact="data.item.corporate.contact?.id_contact ?? 0"
                  :title="`Datos de contacto de la Planta ${data.item.corporate.corp_tradename}`"
                />
                <!-- button password -->
                <modal-historical 
                  :register="data.item"
                />
                <!-- button update -->
                <register-modal 
                  @successfully="getContracts"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-b-tooltip.hover.left
                  title="Eliminar Contrato"
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
import FilterArea from '../components/slots/FilterArea.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import Filters  from './Filters.vue'
import RegisterModal from './Modal.vue'
import ModalHistorical from './ModalHistorical.vue'
import ModalContact  from '../customer/corporate/ModalContact.vue'
import { getContracts, deleteContract, changeStatus } from '../../../services/contractService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Contratos`
    this.getContracts()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    ModalHistorical,
    ModalContact,
  },
  data() {
    return {
      items: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        contract: null,
        id_status: null,
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
      this.getContracts()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'corp_tradename',
          label: 'Planta',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'contract',
          label: 'Contrato',
          sortable: false,
        },
        {
          key: 'in_range_date',
          label: 'Duración por Fecha',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estatus',
          class: 'text-center',
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
    async getContracts() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getContracts(params, this.filters)
        this.items = data.data
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
    async setFilters({ id_customer, id_corporate, contract, id_status }) {
      this.filters.id_customer = id_customer,
      this.filters.id_corporate = id_corporate,
      this.filters.contract = contract,
      this.filters.id_status = id_status,
      await this.getContracts()
    },
    labelStatus(status) {
      return `${(status ? 'Desactivar' : 'Activar')} Contrato`
    },
    async changeStatus({id_contract}) {
      try {
        this.showLoadingMixin()
        const { data } = await changeStatus(id_contract)
        this.showLoadingMixin()
        this.responseMixin(data)
        await this.getContracts()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async alertRemove({ id_contract, contract }) {
      try {
        const question = `¿Estás seguro de eliminar el contrato: '${contract}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteContract(id_contract)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getContracts()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style></style>
