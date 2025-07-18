<template>
  <fragment>
    <corporate-audit-section
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :id-audit-register="idAuditRegister"
    />
    <!-- <corporate-audit-action-plan-section 
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :id-audit-register="idAuditRegister"
    /> -->
  </fragment>
</template>

<script>
import CorporateAuditSection from './sections/CorporateAuditSection'
// import CorporateAuditActionPlanSection from './sections/CorporateAuditActionPlanSection'


export default {
  components: {
    CorporateAuditSection,
    // CorporateAuditActionPlanSection
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