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
      <b-icon icon="question-circle-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <filters 
          :record="record"
          :init="dialog"
          @fieldSelected="setFilters"
        />
        <b-row>
          <b-col>
            <register-modal 
              @successfully="getRiskHelps"
              :id-risk-category="record.id_risk_category"
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
              <template #cell(attribute)="data">
                {{ data.item.attribute.risk_attribute }}
              </template>
              <template #cell(risk_help)="data">
                {{ data.item.risk_help }}
              </template>
              <template #cell(standard)="data">
                {{ data.item.standard  }}
              </template>
              <template #cell(value)="data">
                <b-badge variant="secondary">
                  {{ data.item.value }}
                </b-badge>
              </template>
              <template #cell(actions)="data">
                <register-modal 
                  @successfully="getRiskHelps"
                  :is-new="false"
                  :id-risk-category="record.id_risk_category"
                  :record="data.item"
                />
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
import AppPaginator from '../../../components/app-paginator/AppPaginator'
import Filters from './Filters'
import RegisterModal from './ModalHelpForm'
import { getRiskHelps } from '../../../../../services/riskService';

export default {
  components: {
    AppPaginator,
    Filters,
    RegisterModal
  },
  props: {
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
        risk_help: null,
        standard: null,
        id_risk_attribute: null,
      },
      items: []
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      return `Valoraciones para: ${this.record.risk_category}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Mostrar valoraciones para: ${this.record.risk_category}`
    },
    headerTable() {
      return [
        {
          key: 'attribute',
          label: 'Atributo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'risk_help',
          label: 'Nombre',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'standard',
          label: 'Criterio',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'value',
          label: 'Valor',
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
      this.getRiskHelps()
    },
  },
  methods: {
    async showModal() {
      await this.getRiskHelps()
      this.dialog = true
    },
    async getRiskHelps() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRiskHelps(this.record.id_risk_category, params, this.filters)
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
    async setFilters({ risk_help, standard, id_risk_attribute }) {
      this.filters.risk_help = risk_help
      this.filters.standard = standard
      this.filters.id_risk_attribute = id_risk_attribute
      await this.getRiskHelps()
    },
  }
}
</script>

<style>

</style>