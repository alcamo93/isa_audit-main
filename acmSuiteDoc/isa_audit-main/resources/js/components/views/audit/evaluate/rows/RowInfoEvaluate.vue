<template>
  <b-tr>
    <b-td rowspan="2" class="text-center">{{ getNoRequirementText(row) }}</b-td>
    <b-td rowspan="2" class="text-justify">{{ getRequirementText(row) }}</b-td>
    <b-td rowspan="2" class="text-center">{{ getFieldRequirement(row, 'evidence') }}</b-td>
    <b-td rowspan="2" class="text-justify">{{ getFieldRequirement(row, 'document') }}</b-td>
    <b-td rowspan="2" class="text-justify">{{ getFieldRequirement(row, 'description') }}</b-td>
    <b-td class="text-center">{{ getFieldRequirement(row, 'condition') }}</b-td>
    <b-td class="text-center">{{ getFieldRequirement(row, 'application_type') }}</b-td>
    <b-td class="text-center td-actions">
      <modal-risk v-if="evaluateRiskShow"
        title-button="Nivel de Riesgo"
        variant="danger"
        :id-audit-process="this.params.idAuditProcess"
        :id-aplicability-register="this.params.idAplicabilityRegister"
        :id-register-section="this.params.idAuditRegister"
        registerable-type="audit"
        :registerable-id="row.audit.id_audit"
        :record="row.audit"
        @successfully="successfully"
      />
      <b-badge v-else pill variant="secondary">
        N/A
      </b-badge>
    </b-td>
    <b-td class="text-center td-actions">
      <!-- button legal basis -->
      <legal-modal 
        :record="row"
      />
      <!-- button acceptance -->
      <accept-modal 
        :record="row"
      />
      <!-- button finding -->
      <finding-modal 
        :params="params"
        :record="row"
        @successfully="successfully"
      />
      <!-- button help -->
      <help-modal 
        :record="row"
      />
      <!-- button images -->
      <images-modal
        :params="params" 
        :record="row"
      />
    </b-td>
  </b-tr>
</template>

<script>
import ModalRisk from '../../../process/risk/ModalRisk.vue'
import LegalModal from '../modals/LegalModal.vue'
import AcceptModal from '../modals/AcceptModal.vue'
import HelpModal from '../modals/HelpModal.vue'
import FindingModal from '../modals/FindingModal.vue'
import ImagesModal from '../modals/ImagesModal.vue'
import { getNoRequirementText, getRequirementText, getFieldRequirement } from '../../../components/scripts/texts'

export default {
  components: {
    ModalRisk,
    LegalModal,
    AcceptModal,
    HelpModal,
    FindingModal,
    ImagesModal
  },
  props: {
    params: {
      type: Object,
      required: true
    },
    row: {
      type: Object,
      required: true
    },
    variantRow: {
      type: String,
      required: false,
      default: ''
    },
    evaluateRisk: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    hasAudit() {
      return this.row.audit != null
    },
    noEvaluateRisk() {
      const isSubrequirement = this.row.id_subrequirement == null
      const hasSubrequirements = Boolean(this.row.requirement.has_subrequirement)
      return hasSubrequirements && isSubrequirement
    },
    evaluateRiskShow() {
      const isNegativeAnswer = this.hasAudit ? (this.row.audit.answer == 0) : false
      return this.evaluateRisk && isNegativeAnswer && !this.noEvaluateRisk
    }
  },
  methods: {
    getNoRequirementText,
    getRequirementText, 
    getFieldRequirement,
    successfully() {
      this.$emit('successfully')
    }
  },
}
</script>

<style>
  
</style>