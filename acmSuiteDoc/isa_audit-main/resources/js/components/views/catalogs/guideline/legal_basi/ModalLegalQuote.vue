<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-link 
      v-b-tooltip.hover.left 
      :title="titleTooltip"
      variant="info"
      @click="showModal"
    >
      <b-icon icon="file-richtext-fill" font-scale="1.5" aria-hidden="true"></b-icon>
    </b-link>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <rich-text-edit 
          :initial-content="legalQuote"
          :disabled="true"
          :onlyPresentation="true"
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
import RichTextEdit from '../../../components/rich_text/RichTextEdit'

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
      return `Cita Legal: ${this.record.legal_basis}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Ver Cita Legal: ${this.record.legal_basis}`
    },
    legalQuote() {
      if (this.record == null) return ''
      return this.record.legal_quote_env
    }
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