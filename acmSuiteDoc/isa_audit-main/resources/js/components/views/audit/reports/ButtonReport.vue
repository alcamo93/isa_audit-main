<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="success"
      v-b-tooltip.hover
      :title="titleButton"
      class="mr-2"
      :class="{'float-right' : rightClass}"
      @click="getReportAudit"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>
  </fragment>
</template>

<script>
import { getReportAudit } from '../../../../services/AuditRegisterService'

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
      required: true
    },
    titleButton: {
      type: String,
      required: false,
      default: 'Reporte de Auditoría'
    },
    rightClass: {
      type: Boolean,
      required: false,
      default: false,
    }
  },
  methods: {
    async getReportAudit() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getReportAudit(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister)
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