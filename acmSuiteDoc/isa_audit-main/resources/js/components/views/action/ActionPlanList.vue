<template>
  <fragment>
    <loading :show="loadingMixin" />
    <filter-area 
      :title="originName" 
      :opened="true"
    >
      <template v-slot:action>
        <b-button
          class="mt-1 mr-2"
          variant="success"
          v-b-tooltip.hover.left
          title="Regresar"
          @click="backProcess"
        >
          Regresar
        </b-button>
      </template>
      <filters
        @filterSelected="setFilters"
        :id-audit-process="idAuditProcess"
        :headers="headers"
        ref="filtersArea"
      />
    </filter-area>

    <filter-area title="Requerimientos" :opened="true">
      <template v-slot:action>
        <action-plan-report-button 
          :id-audit-process="idAuditProcess"
          :id-aplicability-register="idAplicabilityRegister"
          :origin="origin"
          :id-section-register="idSectionRegister"
          :id-action-register="idActionRegister"
          class-button="mt-1 mr-2"
        />
      </template>
      <div class="row d-flex justify-content-center">
        <template>
          <b-button-group size="sm">
            <b-button v-for="button in status" :key="button.id_status"
              :variant="button.color"
              @click="setStatus(button.id_status)"
            >
              <b-badge :variant="button.color">
                {{ button.count }}
              </b-badge>
              {{ button.status }}
            </b-button>
          </b-button-group>
        </template>
      </div>
      <b-table-simple class="group-table" hover responsive>
        <b-thead>
          <b-tr>
            <b-th
              class="text-center" 
              :class="hideColumnHead(showFinding, field.key, 'finding')"
              v-for="field in headerTable" :key="field.key"
            >
              {{ field.label }}
            </b-th>
          </b-tr>
        </b-thead>
        <b-tbody>
          <template v-if="!groupList.length">
            <b-tr class="row-group">
              <b-td class="text-center" :colspan="headerTable.length">
                No hay registros que mostrar
              </b-td>
            </b-tr>
          </template>
          <template v-else>
            <template v-for="aspect in groupList">
              <b-tr 
                :key="getKeyGroupAction(aspect, 'aspect')" 
                class="row-group row-level-0"
              >
                <b-td class="text-center" :colspan="headerTable.length">
                  {{ getLabelGroup(aspect, 'aspect') }}
                </b-td>
              </b-tr>
              <template v-for="requirement in aspect">
                <b-tr 
                  :key="getKeyGroupAction(requirement, 'requirement')" 
                  class="row-group row-level-1"
                >
                  <b-td class="text-center" :colspan="headerTable.length">
                    {{ getLabelGroup(requirement, 'requirement') }}
                  </b-td>
                </b-tr>
                <template>
                  <b-tr v-for="row in requirement" :key="row.id">
                    <b-td class="text-center">{{ getRequirementText(row) }}</b-td>
                    <b-td class="text-center">
                      <b-link v-if="row.risk_totals.length">
                        <div class="d-flex align-items-center flex-column">
                          <b-badge v-for="category in row.risk_totals" :key="category.id_risk_category"        
                            class="mb-1" variant="info"
                          >
                            {{ category.category.risk_category }}: {{ category.interpretation }}
                          </b-badge>
                        </div>
                      </b-link>
                      <b-badge v-else variant="secondary">
                        Riesgo no evaluado 
                      </b-badge>
                    </b-td> <!-- targets: 3 -->
                    <b-td class="text-center">
                      <modal-priority
                        :record="row"
                        :id-audit-process="idAuditProcess"
                        :id-aplicability-register="idAplicabilityRegister"
                        :origin="origin"
                        :id-section-register="idSectionRegister"
                        :id-action-register="idActionRegister"
                        :id-action-plan="row.id_action_plan"
                        @successfully="getActions"
                      />
                    </b-td>
                    <b-td class="text-center" :class="showFinding ? 'd-none' : ''">
                      <!-- hallazgo solo en ap auditoria -->
                      <modal-finding
                        :record="row"
                      />
                    </b-td>
                    <b-td>
                      <b-button class="btn-link go-to-process">
                        {{ row.init_date_format  }}
                      </b-button>
                    </b-td>
                    <b-td>
                      <b-button class="btn-link go-to-process">
                        {{ row.close_date_format  }}
                      </b-button>
                    </b-td>
                    <b-td class="text-center">
                      <b-badge v-if="row.status"
                        pill 
                        :variant="row.status.color"
                      >
                        {{ row.status.status }}
                      </b-badge>
                    </b-td>
                    <b-td class="text-center">
                      <modal-user
                        :action="row"
                        :id-audit-process="idAuditProcess"
                        :id-aplicability-register="idAplicabilityRegister"
                        :origin="origin"
                        :id-section-register="idSectionRegister"
                        :id-action-register="idActionRegister"
                        :id-action-plan="row.id_action_plan"
                        @successfully="getActions"
                      />
                    </b-td>
                    <b-td class="text-center d-flex justify-content-center">
                      <modal-task-main
                        v-if="validateAuditors"
                        :action="row"
                        :id-audit-process="idAuditProcess"
                        :id-aplicability-register="idAplicabilityRegister"
                        :origin="origin"
                        :id-section-register="idSectionRegister"
                        :id-action-register="idActionRegister"
                        :id-action-plan="row.id_action_plan"
                        @successfully="getActions"
                      />
                      <modal-expired
                        :id-audit-process="idAuditProcess"
                        :id-aplicability-register="idAplicabilityRegister"
                        :origin="origin"
                        :id-section-register="idSectionRegister"
                        :id-action-register="idActionRegister"
                        :id-action-plan="row.id_action_plan"
                        :key-status="row.status?.key ?? ''"
                        :has-historial-casuse="row.expired_exists"
                        @successfully="reloadAll"
                      />
                    </b-td>
                  </b-tr>
                </template>
              </template>
            </template>
          </template>
        </b-tbody>
      </b-table-simple>
      <!-- Paginator -->
      <app-paginator
        :data-list="paginate"
        @pagination-data="changePaginate"
      />
      <!-- End Paginator -->
    </filter-area>
  </fragment>
