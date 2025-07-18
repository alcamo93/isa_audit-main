<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="success"
      v-b-tooltip.hover
      :title="titleButton"
      class="mr-2"
      :class="{'float-right' : rightClass}"
      @click="getResultsReportApplicability"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>
  </fragment>
</template>

<script>
import { getResultsReportApplicability } from '../../../../services/AplicabilityRegisterService'

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
    titleButton: {
      type: String,
      required: false,
      default: 'Resultados'
    },
    rightClass: {
      type: Boolean,
      required: false,
      default: false,
    }
  },
  methods: {
    async getResultsReportApplicability() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getResultsReportApplicability(this.idAuditProcess, this.idAplicabilityRegister)
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