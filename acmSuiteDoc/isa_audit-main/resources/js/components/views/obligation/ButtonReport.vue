<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="success"
      v-b-tooltip.hover
      :title="titleButton"
      :class="{'float-right' : rightClass}"
      @click="getReportObligation"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>
  </fragment>
</template>

<script>
import { getReportObligation } from '../../../services/ObligationRegisterService'

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
    idObligationRegister: {
      type: Number,
      required: true
    },
    titleButton: {
      type: String,
      required: false,
      default: 'Reporte de Permisos Críticos'
    },
    rightClass: {
      type: Boolean,
      required: false,
      default: false,
    }
  },
  methods: {
    async getReportObligation() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getReportObligation(this.idAuditProcess, this.idAplicabilityRegister, this.idObligationRegister)
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