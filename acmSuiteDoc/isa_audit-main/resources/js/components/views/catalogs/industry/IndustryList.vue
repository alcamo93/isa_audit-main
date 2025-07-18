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
                @successfully="getIndustries"
                :is-new="true"
              ></register-modal>
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
              <template #cell(industry)="data">
                <span> {{ data.item.industry }} </span>
              </template>
              <template #cell(actions)="data">
                <!-- button update -->
                <register-modal 
                  @successfully="getIndustries"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-b-tooltip.hover.left
                  title="Eliminar giro"
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
import FilterArea from '../../components/slots/FilterArea'
import AppPaginator from '../../components/app-paginator/AppPaginator'
import Filters  from './Filters'
import RegisterModal from './Modal'
import { getIndustries, deleteIndustry } from '../../../../services/industryService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Giros`
    this.getIndustries()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
  },
  data() {
    return {
      items: [],
      filters: {
        industry: null,
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
      this.getIndustries()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'industry',
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
    async getIndustries() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getIndustries(params, this.filters)
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
    async setFilters({ industry }) {
      this.filters.industry = industry
      await this.getIndustries()
    },    
    async alertRemove({ id_industry, industry }) {
      try {
        const question = `¿Estas seguro de eliminar el giro: '${industry}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteIndustry(id_industry)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getIndustries()
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
