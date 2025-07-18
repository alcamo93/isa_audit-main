<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover.left
      :title="titleTooltip"
      class="btn btn-link go-to-process"
      @click="showModal"
    >
      <b-icon icon="question-circle-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <p class="font-weight-bold" v-if="hasDocument">
          <span>Evidencia: </span>
          {{ documentComputed }}
        </p>
        <h5 class="text-center font-weight-bold" v-if="!hasHelp">
          No cuenta con ayuda registrada
        </h5>
        <rich-text-edit v-else
          :initial-content="helpComputed"
          disabled
          onlyPresentation
        />
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
import RichTextEdit from "../components/rich_text/RichTextEdit.vue";
import { getNoRequirementText, getFieldRequirement } from '../components/scripts/texts'

export default {
  components: {
    RichTextEdit
  },
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
      return `Ayuda para requerimiento: No. ${name}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Ayuda para el auditor: No. ${getNoRequirementText(this.record)}`
    },
    hasDocument() {
      if (this.record == null) return ''
      return getFieldRequirement(this.record, 'document') != '----'
    },
    documentComputed() {
      if (this.record == null) return ''
      return getFieldRequirement(this.record, 'document')
    },
    hasHelp() {
      if (this.record == null) return false
      const hasRequirement = getFieldRequirement(this.record, 'help')
      return hasRequirement != null
    },
    helpComputed() {
      if (this.record == null) return ''
      return getFieldRequirement(this.record, 'help')
    },
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()      
      this.showLoadingMixin()
      this.dialog = true
    },
  }
}
</script>

<style>

</style>