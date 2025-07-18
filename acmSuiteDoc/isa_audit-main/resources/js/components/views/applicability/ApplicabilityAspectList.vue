<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" opened>
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
            :headers="headers"
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
              @click="completeAplicabilityRegister"
            >
              <b-icon icon="check-circle-fill" aria-hidden="true"></b-icon>
              Finalizar Aplicabilidad
            </b-button>
            <button-report-applicability
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :right-class="true"
            />
            <button-answers-report
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :right-class="true"
            />
          </b-col>
        </b-row>
        <b-row v-if="showInfoGlobal">
          <b-col class="text-center">
            <label class="font-weight-bold">
              Aspectos Clasificados
            </label>
            <div>
              {{ `${progress.global.count_classified_aspects} / ${progress.global.count_all_aspects}` }}
            </div>
          </b-col>
        </b-row>
        <b-row v-else>
          <b-col class="text-center" sm="12" md="4" lg="4">
            <label class="font-weight-bold">
              Materia
            </label>
            <div>
              {{ progress.matter.matter }}
            </div>
          </b-col>
          <b-col class="text-center" sm="12" md="4" lg="4">
            <label class="font-weight-bold">
              Estatus Materia
            </label>
            <div>
              {{ progress.matter.status }}
            </div>
          </b-col>
          <b-col class="text-center" sm="12" md="4" lg="4">
            <label class="font-weight-bold">
              Aspectos Clasificados
            </label>
            <div>
              {{ `${progress.matter.count_classified_aspects} / ${progress.matter.count_all_aspects}` }}
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
                <b-badge v-if="data.item.id_application_type" pill variant="primary">
                  {{ data.item.application_type.application_type }}
                </b-badge>
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
import FilterArea from '../components/slots/FilterArea'
import AppPaginator from '../components/app-paginator/AppPaginator'
import ButtonReportApplicability from './reports/ButtonReport'
import ButtonAnswersReport from './reports/ButtonAnswersReport'
import Filters  from './Filters'
import { completeAplicabilityRegister } from '../../../services/AplicabilityRegisterService'
import { getContractAspectsAll } from '../../../services/contractAspectService'

export default {
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    ButtonReportApplicability,
    ButtonAnswersReport,
  },
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Aplicabilidad`
    this.getContractAspectsAll()
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
  },
  data() {
    return {
      items: [],
      headers: {
        audit_process: '---' ,
        corporate_name: '---' ,
        customer_name: '---' ,
        evaluate_risk: '---',
        matters: [],
        scope: '---',
        status: [],
      },
      filters: {
        id_contract_matter: null,
        id_contract_aspect: null,
        id_status: null,
      },
      progress: {
        global: {
          count_classified_aspects: 0,
          count_all_aspects: 0,
          total: 0
        },
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
      this.getContractAspectsAll()
    }
  },
  computed: {
    showInfoGlobal() {
      return this.progress.matter == null
    },
    showFinishButton() {
      if (this.progress.status == null) return false
      return this.progress.status.key == 'CLASSIFIED_APLICABILITY'
    },
    totalProgress() {
      return this.progress.matter != null ? this.progress.matter.total : this.progress.global.total
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
          key: 'actions',
          label: 'Acciones',
          class: 'text-center td-actions',
          sortable: false,
        }
      ]
    }
  },
  methods: {
    async getContractAspectsAll() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getContractAspectsAll(this.idAuditProcess, this.idAplicabilityRegister, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.setHeaders(data.info)
        this.setProgress(data.progress)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setHeaders({ audit_process, corporate_name, customer_name, evaluate_risk, matters, scope, status }) {
      this.headers.audit_process = audit_process
      this.headers.corporate_name = corporate_name
      this.headers.customer_name = customer_name
      this.headers.evaluate_risk = evaluate_risk
      this.headers.matters = matters
      this.headers.scope = scope
      this.headers.status = status
    },
    setProgress({ total, count_all_aspects, count_classified_aspects, matter, status }) {
      this.progress.global.total = total
      this.progress.global.count_all_aspects = count_all_aspects
      this.progress.global.count_classified_aspects = count_classified_aspects
      this.progress.matter = matter
      this.progress.status = status
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ id_contract_matter, id_contract_aspect, id_status }) {
      this.filters.id_contract_matter = id_contract_matter
      this.filters.id_contract_aspect = id_contract_aspect
      this.filters.id_status = id_status
      await this.getContractAspectsAll()
    },
    async completeAplicabilityRegister() {
      try {
        const title = '¿Estás seguro de finalizar la Aplicabilidad?'
        const question = 'Una vez finalizada no podras modificar las respuestas'
        const { value } = await this.alertQuestionMixin(title, question, 'question')
        if (value) {
          this.showLoadingMixin()
          const { data } = await completeAplicabilityRegister(this.idAuditProcess, this.idAplicabilityRegister)
          await this.getContractAspectsAll()
          this.responseMixin(data)
          this.showLoadingMixin()
        }
      } catch (error) {
        this.responseMixin(data)
        this.showLoadingMixin()
      }
    },
    showEvaluate({id_contract_matter, id_contract_aspect}) {
      window.location.href = `/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/matter/${id_contract_matter}/aspect/${id_contract_aspect}/view`
    },
    backProcess() {
      window.location.href = `/v2/process/view`;
    },
  }
}
</script>

<style></style>
