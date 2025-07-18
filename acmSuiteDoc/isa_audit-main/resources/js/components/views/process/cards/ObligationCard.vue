<template>
  <fragment v-if="completedAplicability">
    <!-- activate obligation -->
    <b-row v-if="evaluationObligation && !thereIsObligation" >
      <b-col class="text-center">
        <activate-section
          @successfully="reload"
          section="obligation"
          :id-audit-process="idAuditProcess"
          :id-aplicability-register="idAplicabilityRegister"
        ></activate-section>
      </b-col>
    </b-row>

    <b-row v-if="evaluationObligation && thereIsObligation">
      <b-col md="12" sm="12">
        <b-card body-class="text-center" class="text-center">
          <h6 class="font-italic font-weight-bold mb-0">Evaluación: Permisos Críticos</h6>
          <b-card-text class="font-italic">
            Evalua cada requiermiento critico, en esta sección colocarás fechas de vencimiento de cada requerimiento
          </b-card-text>
          <b-row>
            <!-- go to obligations -->
            <template>
              <b-col sm="12" md="6">
                <b-card-text class="font-weight-bold">
                  Periodo: 
                  <b-badge pill :variant="colorBtnObligations">
                    {{ periodDateObligations }} 
                  </b-badge>
                </b-card-text>
                <b-button 
                  :disabled="enableBtnObligations"
                  @click="openObligations"
                  block pill 
                  :variant="colorBtnObligations"
                >
                  Ir a Permisos Críticos 
                  <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
                </b-button>
              </b-col>
            </template>
            <!-- go to action plan -->
            <template>
              <b-col v-if="hasActionPlan" sm="12" md="6">
                <b-card-text class="font-weight-bold">
                  Periodo: 
                  <b-badge pill :variant="colorBtnAction">
                    {{ periodDateObligations }} 
                  </b-badge>
                </b-card-text>
                <b-button 
                  :disabled="enableBtnAction"
                  @click="openActionPlan"
                  block pill 
                  :variant="colorBtnAction"
                >
                  Ir a Plan de accion
                  <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon>
                </b-button>
              </b-col>
              <b-col v-else sm="12" md="6">
                <b-card-text class="font-weight-bold">
                  Periodo: 
                  <b-badge pill variant="info"> --/--/-- - --/--/-- </b-badge>
                </b-card-text>
                <activate-action-plan 
                  @successfully="reload"
                  :id-audit-process="idAuditProcess"
                  :id-aplicability-register="idAplicabilityRegister"
                  section="obligation"
                  :registerable-id="obligationRegister.id"
                />
              </b-col>
            </template>

          </b-row>
        </b-card>
      </b-col>
    </b-row>
    
  </fragment>
</template>

<script>
import ActivateSection from './ActivateSection.vue'
import ActivateActionPlan from './ActivateActionPlan.vue'

export default {
  components: {
    ActivateSection,
    ActivateActionPlan
  },
  props: {
    process: {
      type: Object,
      required: true
    }
  },
  computed: {
    evaluationObligation() {
      const evaluateTypeId = this.process?.evaluation_type_id ?? 1
      return evaluateTypeId == 2 || evaluateTypeId == 3
    },
    idAuditProcess() {
      return this.process?.id_audit_processes ?? null
    },
    idAplicabilityRegister() {
      return this.process?.aplicability_register?.id_aplicability_register ?? null
    },
    idObligationRegister() {
      return this.process.aplicability_register.obligation_register.id ?? null
    },
    idActionRegister() {
      return this.process?.aplicability_register?.obligation_register?.action_plan_register?.id_action_register ?? null
    },
    completedAplicability() {
      const statusKey = this.process.aplicability_register?.status?.key ?? ''
      return statusKey == 'FINISHED_APLICABILITY'
    },
    thereIsObligation() {
      const thereIs = this.process?.aplicability_register?.obligation_register ?? null
      return thereIs != null ?? false
    },
    periodDateObligations() {
      const obligationRegister = this.process?.aplicability_register?.obligation_register
      if ( !obligationRegister?.id ) return '--/--/-- - --/--/--'
      const { init_date_format, end_date_format } = obligationRegister
      return `${init_date_format} - ${end_date_format}`
    },
    hasActionPlan() {
      const actionPlanRegister = this.process?.aplicability_register?.obligation_register?.action_plan_register
      return actionPlanRegister != null
    },
    obligationRegister() {
      return this.process?.aplicability_register?.obligation_register ?? null
    },
    enableBtnObligations() {
      return !(this.process?.aplicability_register?.obligation_register?.in_range_date ?? false)
    },
    colorBtnObligations() {
      const inRangeDate = this.process?.aplicability_register?.obligation_register?.in_range_date ?? true
      return inRangeDate ? 'info' : 'secondary'
    },
    enableBtnAction() {
      return !(this.process?.aplicability_register?.obligation_register?.action_plan_register?.in_range_date ?? false)
    },
    colorBtnAction() {
      const inRangeDate = this.process?.aplicability_register?.obligation_register?.action_plan_register?.in_range_date ?? true
      return inRangeDate ? 'info' : 'secondary'
    }
  },
  methods: {
    reload() {
      this.$emit('successfully')
    },
    openObligations() {
      if ( this.idObligationRegister == null ) return
      const host = window.location.origin
      const url = `${host}/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/obligation/${this.idObligationRegister}/view`
      window.open(url, '_blank')
    },
    openActionPlan() {
      if ( this.idActionRegister == null ) return
      const host = window.location.origin
      const url = `${host}/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/obligation/${this.idObligationRegister}/action/${this.idActionRegister}/plan/view`
      window.open(url, '_blank')
    }
  }
}
</script>

<style>

</style>