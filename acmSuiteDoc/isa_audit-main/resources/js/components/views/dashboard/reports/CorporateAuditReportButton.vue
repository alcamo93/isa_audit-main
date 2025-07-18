<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="success"
      :size="size"
      v-b-tooltip.hover
      :title="titleButton"
      :class="{'float-right' : rightClass}"
      @click="getReportCorporateAudit"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>
  </fragment>
</template>

<script>
import { getReportCorporateAudit } from '../../../../services/reportDashboardService'

export default {
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
    },
    titleButton: {
      type: String,
      required: false,
      default: 'Reporte Global de Auditoría'
    },
    size: {
      type: String,
      required: false,
      default: 'sm'
    },
    rightClass: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  methods: {
    async getReportCorporateAudit() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getReportCorporateAudit(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister)
        this.responseFileMixin(data, headers)
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