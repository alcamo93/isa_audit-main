<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="success"
      :size="size"
      v-b-tooltip.hover
      :title="titleButton"
      :class="{'float-right' : rightClass}"
      @click="getReportCorporateObligation"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>
  </fragment>
</template>

<script>
import { getReportCorporateObligation } from '../../../../services/reportDashboardService'

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
    obligationRegisterId: {
      type: Number,
      required: true,
    },
    titleButton: {
      type: String,
      required: false,
      default: 'Reporte Global de Permisos Críticos'
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
    async getReportCorporateObligation() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getReportCorporateObligation(this.idAuditProcess, this.idAplicabilityRegister, this.obligationRegisterId)
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