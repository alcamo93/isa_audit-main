<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover.left
      :title="titleTooltip"
      class="btn-link go-to-process"
      @click="showModal"
    >
      {{ titleTruncateModal  }}
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <p class="font-weight-bold">Hallazgo:</p>
        <p class="text-justify"> {{ findingComputed }}</p>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import { getNoRequirementText, truncateText } from '../components/scripts/texts'

export default {
  props: {
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      const name = getNoRequirementText(this.record)
      return `No: ${name}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      const withFinding = `Mostrar Hallazgo para requerimiento No: ${getNoRequirementText(this.record)}`
      const withoutFinding = 'Requerimiento sin Hallazgo'
      return this.record.finding == null ? withoutFinding : withFinding
    },
    titleTruncateModal() {
      if (this.record == null) return ''
      const { finding } = this.record
      return truncateText(finding, 'Sin Hallazgo')
    },
    findingComputed() {
      if (this.record == null) return ''
      const { finding } = this.record
      return finding
    },
    disabledOpen() {
      if (this.record == null) return false
      const { finding } = this.record
      return finding == null
    }
  },
  methods: {
    async showModal() {
      if (this.disabledOpen) return 
      this.showLoadingMixin()      
      this.showLoadingMixin()
      this.dialog = true
    },
  }
}
</script>

<style>

</style>