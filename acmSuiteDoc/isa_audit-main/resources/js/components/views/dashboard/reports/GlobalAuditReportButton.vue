<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="success"
      :size="size"
      v-b-tooltip.hover
      :title="titleButton"
      :class="{'float-right' : rightClass}"
      @click="getReportGlobalAudit"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>
  </fragment>
</template>

<script>
import { getReportGlobalAudit } from '../../../../services/reportDashboardService'

export default {
  props: {
    idCustomer: {
      type: Number,
      required: true
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
    async getReportGlobalAudit() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getReportGlobalAudit(this.idCustomer)
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