</template>

<script>
import FilterArea from '../components/slots/FilterArea.vue'
import Filters  from './Filters.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import ModalFinding from './ModalFinding.vue'
import ModalPriority from './ModalPriority.vue'
import ModalUser from './ModalUser.vue'
import ModalExpired from './expired/ModalExpired.vue'
import ModalTaskMain from './task/ModalTaskMain.vue'
import ActionPlanReportButton from './ActionPlanReportButton.vue'
import { allOrigins } from '../constants/origins'
import { getActionsMain } from '../../../services/actionPlanService'
import { groupItems, getLabelGroup, getNoRequirementText, 
  getRequirementText, getKeyGroupAction } from '../components/scripts/texts'

export default {
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    ModalFinding,
    ModalPriority,
    ModalUser,
    ModalExpired,
    ModalTaskMain,
    ActionPlanReportButton,
  },
  created() {
    this.titlePage = this.originName
  },
  async mounted() {
    document.querySelector('#titlePage').innerHTML = this.titlePage
    await this.getActions()
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    idAplicabilityRegister: {
      type: Number,
      required: true,
    },
    origin: {
      type: String,
      required: true,
    },
    idSectionRegister: {
      type: Number,
      required: true,
    },
    idActionRegister: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      items: [],
      status: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      titlePage: '',
      filters: {
        id_matter: null,
        id_aspect: null,
        no_requirement: null,
        id_status: null,
        id_priority: null,
        dates: null,
        name: null
      },
      headers: {
        audit_process: '---',
        corporate_name: '---',
        customer_name: '---',
        scope: '---',
        matters: [],
        status: [],
      }  
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'requirement',
          label: 'Requerimiento',
        },
        {
          key: 'risk_level',
          label: 'Nivel de Riesgo',
        },
        {
          key: 'id_priority',
          label: 'Prioridad',
        },
        {
          key: 'finding',
          label: 'Hallazgo',
        },
        {
          key: 'init_date',
          label: 'Fecha de inicio',
        },
        {
          key: 'close_date',
          label: 'Fecha de vencimiento',
        },
        {
          key: 'id_status',
          label: 'Estatus',
        },
        {
          key: 'full_name',
          label: 'Responsable de aprobación',
        },
        {
          key: 'actions',
          label: 'Acciones',
          class: 'text-center td-actions',
        },
      ]
    },
    originName() {
      return `Plan de Acción: ${ allOrigins(this.origin) }`
    },
    groupList() {
      return this.groupItems(this.items)
    },
    showFinding() {
      return this.origin == 'audit' ? false : true
    }
  },
  watch: {
    filtersValue: {
      handler(newValue) {
        this.setFilters(newValue)
      },
      deep: true,
    },
    'paginate.page': function() {
      this.getActions()
    }
  },
  methods: {
    // external methods
    groupItems, 
    getLabelGroup,
    getNoRequirementText, 
    getRequirementText, 
    getKeyGroupAction,
    hideColumnHead(value, key, hideKey) {
      return value && key == hideKey ? 'd-none' : ''
    },
    // local methods
    async getActions() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        const { data } = await getActionsMain(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.status = data.info.status
        this.setHeaders(data.info)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async reloadAll() {
      this.$emit('successfully')
    },
    async setFilters({ id_matter, id_aspect, no_requirement, id_status, id_priority, dates, name }) {
      this.filters.id_matter = id_matter
      this.filters.id_aspect = id_aspect
      this.filters.no_requirement = no_requirement
      this.filters.id_status = id_status
      this.filters.id_priority = id_priority
      this.filters.dates = dates
      this.filters.name = name
      await this.getActions()
    },
    setHeaders({ audit_process, corporate_name, customer_name, scope, matters, status }) {
      this.headers.audit_process = audit_process
      this.headers.corporate_name = corporate_name
      this.headers.customer_name = customer_name
      this.headers.scope = scope
      this.headers.matters = matters
      this.headers.status = status
    },
    setStatus(id_status) {
      this.$refs.filtersArea.filters.id_status = id_status
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    validateAuditors(row) {
      return row.auditors.length > 0
    },
    backProcess() {
      window.location.href = `/v2/process/view`
    },
  }
}
</script>

<style>

</style>