<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover.leftbottom
      title="Mostrar evaluación"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="eye-fill" aria-hidden="true"></b-icon> 
    </b-button>
    
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
      hide-footer
    >
      <b-container fluid>
        <data-sheet :record="record" />
      </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import DataSheet from './DataSheet'
import { getProcess } from '../../../../services/processService'

export default {
  components: {
    DataSheet
  },
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      dialog: false,
      loading: false,
      record: null,
    }
  },
  computed: {
    titleModal() {
      return `Detalles de evaluación: ${this.record?.audit_processes ?? ''}`
    },
  },
  methods: {
    async showModal() {
      try {
        this.showLoadingMixin()
        const { data } = await getProcess(this.id)
        this.record = data.data
        this.showLoadingMixin()
        this.dialog = true
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style></style>