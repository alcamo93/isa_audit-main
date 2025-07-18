<template>
  <fragment>
    <loading :show="loadingMixin" />
    <filter-area :opened="true">
      <template v-slot:action>
        <b-button class="float-right mt-2 mr-2"
          variant="success"
          v-b-tooltip.hover
          title="Regresar"
          @click="backProcess"
        >
          Regresar
        </b-button>
      </template>
      <filters
        @filterSelected="setFilters"
        :headers="headers"
        :status="status"
        ref="filtersArea"
      />
    </filter-area>
    <b-card>
      <b-row>
        <b-col>
          <button-report class="mr-2"
            :id-audit-process="idAuditProcess"
            :id-aplicability-register="idAplicabilityRegister"
            :id-obligation-register="idObligationRegister"
            :right-class="true"
            title-button="Reporte"
          />
        </b-col>
      </b-row>
      <b-row>
        <b-col>
          <b-card-text>
            <div class="row d-flex justify-content-center">
              <template>
                <b-button-group size="sm">
                  <b-button v-for="button in status" :key="button.id_status"
                    :variant="button.color"
                    @click="currentStatus(button.id_status)"
                  >
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
                      :key="getKeyGroupObligation(aspect, 'aspect')" 
                      class="row-group row-level-0"
                    >
                      <b-td class="text-center" :colspan="headerTable.length">
                        {{ getLabelGroup(aspect, 'aspect') }}
                      </b-td>
                    </b-tr>
                    <template v-for="requirement in aspect">  
                      <b-tr 
                        :key="getKeyGroupObligation(requirement, 'requirement')" 
                        class="row-group row-level-1"
                      >
                        <b-td class="text-center" :colspan="headerTable.length">
                          {{ getLabelGroup(requirement, 'requirement') }}
                        </b-td>
                      </b-tr>
                      <template>
                        <b-tr v-for="row in requirement" :key="row.id">
                          <b-td class="text-center">{{ row.requirement.matter.matter }}</b-td>
                          <b-td class="text-center">{{ getNoRequirementText(row) }}</b-td>
                          <b-td class="text-center">{{ getRequirementText(row) }}</b-td>
                          <b-td class="text-center">
                            <modal-risk v-if="evaluateRisk"
                              :disabled-modal="row.auditor == null"
                              :id-audit-process="idAuditProcess"
                              :id-aplicability-register="idAplicabilityRegister"
                              :id-register-section="idObligationRegister"
                              registerable-type="obligation"
                              :registerable-id="row.id_obligation"
                              :record="row"
                              variant="danger"
                              @successfully="getObligations"
                            />
                            <b-badge v-else pill variant="secondary">Riesgo no evaluado</b-badge>
                          </b-td>
                          <b-td class="text-center">
                            <b-link class="btn btn-link go-to-process">
                              {{ row.init_date_format }}
                            </b-link>
                          </b-td>
                          <b-td class="text-center">
                            <b-link class="btn btn-link go-to-process">
                              {{ row.end_date_format }}
                            </b-link>
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
                            {{ getAreaPerProcess }}
                          </b-td>
                          <b-td class="text-center">
                            <modal-user
                              @successfully="getObligations"
                              :id-audit-process="idAuditProcess"
                              :id-aplicability-register="idAplicabilityRegister"
                              :id-obligation-register="idObligationRegister"
                              :obligation="row"
                            />
                          </b-td>
                          <b-td class="text-center td-actions">
                            <adapter-file-buttons
                              @successfully="getObligations"
                              :id-audit-process="idAuditProcess"
                              :id-aplicability-register="idAplicabilityRegister"
                              :id-section-register="idObligationRegister"
                              :evaluateable-id="row.id_obligation"
                              :item="row"
                              origin="Obligation"
                              :show-library="true"
                            />
                            <modal-help :record="row"/>
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
          </b-card-text>
        </b-col>
      </b-row>
    </b-card>
  </fragment>
</template>

<script>
import FilterArea from '../components/slots/FilterArea.vue'
import Filters  from './Filters.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import ModalUser from './ModalUser.vue'
import ModalHelp  from './ModalHelp.vue'
import ModalRisk from '../process/risk/ModalRisk.vue'
import AdapterFileButtons from '../components/files/AdapterFileButtons.vue'
import ButtonReport from './ButtonReport.vue'
import { getObligations } from '../../../services/obligationService'
import { groupItems, getLabelGroup, getNoRequirementText, 
  getRequirementText, getKeyGroupObligation } from '../components/scripts/texts'

export default {
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    ModalUser,
    AdapterFileButtons,
    ModalRisk,
    ModalHelp,
    ButtonReport
  },
  async mounted() {
    document.querySelector('#titlePage').innerHTML = `Reporte de estatus de permisos críticos`
    await this.getObligations()
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
    idObligationRegister: {
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
      filters: {
        obligation_register_id: null,
        id_matter: null,
        id_aspect: null,
        no_requirement: null,
        id_status: null,
        dates: null,
        name: null
      },
      headers: {
        audit_process: '---',
        corporate_name: '---',
        customer_name: '---',
        scope: '---',
        evaluate_risk: false,
        matters: [],
        status: [],
      }
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'id_matter',
          label: 'Materia',
          sortable: false,
        },
        {
          key: 'no_requirement',
          label: 'No. Requerimiento',
          sortable: false,
        },
        {
          key: 'requirement',
          label: 'Requerimiento',
          sortable: false,
        },
        {
          key: 'risk_level',
          label: 'Nivel de Riesgo',
          sortable: false,
        },
        {
          key: 'init_date',
          label: 'Fecha de expedición',
          sortable: false,
        },
        {
          key: 'end_date',
          label: 'Fecha de vencimiento',
          sortable: false,
        },
        {
          key: 'id_status',
          label: 'Estatus Permiso/Estudio/Programa',
          sortable: false,
        },
        {
          key: 'area',
          label: 'Área',
          sortable: false,
        },
        {
          key: 'full_name',
          label: 'Responsable',
          sortable: false,
        },
        {
          key: 'actions',
          label: 'Acciones',
          sortable: false,
          class: 'text-center d-flex justify-content-center'
        },
      ]
    },
    getAreaPerProcess() {
      return this.headers.scope
    },
    groupList() {
      return this.groupItems(this.items)
    },
    evaluateRisk() {
      return this.headers.evaluate_risk
    }
  },
  watch: {
    'paginate.page': function() {
      this.getObligations()
    }
  },
  methods: {
    // external methods
    groupItems, 
    getLabelGroup, 
    getNoRequirementText, 
    getRequirementText, 
    getKeyGroupObligation,
    // local methods
    backProcess() {
      window.location.href = `/v2/process/view`;
    },
    async getObligations() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        const { data } = await getObligations(this.idAuditProcess, this.idAplicabilityRegister, this.idObligationRegister, params, this.filters)
        this.status = data.info.status
        this.items = data.data
        this.headers = data.info
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
    async setFilters({ id_matter, id_aspect, no_requirement, id_status, dates, name }) {
      this.filters.id_matter = id_matter
      this.filters.id_aspect = id_aspect
      this.filters.no_requirement = no_requirement
      this.filters.id_status = id_status
      this.filters.dates = dates
      this.filters.name = name
      await this.getObligations()
    },
    currentStatus(id_status) {
      this.$refs.filtersArea.filters.id_status = id_status
    },
  },
}
</script>

<style>

</style>