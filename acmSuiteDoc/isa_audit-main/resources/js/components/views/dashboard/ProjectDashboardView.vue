<template>
  <fragment>
    <corporate-dashboard-obligation-view v-if="!emptyObligation"
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :obligation-register-id="obligationRegisterId"
    />
    <div class="d-flex" v-else>
      <b-card class="flex-fill text-center">
        <h5>No hay Permisos Críticos para mostrar aún</h5>
      </b-card>
    </div>
    <corporate-dashboard-audit-view v-if="!emptyAudit"
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :id-audit-register="idAuditRegister"
    />
    <div class="d-flex" v-else>
      <b-card class="flex-fill text-center">
        <h5>No hay Auditorías para mostrar aún</h5>
      </b-card>
    </div>
    <corporate-dashboard-compliance-view v-if="!emptyAudit"
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :id-audit-register="idAuditRegister"
    />
    <div class="d-flex" v-else>
      <b-card class="flex-fill text-center">
        <h5>No hay Cumplimiento EHS para mostrar aún</h5>
      </b-card>
    </div>

  </fragment>
</template>

<script>
import CorporateDashboardObligationView from './CorporateDashboardObligationView'
import CorporateDashboardAuditView from './CorporateDashboardAuditView'
import CorporateDashboardComplianceView from './CorporateDashboardComplianceView'
import { getCorporateGlobal }  from '../../../services/dashboardService'

export default {
  components: {
    CorporateDashboardObligationView,
    CorporateDashboardAuditView,
    CorporateDashboardComplianceView
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
  },
  async created() {
    await this.getCorporateGlobal()
  },
  computed: {
    emptyAudit() {
      return this.idAuditRegister == null
    },
    emptyObligation() {
      return this.obligationRegisterId == null
    }
  },
  data() {
    return {
      idAuditRegister: null,
      obligationRegisterId: null
    }
  },
  methods: {
    async getCorporateGlobal() {
      try {
        this.showLoadingMixin()
        const { data } = await getCorporateGlobal(this.idAuditProcess, this.idAplicabilityRegister)
        const { current_audit, aplicability_register } = data.data
        
        if ( current_audit ) {
          this.idAuditRegister = current_audit.id_audit_register 
          this.obligationRegisterId = aplicability_register.obligation_register.id
        }
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>