<template>
  <fragment>
    <div class="d-flex" v-if="!with_data">
      <b-card class="flex-fill text-center">
        <h5>No se cuenta con datos para mostrar aún el Plan de Acción de Auditoría</h5>
      </b-card>
    </div>
    <filter-area v-else :title="`Plan de Acción de Auditoría ${year} - ${customer.corp_tradename}`" :opened="true" :custom-title="true">
      <loading :show="loadingMixin" />
      <modal-condition-graph ref="modalConditionGraph" 
        :id-audit-process="idAuditProcess"
        :id-aplicability-register="idAplicabilityRegister"
        section-name="audit"
        :id-section-register="idAuditRegister"
        :id-action-register="customer.id_action_register"
        :filters="modal"
        :data="findings.graph" 
        :aspects="findings.aspects" />
      <records-action-plan-graph ref="modalRecordsActionPlanGraph" :filters="modal_graph" />
      <records-action-plan ref="modalRecordsActionPlan" 
        :id-audit-process="idAuditProcess"
        :id-aplicability-register="idAplicabilityRegister"
        section-name="audit"
        :id-section-register="idAuditRegister"
        :id-action-register="customer.id_action_register"
        :filters="modal" 
      />
      <records-action-plan-risk ref="modalRecordsActionPlanRisk" 
        :id-audit-process="idAuditProcess"
        :id-aplicability-register="idAplicabilityRegister"
        section-name="audit"
        :id-section-register="idAuditRegister"
        :id-action-register="customer.id_action_register"
        :filters="modal_risk"
      />
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <!-- Graph pie -->
        <b-card class="flex-fill text-center m-1 w-25">
          <h5 class="global-dashboard-title">
            estatus plan de acción
          </h5>
          <current-year :series="compliance.series" :labels="compliance.labels" :colors="compliance.colors" />
        </b-card>
        <!-- Graph pie -->
        <!-- Graph bars -->
        <b-card class="flex-fill text-center m-1">
          <h5 class="global-dashboard-title">
            estatus mensual
          </h5>
          <industrial-plant-grouped :colors="monthly.colors" :series="monthly.series" :text="'Requerimientos'"
            @clickedIndex="showGraph" />
        </b-card>
        <!-- Graph bars -->
      </div>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <b-card class="flex-fill text-center m-1 w-25">
          <b-card-text v-b-tooltip.hover.left title="Mostrar Porcentage de Hallazgos" class="cursor-pointer"
            @click="showModalGraphFinding">
            <h5 class="global-dashboard-title findings-size-h5">
              {{ findings.total }} <br />
              Hallazgos
            </h5>
          </b-card-text>
        </b-card>
        <b-card class="flex-fill text-center m-1">
          <h5 class="global-dashboard-title">
            número de hallazgos
          </h5>
          <non-compliance-critical :series="criticals.series" />
        </b-card>
      </div>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <b-card class="flex-fill text-center m-1" v-for="item in matters.data" :key="item.id">
          <h5 class="global-dashboard-title text-break">
            estatus de hallazgos
          </h5>
          <h5 class="global-dashboard-title text-break">
            {{ item.matter }}
          </h5>
          <requirements-status :id-matter="item.id" :matter="item.matter" :series="item.serie"
            @clickedIndex="showRecords" />
        </b-card>
      </div>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap" v-if="risk.evaluate_risk">
        <b-card class="flex-fill text-center m-1" v-for="category in risk.categories" :key="category.id">
          <h5 class="global-dashboard-title text-break">
            hallazgos
          </h5>
          <h5 class="global-dashboard-title text-break">
            nivel de riesgo {{ category.risk_category }}
          </h5>
          <risk-level :series="category.serie" x-axis-title="Clasificación de Riesgo" @clickedIndex="showRecordsRisk" />
        </b-card>
      </div>
    </filter-area>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import CurrentYear from '../graphics/pie-chart/CurrentYear'
import IndustrialPlantGrouped from '../graphics/bar-chart/IndustrialPlantGrouped'
import NonComplianceCritical from '../graphics/bar-chart/NonComplianceCritical'
import RequirementsStatus from '../graphics/bar-chart/RequirementsStatus'
import RiskLevel from '../graphics/bar-chart/RiskLevel'
import ModalConditionGraph from '../modals/ModalConditionGraph'
import RecordsActionPlanGraph from '../modals/RecordsActionPlanGraph'
import RecordsActionPlan from '../modals/RecordsActionPlan'
import RecordsActionPlanRisk from '../modals/RecordsActionPlanRisk'
import { getDataAuditActionCorporate } from '../../../../services/dashboardService'

