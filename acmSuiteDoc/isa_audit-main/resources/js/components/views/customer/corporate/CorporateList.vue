<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" :opened="true">
          <template v-slot:action>
            <b-button
              class="float-right mt-2 mr-2"
              variant="success"
              @click="goToCustomers"
            >
              Regresar
            </b-button>
          </template>
          <filters
            @filterSelected="setFilters"
          />
        </filter-area>
      </div>
      <b-card>
        <div>
          <register-modal 
            @successfully="getCorporates"
            :id-customer="idCustomer"
            :is-new="true"
          />
        </div>
        <div>
          <b-table 
            responsive 
            striped 
            hover 
            show-empty
            empty-text="No hay registros que mostrar"
            :fields="headerTable" 
            :items="items"
          >
            <template #cell(image)="data">
              <modal-image 
                :record="data.item"
                @successfully="getCorporates"
              />
            </template>
            <template #cell(corp_trademark)="data">
              <span> {{ data.item.corp_trademark }} </span>
            </template>
            <template #cell(corp_tradename)="data">
              <span> {{ data.item.corp_tradename }} </span>
            </template>
            <template #cell(rfc)="data">
              <span> {{ data.item.rfc }} </span>
            </template>
            <template #cell(status)="data">
              <b-badge 
                pill 
                :variant="data.item.status.color"
              >
                {{ data.item.status.status }}
              </b-badge>
            </template>
            <template #cell(industry)="data">
              <span> {{ data.item.industry.industry }} </span>
            </template>
            <template #cell(actions)="data">
              <!-- button contact -->
              <modal-contact
                @successfully="getCorporates"
                :id-customer="idCustomer"
                :id-corporate="data.item.id_corporate"
                :id-contact="data.item.contact?.id_contact ?? 0"
                :title="`Contacto de la Planta ${data.item.corp_tradename}`"
              />
              <!-- button address -->
              <modal-address
                @successfully="getCorporates"
                :id-customer="idCustomer"
                :id-corporate="data.item.id_corporate"
                :is-new="data.item.addresses.length == 0"
                :register="data.item"
              />
              <!-- button update -->
              <register-modal 
                @successfully="getCorporates"
                :id-customer="idCustomer"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left
                title="Eliminar Planta"
                variant="danger"
                class="btn-link"
                @click="alertRemove(data.item)"
              >
                <b-icon icon="x-lg" aria-hidden="true"></b-icon>
              </b-button>
            </template>
          </b-table>
        </div>
        <div>
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
        </div>
      </b-card>
    </b-container>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea.vue'
import AppPaginator from '../../components/app-paginator/AppPaginator.vue'
import Filters  from './Filters.vue'
import RegisterModal from './Modal.vue'
import ModalContact  from './ModalContact.vue'
import ModalAddress from './ModalAddress.vue'
import ModalImage from './ModalImage.vue'
import { getCorporates, deleteCorporate } from '../../../../services/corporateService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Plantas`
    this.getCorporates()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    ModalContact,
    ModalAddress,
    ModalImage,
  },
  props: {
    idCustomer: {
      type: Number,
      required: true,
    }
  },
  data() {
    return {
      items: [],
      filters: {
        corporate_name: null,
        id_status: null,
        type: null,
        id_industry: null,
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
      this.getCorporates()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'image',
          label: 'Logo',
          sortable: false,
        },
        {
          key: 'corp_trademark',
          label: 'Razón social',
          sortable: false,
        },
        {
          key: 'corp_tradename',
          label: 'Planta',
          sortable: false,
        },
        {
          key: 'rfc',
          label: 'RFC',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estatus',
          sortable: false,
        },
        {
          key: 'industry',
          label: 'Giro',
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
    async getCorporates() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getCorporates(this.idCustomer, params, this.filters)
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
    async setFilters({ corporate_name, id_status, type, id_industry }) {
      this.filters.corporate_name = corporate_name
      this.filters.id_status = id_status
      this.filters.type = type
      this.filters.id_industry = id_industry
      await this.getCorporates()
    },
    async alertRemove({ id_corporate, corp_trademark }) {
      try {
        const question = `¿Estás seguro de eliminar la planta: '${corp_trademark}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteCorporate(this.idCustomer, id_corporate)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getCorporates()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    goToCustomers() {
      window.location.href = '/v2/customer/view'
    },
  }
}
</script>

<style></style>
