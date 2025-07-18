<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="primary"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="download" aria-hidden="true"></b-icon>
    </b-button>    
  </fragment>
</template>

<script>
import { getContentFile } from '../../../../../services/fileService'

export default {
  props: {
    record: {
      type: Object,
      required: true,
    },
  },
  computed: {
    titleTooltip() {
      return 'Descargar Evidencia/documento cargado'
    },
  },
  methods: {
    async showModal() { 
      await this.getContentFile()   
      this.dialog = true
    },
    async getContentFile() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await getContentFile(this.record.id)
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