export default {
  components: {
    FilterArea,
    CurrentYear,
    IndustrialPlantGrouped,
    NonComplianceCritical,
    RequirementsStatus,
    RiskLevel,
    ModalConditionGraph,
    RecordsActionPlanGraph,
    RecordsActionPlan,
    RecordsActionPlanRisk
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
    idAuditRegister: {
      type: Number,
      required: true,
    }
  },
  mounted() {
    this.getCorporateAuditActionPlan()
  },
  data() {
    return {
      year: '',
      with_data: false,
      customer: {
        corp_tradename: '',
        id_action_register: 0
      },
      compliance: {
        series: [],
        labels: [],
        colors: [],
      },
      monthly: {
        colors: [],
        series: [],
      },
      criticals: {
        series: []
      },
      matters: {
        columns: 12,
        data: []
      },
      risk: {
        evaluate_risk: true,
        columns: 12,
        categories: []
      },
      modal: {
        id_action_register: null,
        id_matter: null,
        id_status: null,
        status_name: '',
        matter_name: ''
      },
      modal_graph: {
        id_action_register: null,
        id_matter: null,
        matter_name: '',
        matters: []
      },
      modal_risk: {
        id_action_register: null,
        id_risk_category: null,
        risk_category: null,
        interpretation: null,
      },
      findings: {
        total: 0,
        graph: {
          series: [],
          labels: [],
          colors: [],
        },
        aspects: [],
        id_action_register: 0
      }
    }
  },
  methods: {
    async getCorporateAuditActionPlan() {
      try {
        this.showLoadingMixin()
        const { data } = await getDataAuditActionCorporate(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister)
        const { with_data, year, monthly, project, compliance, counts, risk, findings } = data.data
        this.year = year
        this.with_data = with_data
        if (!with_data) {
          this.showLoadingMixin()
          return
        }
        this.modal_graph.matters = counts
        this.setCustomer(project)
        this.setCompliance(compliance)
        this.setMontly(monthly, counts)
        this.setCritical(counts)
        this.setMatterCount(counts)
        this.setRisk(risk)
        this.setFindings(findings)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setCustomer({ corp_tradename, id_action_register }) {
      this.customer.corp_tradename = corp_tradename
      this.customer.id_action_register = id_action_register
    },
    setCompliance(compliance) {
      this.compliance.series = compliance.map(item => item.total)
      this.compliance.labels = compliance.map(item => item.label)
      this.compliance.colors = compliance.map(item => item.color)
    },
    setMontly(monthly, counts) {
      this.monthly.colors = counts.map(matter => matter.color)
      this.monthly.series = counts.map(matter => ({
        id: matter.id_matter,
        name: matter.matter,
        data: monthly.map(month => {
          const currentMonth = month.historical_total == undefined ? [] : month.historical_total
          const find = currentMonth.find(item => item.matter_id == matter.id_matter)
          const total = find == undefined ? 0 : find.total
          return { x: month.name, y: total, fillColor: matter.matter.color }
        })
      })
      )
    },
    setCritical(counts) {
      const aspects = counts.map(matter => matter.aspects).flat().map(aspect => {
        return { y: aspect.count_critical, x: aspect.aspect, fillColor: aspect.color }
      }).filter(item => item.y != 0)
      this.criticals.series = [{ name: 'Conteo', data: aspects }]
    },
    setMatterCount(counts) {
      const numberMatter = counts.length == 0 ? 1 : counts.length
      this.matters.columns = 12 / numberMatter
      this.matters.colors = counts.map(matter => matter.color)
      this.matters.data = counts.map(matter => {
        const currentSerie = matter.count_status.map(status => ({ x: status.status, y: status.count, fillColor: status.color_hexadecimal, id_status: status.id_status }))
        return { id: matter.id_matter, matter: matter.matter, serie: [{ name: 'Conteo', data: currentSerie }] }
      })
    },
    setRisk({ evaluate_risk, categories }) {
      this.risk.evaluate_risk = evaluate_risk
      const numberCategories = categories.length == 0 ? 1 : categories.length
      this.risk.columns = 12 / numberCategories
      this.risk.categories = categories.map(({ id_risk_category, risk_category, interpretations }) => {
        const serie = interpretations.map(({ interpretation, count, color, id_risk_category }) => {
          return { x: interpretation, y: count, fillColor: color, id_risk_category, interpretation, risk_category }
        })
        return { id: id_risk_category, risk_category: risk_category, serie: [{ name: 'Nivel de Riesgo', data: serie }] }
      })
    },
    setFindings({ count_total, percentage, aspects }) {
      this.findings.total = count_total
      this.findings.aspects = aspects
      this.findings.graph.series = percentage.map(item => item.total)
      this.findings.graph.labels = percentage.map(item => item.label)
      this.findings.graph.colors = percentage.map(item => item.color)
    },
    showModalGraphFinding() {
      this.$refs.modalConditionGraph.showModal()
    },
    showRecords({ id_matter, id_status, matter_name, status_name }) {
      this.modal.id_matter = id_matter
      this.modal.matter_name = matter_name
      this.modal.id_status = id_status
      this.modal.status_name = status_name
      this.$refs.modalRecordsActionPlan.showModal()
    },
    showGraph({ serie }) {
      const { id, name } = serie
      this.modal_graph.id_matter = id
      this.modal_graph.matter_name = name
      this.modal_graph.corp_tradename = this.customer.corp_tradename
      this.modal_graph.year = this.year
      this.$refs.modalRecordsActionPlanGraph.showModal()
    },
    showRecordsRisk({ id_risk_category, risk_category, interpretation }) {
      this.modal_risk.id_risk_category = id_risk_category
      this.modal_risk.risk_category = risk_category
      this.modal_risk.interpretation = interpretation
      this.$refs.modalRecordsActionPlanRisk.showModal()
    },
  }
}
</script>

<style scoped>
.text-break {
  width: auto;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.global-dashboard-title {
  text-transform: capitalize !important;
  color: #113C53;
  font-family: Verdana;
  font-weight: 600;
  font-size: 18px;
}
.findings-size-h5 {
  font-size: 30px;
  padding: 50px 0px 50px 0px;
}

.cursor-pointer {
  cursor: pointer;
}
</style>