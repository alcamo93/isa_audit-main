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
              @click="backProcess"
            >
              Regresar
            </b-button>
          </template>
          <filters
            :info="info"
            @filterSelected="setFilters"
          />
        </filter-area>
      </div>
      <b-card>
        <b-row>
          <b-col class="d-flex justify-content-end">
            <b-button v-if="showFinishButton"
              class="float-right mr-2"
              variant="success"
              @click="completeAuditRegister"
            >
              <b-icon icon="check-circle-fill" aria-hidden="true"></b-icon>
              Finalizar Auditoría
            </b-button>
            <button-report-audit
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :id-audit-register="idAuditRegister"
              :right-class="true"
            />
            <button-document-report
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :id-audit-register="idAuditRegister"
              :right-class="true"
            />
            <button-progress-report
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :id-audit-register="idAuditRegister"
              :right-class="true"
            />
          </b-col>
        </b-row>
        <b-row v-if="!showInfoMatter">
          <b-col class="text-center">
            <label class="font-weight-bold">
              Cumplimiento Global
            </label>
            <div>
              {{ progress.total }}%
            </div>
          </b-col>
        </b-row>
        <b-row v-else>
          <b-col class="text-center" sm="12" md="3" lg="3">
            <label class="font-weight-bold">
              Materia
            </label>
            <div>
              {{ progress.matter.matter }}
            </div>
          </b-col>
          <b-col class="text-center" sm="12" md="3" lg="3">
            <label class="font-weight-bold">
              Estatus Materia
            </label>
            <div>
              {{ progress.matter.status }}
            </div>
          </b-col>
          <b-col class="text-center" sm="12" md="3" lg="3">
            <label class="font-weight-bold">
              Cumplimiento Global
            </label>
            <div>
              {{ progress.matter.total }}%
            </div>
          </b-col>
          <b-col class="text-center" sm="12" md="3" lg="3">
            <label class="font-weight-bold">
              Aspectos Auditados
            </label>
            <div>
              {{ `${progress.matter.count_audited_aspects} / ${progress.matter.count_all_aspects}` }}
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <b-progress class="mt-2" variant="success" show-progress striped animated>
              <b-progress-bar :value="totalProgress" :label="`${totalProgress}%`"></b-progress-bar>
            </b-progress>
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
              <template #cell(matter)="data">
                <span> {{ data.item.matter.matter }} </span>
              </template>
              <template #cell(aspect)="data">
                <span> {{ data.item.aspect.aspect }} </span>
              </template>
              <template #cell(status)="data">
                <b-badge pill :variant="data.item.status.color">
                  {{ data.item.status.status }}
                </b-badge>
              </template>
              <template #cell(application_type)="data">
                <b-badge pill variant="primary">
                  {{ data.item.application_type.application_type }}
                </b-badge>
              </template>
              <template #cell(total)="data">
                {{ data.item.total }}%
              </template>
              <template #cell(actions)="data">
                <!-- button aspect -->
                <b-button 
                  v-b-tooltip.hover.left
                  :title="`Evaluar Aspecto: ${data.item.aspect.aspect}`"
                  variant="info"
                  class="btn-link"
                  @click="showEvaluate(data.item)"
                >  
                  <b-icon icon="ui-checks-grid" aria-hidden="true"></b-icon>
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
import FilterArea from '../components/slots/FilterArea.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import ButtonReportAudit from './reports/ButtonReport.vue'
import ButtonDocumentReport from './reports/ButtonDocumentReport.vue'
import ButtonProgressReport from './reports/ButtonProgressReport.vue'
import Filters  from './Filters.vue'
import { completeAuditRegister } from '../../../services/AuditRegisterService'
import { getAuditAspectsAll } from '../../../services/AuditAspectService'

export default {
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    ButtonReportAudit,
    ButtonDocumentReport,
    ButtonProgressReport
  },
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Auditoría`
    this.getAuditAspectsAll()
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true
    },
    idAplicabilityRegister: {
      type: Number,
      required: true
    },
    idAuditRegister: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      items: [],
      filters: {
        id_audit_matter: null,
        id_audit_aspect: null,
        id_status: null,
      },
      info: {
        audit_process: '',
        corporate_name: '',
        customer_name: '',
        evaluate_risk: false,
        scope: '',
        matters: [],
        status: [],
      },
      progress: {
        total: 0,
        matter: null,
        status: null
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
      this.getAuditAspectsAll()
    }
  },
  computed: {
    showInfoMatter() {
      return this.progress.matter != null
    },
    showFinishButton() {
      if (this.progress.status == null) return false
      return this.progress.status.key != 'FINISHED_AUDIT_AUDIT'
    },
    totalProgress() {
      return this.progress.matter != null ? this.progress.matter.total : this.progress.total
    },
    headerTable() {
      return [
        {
          key: 'matter',
          label: 'Materia',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'aspect',
          label: 'Aspecto',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estatus Aspecto',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'application_type',
          label: 'Competencia',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'total',
          label: 'Cumplimiento',
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
    async getAuditAspectsAll() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getAuditAspectsAll(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister, params, this.filters)
        this.items = data.data
        this.setInfo(data.info)
        this.progress.total = data.progress.total
        this.progress.matter = data.progress.matter
        this.progress.status = data.progress.status
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
    async setFilters({ id_audit_matter, id_audit_aspect, id_status }) {
      this.filters.id_audit_matter = id_audit_matter
      this.filters.id_audit_aspect = id_audit_aspect
      this.filters.id_status = id_status
      await this.getAuditAspectsAll()
    },    
    async alertRemove({ id_audit_matter, id_audit_aspect, aspect }) {
      try {
        const question = `¿Estás seguro de eliminar el aspecto: '${aspect.aspect}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteAuditAspect(this.idAuditRegister, id_audit_matter, id_audit_aspect)
          await this.getAuditAspectsAll()
          this.showLoadingMixin()
          this.responseMixin(data)
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async completeAuditRegister() {
      try {
        const title = '¿Estás seguro de finalizar la Auditoria?'
        const question = 'Una vez finalizada no podras modificar las respuestas'
        const { value } = await this.alertQuestionMixin(title, question, 'question')
        if (value) {
          this.showLoadingMixin()
          const { data } = await completeAuditRegister(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister)
          await this.getAuditAspectsAll()
          this.responseMixin(data)
          this.showLoadingMixin()
        }
      } catch (error) {
        this.responseMixin(data)
        this.showLoadingMixin()
      }
    },
    setInfo({ audit_process, corporate_name, customer_name, evaluate_risk, scope, matters, status }) {
      this.info.audit_process = audit_process
      this.info.corporate_name = corporate_name
      this.info.customer_name = customer_name
      this.info.evaluate_risk = evaluate_risk
      this.info.scope = scope
      this.info.matters = matters
      this.info.status = status
    },
    showEvaluate({id_audit_matter, id_audit_aspect}) {
      window.location.href = `/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/audit/${this.idAuditRegister}/matter/${id_audit_matter}/aspect/${id_audit_aspect}/view`
    },
    backProcess() {
      window.location.href = `/v2/process/view`
    },
  }
}
</script>

<style></style>
