<template>
  <fragment>
    <corporate-compliance-section 
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :id-audit-register="idAuditRegister"
    />
    <corporate-compliance-action-plan-section 
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :id-audit-register="idAuditRegister"
    />
  </fragment>
</template>

<script>
import CorporateComplianceSection from './sections/CorporateComplianceSection'
import CorporateComplianceActionPlanSection from './sections/CorporateComplianceActionPlanSection'

export default {
  components: {
    CorporateComplianceSection,
    CorporateComplianceActionPlanSection
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
}
</script>

<style>

</style>