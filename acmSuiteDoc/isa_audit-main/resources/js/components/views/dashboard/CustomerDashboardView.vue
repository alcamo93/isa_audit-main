<template>
  <fragment>
    <global-obligation-section
      :id-customer="idCustomer"
    />
    <historical-obligation-section
      :id-customer="idCustomer"
    />
    <global-audit-section
      :id-customer="idCustomer"
    />
    <historical-audit-section
      :id-customer="idCustomer"
    />
    <global-compliance-section
      :id-customer="idCustomer"
    />
    <historical-compliance-section
      :id-customer="idCustomer"
    />
  </fragment>
</template>

<script>
import GlobalObligationSection from './sections/GlobalObligationSection'
import HistoricalObligationSection from './sections/HistoricalObligationSection'
import GlobalAuditSection from './sections/GlobalAuditSection'
import HistoricalAuditSection from './sections/HistoricalAuditSection'
import GlobalComplianceSection from './sections/GlobalComplianceSection'
import HistoricalComplianceSection from './sections/HistoricalComplianceSection'

export default {
  components: {
    GlobalObligationSection,
    HistoricalObligationSection,
    GlobalAuditSection,
    HistoricalAuditSection,
    GlobalComplianceSection,
    HistoricalComplianceSection,
  },
  props: {
    idCustomer: {
      type: Number,
      required: true,
    }
  },
}
</script>

<style>

</style>