<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <template v-slot:action>
          <b-button
            class="float-right mt-2 mr-2"
            variant="success"
            @click="goToGuidelines"
          >
            Regresar
          </b-button>
        </template>
        <filters
          :id-guideline="idGuideline"
          @fieldSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <b-row>
            <b-col>
              <register-modal 
                @successfully="getLegalBasis"
                :id-guideline="idGuideline"
                :is-new="true"
              />
            </b-col>
          </b-row>
        </b-card-text>
        <b-card-text>
          <b-table 
            responsive 
            striped 
            hover 
            show-empty
            empty-text="No hay registros que mostrar"
            :fields="headerTable" 
            :items="items"
          >
            <template #cell(order)="data">
              {{ data.item.order }}
            </template>
            <template #cell(legal_basis)="data">
              {{ data.item.legal_basis }}
            </template>
            <template #cell(legal_quote)="data">
              <modal-legal-quote 
                :record="data.item"
              />
            </template>
            <template #cell(actions)="data">
              <!-- button update -->
              <register-modal 
                @successfully="getLegalBasis"
                :id-guideline="idGuideline"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left
                title="Eliminar Artículo"
                variant="danger"
                class="btn-link"
                @click="alertRemove(data.item)"
              >
                <b-icon icon="x-lg" aria-hidden="true"></b-icon>
              </b-button>
            </template>
          </b-table>
          <!-- Paginator -->
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
          <!-- End Paginator -->
        </b-card-text>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import FilterArea from '../../../components/slots/FilterArea'
import Filters from './Filters'
import AppPaginator from '../../../components/app-paginator/AppPaginator'
import RegisterModal from './Modal'
import ModalLegalQuote from './ModalLegalQuote'
import { getLegalBasis, deleteLegalBasi } from '../../../../../services/legalBasiService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Fundamentos Legales`
    this.getLegalBasis()
  },
  props: {
    idGuideline: {
      type: Number,
      required: true
    }
  },
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    RegisterModal,
    ModalLegalQuote,
  },
  data() {
    return {
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        legal_basis: null,
        legal_quote: null,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getLegalBasis()
    },
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'order',
          label: 'Orden',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'legal_basis',
          label: 'Artículo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'legal_quote',
          label: 'Cita Legal',
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
    },
  },
  methods: {
    async getLegalBasis() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getLegalBasis(this.idGuideline, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    changePaginate(paginateData) {
      const { perPage, page } = paginateData
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async alertRemove(item) {
      try {
        const { id_legal_basis, legal_basis } = item
        const question = `¿Estás seguro de eliminar: "${legal_basis}"?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteLegalBasi(this.idGuideline, id_legal_basis)
          this.responseMixin(data)
          await this.getLegalBasis()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setFilters({ legal_basis, legal_quote }) {
      this.filters.legal_basis = legal_basis
      this.filters.legal_quote = legal_quote
      await this.getLegalBasis()
    },
    goToGuidelines() {
      window.location.href = '/v2/catalogs/guideline/view'
    },
  }
}
</script>

<style>

</style>