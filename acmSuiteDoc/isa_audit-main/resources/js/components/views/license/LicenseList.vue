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
              @successfully="getLicenses"
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
              <template #cell(name)="data">
                <span> {{ data.item.name }} </span>
              </template>
              <template #cell(quantity)="data">
                <b-badge v-for="item in data.item.quantity" :key="item.pivot.id"
                  pill variant="info" class="mr-2"
                >
                  {{ item.type }}: {{ item.pivot.quantity }}
                </b-badge>
              </template>
              <template #cell(period)="data">
                <b-badge pill variant="secondary">
                  {{ data.item.num_period }} {{ data.item.period.name }}
                </b-badge>
              </template>
              <template #cell(status)="data">
                <b-form-checkbox 
                  v-model="data.item.status_id" 
                  v-b-tooltip.hover.left
                  :title="labelStatus(data.item.status_id)"
                  :value="1"
                  :unchecked-value="2"
                  switch size="lg"
                  @change="changeStatus(data.item)"
                ></b-form-checkbox>
              </template>
              <template #cell(actions)="data">
                <!-- button update -->
                <register-modal 
                  @successfully="getLicenses"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-b-tooltip.hover.left
                  title="Eliminar Licencia"
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
import { getLicenses, deleteLicense, changeStatus} from '../../../services/licenseService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Licencia`
    this.getLicenses()
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
        name: null,
        status_id: null,
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
      this.getLicenses()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'name',
          label: 'Licencia',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'quantity',
          label: 'Usuarios',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'period',
          label: 'Vigencia',
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
    async getLicenses() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getLicenses(params, this.filters)
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
    async setFilters({ name, status_id }) {
      this.filters.name = name,
      this.filters.status_id = status_id,
      await this.getLicenses()
    },    
    async alertRemove({ id, name }) {
      try {
        const question = `¿Estás seguro de eliminar la licencia: '${name}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteLicense(id)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getLicenses()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    labelStatus(status) {
      return `${(status ? 'Desactivar' : 'Activar')} Lincencia`
    },
    async changeStatus({id}) {
      try {
        this.showLoadingMixin()
        const { data } = await changeStatus(id)
        this.showLoadingMixin()
        this.responseMixin(data)
        await this.getLicenses()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style></style>
