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
  import { getContentImage } from '../../../../../../services/imageService'
  
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
        await this.getContentImage()   
        this.dialog = true
      },
      async getContentImage() {
        try {
          this.showLoadingMixin()
          const { data, headers } = await getContentImage(this.record.id)
          const fileName = headers.filename
          const blob = new Blob([data])
          const url = window.URL.createObjectURL(blob)
          const a = document.createElement('a')
          a.href = url
          a.download = fileName
          a.click()
          window.URL.revokeObjectURL(url)
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