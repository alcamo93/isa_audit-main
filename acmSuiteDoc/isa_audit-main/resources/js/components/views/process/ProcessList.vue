<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area opened>
        <filters
          @filterSelected="setFilters"
          :special-filter="show_special_filter"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <div>
            <register-modal 
              @successfully="getProcess"
              is-new
            />
          </div>
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
            <template #cell(audit_processes)="data">
              <span> {{ data.item.audit_processes }} </span>
            </template>
            <template #cell(corporate)="data">
              <span> {{ data.item.corporate.corp_tradename }} </span>
            </template>
            <template #cell(scope)="data">
              <span v-if="data.item.id_scope == 2"> 
                {{ `${data.item.scope.scope}: ${data.item.specification_scope}` }} 
              </span>
              <span v-else> {{ data.item.scope.scope }} </span>
            </template>
            <!-- dates -->
            <template #cell(dates_format)="data">
              <b-badge 
                pill :variant="data.item.is_in_current_year ? 'success' : 'danger'">
                {{ data.item.dates_format }}
              </b-badge>
            </template>
            <!-- kpi -->
            <template #cell(use_kpi)="data">
              <b-badge 
                pill :variant="data.item.use_kpi ? 'success' : 'secondary'">
                {{ data.item.use_kpi ? 'KPI' : '---' }}
              </b-badge>
            </template>
            <!-- sections -->
            <template #cell(sections)="data">
              <sections-modal
                :record="data.item"
              />
            </template>
            <template #cell(actions)="data">
              <!-- button renewal -->
              <renewal-modal 
                :id="data.item.id_audit_processes" 
              />
              <!-- button update -->
              <register-modal 
                v-show="canUpdateProcess(data.item)"
                @successfully="getProcess"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-show="canDeleteProcess(data.item)"
                variant="danger"
                v-b-tooltip.hover.left
                title="Eliminar Evaluación"
                class="btn-link"
                @click="alertRemove(data.item)"
              >
                <b-icon icon="x-lg" aria-hidden="true"></b-icon> 
              </b-button>
              <!-- button show -->
              <details-modal
                :id="data.item.id_audit_processes"
              />
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
import FilterArea from '../components/slots/FilterArea.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import Filters  from './Filters.vue'
import RegisterModal from './Modal.vue'
import DetailsModal from './details/Details.vue'
import SectionsModal from './Sections.vue'
import RenewalModal from './renewal/RenewalModal.vue'
import { getListProcess, deleteProcess } from '../../../services/processService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Aplicabilidad/Auditoría/Cumplimiento legal`
    this.getProcess()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    DetailsModal,
    SectionsModal,
    RenewalModal,
  },
  data() {
    return {
      show_special_filter: false,
      items: [],
      filters: {
        audit_processes: '',
        id_customer: null,
        id_corporate: null,
        id_scope: null,
        date: null,
        evaluation_type_id: null,
        custom_filter: null
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
      this.getProcess()
    }
  },
  computed: {
    headerTable() {
      const className = this.show_special_filter ? '' : 'd-none'
      return [
        {
          key: 'audit_processes',
          label: 'Nombre de evaluación',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'corporate',
          label: 'Planta',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'scope',
          label: 'Alcance',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'dates_format',
          label: 'Periodo de registro',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'use_kpi',
          label: 'KPI',
          class: `text-center ${className}`,
          sortable: false,
        },
        {
          key: 'sections',
          label: 'Secciones',
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
    async getProcess() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getListProcess(params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.show_special_filter = data.show_special_filter
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    canUpdateProcess({ aplicability_register }) {
      // sin clasificar
      return aplicability_register.id_status == 3
    },
    canDeleteProcess({ aplicability_register }) {
      // diferente a finalizado
      return aplicability_register.id_status != 6
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ process, id_customer, id_corporate, id_scope, date, evaluation_type_id, custom_filter }) {
      this.filters.audit_processes = process
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
      this.filters.id_scope = id_scope,
      this.filters.date = date
      this.filters.evaluation_type_id = evaluation_type_id
      this.filters.custom_filter = custom_filter
      await this.getProcess()
    },
    async alertRemove({ id_audit_processes, audit_processes }) {
      try {
        const question = `¿Estás seguro de eliminar la aplicabilidad: '${audit_processes}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteProcess(id_audit_processes)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getProcess()
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
