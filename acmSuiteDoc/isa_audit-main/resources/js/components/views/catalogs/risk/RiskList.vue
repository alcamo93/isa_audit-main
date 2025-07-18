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
              @successfully="getRiskCategories"
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
              <template #cell(risk_category)="data">
                <span> {{ data.item.risk_category }} </span>
              </template>
              <template #cell(actions)="data">
                <!-- button interpretation -->
                <modal-interpretation 
                  @successfully="getRiskCategories"
                  :register="data.item"
                />
                <!-- button helps -->
                <modal-help
                  @successfully="getRiskCategories"
                  :record="data.item"
                />
                <!-- button update -->
                <register-modal 
                  @successfully="getRiskCategories"
                  :is-new="false"
                  :register="data.item"
                />
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
import FilterArea from '../../components/slots/FilterArea'
import AppPaginator from '../../components/app-paginator/AppPaginator'
import Filters  from './Filters'
import RegisterModal from './Modal'
import ModalInterpretation from './ModalInterpretation'
import ModalHelp from './helper/ModalHelp'
import { getRiskCategories } from '../../../../services/riskService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Nivel de riesgo`
    this.getRiskCategories()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    ModalInterpretation,
    ModalHelp
  },
  data() {
    return {
      items: [],
      filters: {
        risk_category: null,
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
      this.getRiskCategories()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'risk_category',
          label: 'Categoría de Riesgo',
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
    async getRiskCategories() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRiskCategories(params, this.filters)
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
    async setFilters({ risk_category }) {
      this.filters.risk_category = risk_category
      await this.getRiskCategories()
    },
  }
}
</script>

<style></style>
