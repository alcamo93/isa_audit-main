<template>
  <fragment>
    <corporate-obligation-section 
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :obligation-register-id="obligationRegisterId"
    />
    <corporate-action-plan-section 
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :obligation-register-id="obligationRegisterId"
    />
  </fragment>
</template>

<script>
import CorporateObligationSection from './sections/CorporateObligationSection'
import CorporateActionPlanSection from './sections/CorporateActionPlanSection'

export default {
  components: {
    CorporateObligationSection,
    CorporateActionPlanSection
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
    obligationRegisterId: {
      type: Number,
      required: true,
    },
  },
}
</script>

<style>

</style>