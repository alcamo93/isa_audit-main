<template>
  <fragment v-if="completedAplicability">

    <b-row v-if="evaluationAudit && !thereAreAudit" 
      class="d-flex justify-content-center"
    >
      <b-col class="text-center">
        <activate-section
          @successfully="reload"
          section="audit"
          :id-audit-process="idAuditProcess"
          :id-aplicability-register="idAplicabilityRegister"
        />
      </b-col>
    </b-row>

    <template v-if="evaluationAudit && thereAreAudit">
      <b-row>
        <b-col>
          Puedes agregar hasta 
          <b-badge pill variant="info">{{ evaluatePerYear }}</b-badge>
          Auditorías hasta la fecha 
          <b-badge pill variant="info">{{ datePerYear }}</b-badge>
        </b-col>
        <b-col>
          <activate-section
            @successfully="reload"
            section="audit"
            :enable-btn-add-audit="true"
            :id-audit-process="idAuditProcess"
            :id-aplicability-register="idAplicabilityRegister"
          />
        </b-col>
      </b-row>
      <b-row v-for="audit in auditsRegisters" :key="audit.id_audit_register">
        <b-col md="12" sm="12">
          <b-card body-class="text-center" class="text-center">
            <h6 class="font-italic font-weight-bold mb-0">Evaluación: Auditoría</h6>
            <b-card-text class="font-italic">
              Evalua cada requerimiento que aplique o no aplique a tu proceso
            </b-card-text>
            <b-card-text class="font-weight-bold">
              Estatus actual de Auditoría: 
              <b-badge pill :variant="audit.status.color">
                {{ audit.status.status }}
              </b-badge>
            </b-card-text>
            <b-row class="d-flex justify-content-center">
              
              <!-- go to audit -->
              <template>
                <b-col sm="12" md="6">
                  <b-card-text class="font-weight-bold">
                    Período: 
                    <b-badge pill :variant="colorBtnAudits(audit)">
                      {{ audit.init_date_format }} - {{ audit.end_date_format }}
                    </b-badge>
                  </b-card-text>
                  <b-button 
                    :disabled="!audit.in_range_date"
                    @click="openAudit(audit.id_audit_register)"
                    block pill 
                    :variant="colorBtnAudits(audit)"
                  >
                    Ir a Auditoría 
                    <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
                  </b-button>
                </b-col>
              </template>

              <!-- go to action plan -->
              <template v-if="showActionPlan(audit)">

                <b-col v-if="hasActionPlan(audit)" sm="12" md="6">
                  <b-card-text class="font-weight-bold">
                    Periodo: 
                    <b-badge pill :variant="colorBtnAction(audit.action_plan_register)">
                      {{ getPeriodDateAction(audit.action_plan_register) }} 
                    </b-badge>
                  </b-card-text>
                  <b-button 
                    :disabled="enableBtnAction(audit.action_plan_register)"
                    @click="openActionPlan(audit.id_audit_register, audit.action_plan_register.id_action_register)"
                    block pill 
                    :variant="colorBtnAction(audit.action_plan_register)"
                  >
                    Ir a Plan de accion
                    <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon>
                  </b-button>
                </b-col>

                <b-col v-else sm="12" md="6">
                  <b-card-text class="font-weight-bold">
                    Periodo: <b-badge pill variant="info"> --/--/-- - --/--/-- </b-badge>
                  </b-card-text>
                  <activate-action-plan
                    @successfully="reload"
                    :id-audit-process="idAuditProcess"
                    :id-aplicability-register="idAplicabilityRegister"
                    section="audit"
                    :registerable-id="audit.id_audit_register"
                  />
                </b-col>

              </template>

            </b-row>
          </b-card>
        </b-col>  
      </b-row>
    </template>

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
    evaluationAudit() {
      const evaluateTypeId = this.process?.evaluation_type_id ?? 1
      return evaluateTypeId == 1 || evaluateTypeId == 3
    },
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
    completedAplicability() {
      const statusKey = this.process.aplicability_register?.status?.key ?? ''
      return statusKey == 'FINISHED_APLICABILITY'
    },
    thereAreAudit() {
      const allAudits = this.process?.aplicability_register?.audit_register ?? []
      return allAudits.length > 0
    },
    auditsRegisters() {
      return this.process?.aplicability_register?.audit_register ?? []
    },
    datePerYear() {
      return  this.process?.end_date_format ?? '--/--/--'
    },
    evaluatePerYear() {
      this.process?.per_year ?? '--/--/--'
    },
  },
  methods: {
    reload() {
      this.$emit('successfully')
    },
    openAudit(idAuditRegister) {
      const host = window.location.origin
      const urlAudit = `/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/audit/${idAuditRegister}/view`
      const url = `${host}${urlAudit}`
      window.open(url, '_blank')
    },
    openActionPlan(idAuditRegister, idActionRegister) {
      const host = window.location.origin
      const url = `${host}/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/audit/${idAuditRegister}/action/${idActionRegister}/plan/view`
      window.open(url, '_blank')
    },
    showActionPlan(audit) {
      return audit.status.key == 'FINISHED_AUDIT_AUDIT'
    },
    hasActionPlan(actionRegister) {
      return actionRegister?.action_plan_register != null
    },
    getPeriodDateAction({ init_date_format, end_date_format }) {
      return `${init_date_format} - ${end_date_format}`
    },
    enableBtnAudits(audit) {
      return !audit.in_range_date
    },
    colorBtnAudits(audit) {
      if (audit == null) return 'info'
      return audit.in_range_date ? 'info' : 'secondary'
    },
    enableBtnAction(action) {
      if (action == null) return false
      return !action.in_range_date
    },
    colorBtnAction(action) {
      if (action == null) return 'info'
      return action.in_range_date ? 'info' : 'secondary'
    }
  }
}
</script>

<style>

</style>