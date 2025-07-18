<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="book" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <filters 
          :id-form="idForm"
          :id-requirement="idRequirement"
          :record="record"
          :init="dialog"
          @fieldSelected="setFilters"
        />
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
                <b-button
                  v-b-tooltip.hover.left
                  :title="hasRelation('message', data.item)"
                  :variant="hasRelation('color', data.item)"
                  class="btn-link"
                  @click="setLegal(data.item)"
                >
                  <b-icon :icon="hasRelation('icon', data.item)" aria-hidden="true"></b-icon>
                </b-button>
              </template>
            </b-table>
             <!-- Paginator -->
            <app-paginator
              :data-list="paginate"
              @pagination-data="changePaginate"
            />
            <!-- End Paginator -->
          </b-col>
        </b-row>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import AppPaginator from '../../../../../components/app-paginator/AppPaginator'
import ModalLegalQuote from '../../../../guideline/legal_basi/ModalLegalQuote'
import Filters from './Filters'
import { getRelationArticles, setRelationLegal } from '../../../../../../../services/subrequirementLegalsService'

export default {
  components: {
    AppPaginator,
    ModalLegalQuote,
    Filters
  },
  props: {
    idForm: {
      type: Number,
      required: true,
    },
    idRequirement: {
      type: Number,
      required: true,
    },
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        id_guideline: null,
        legal_basis: null,
        legal_quote: null,
        has_relation: null,
        id_legal_classification: null
      },
      items: []
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      return `Fundamentos legales para: ${this.record.no_subrequirement}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Selección de fundamentos legales para: ${this.record.no_subrequirement}`
    },
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
          class: 'text-justify',
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
  watch: {
    'paginate.page': function() {
      this.getArticles()
    },
  },
  methods: {
    async showModal() {
      await this.getArticles()
      this.dialog = true
    },
    async getArticles() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRelationArticles(this.idForm, this.idRequirement, this.record.id_subrequirement, params, this.filters)
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
    async setFilters({ id_guideline, legal_basis, legal_quote, has_relation, id_legal_classification }) {
      this.filters.id_guideline = id_guideline
      this.filters.legal_basis = legal_basis
      this.filters.legal_quote = legal_quote
      this.filters.has_relation = has_relation
      this.filters.id_legal_classification = id_legal_classification
      await this.getArticles()
    },
    hasRelation(type, {subrequirements}) {
      if (type == 'message') {
        return Boolean(subrequirements.length) ? 'Remover relación de articulo con requerimiento' : 'Relacionar artículo con requerimiento'
      }
      if (type == 'icon') {
        return Boolean(subrequirements.length) ? 'patch-check-fill' : 'patch-minus-fill'
      }
      if (type == 'color') {
        return Boolean(subrequirements.length) ? 'success' : 'warning'
      }
    },
    async setLegal({id_legal_basis}) {
      try {
        this.showLoadingMixin()
        const { data } = await setRelationLegal(this.idForm, this.idRequirement, this.record.id_subrequirement, id_legal_basis)
        await this.getArticles()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style>

</style>