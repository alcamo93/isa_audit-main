<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      class="btn-link"
      v-b-tooltip.hover.left 
      :title="titleTooltip"
      variant="info"
      @click="showModal"
    >
      <b-icon icon="check-square-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <rich-text-edit
          :initial-content="text"
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
import { getNoRequirementText } from '../../../components/scripts/texts'
import { getSingleRequirement, getSingleSubrequirement } from '../../../../../services/catalogSingleService'

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
      text: null
    }
  },
  computed: {
    isRequirement() {
      return this.record.subrequirement == null ? true : false
    },
    idRecord() {
      return this.record.subrequirement == null 
        ? this.record.requirement.id_requirement : this.record.subrequirement.id_subrequirement
    },
    getNameRecord() {
      const type = this.isRequirement ? 'requerimiento' : 'subrequerimiento'
      return `${type} ${getNoRequirementText(this.record)}`
    },
    titleModal() {
      if (this.record == null) return ''
      return `Criterio de aceptación de ${this.getNameRecord}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Ver criterio de aceptación de ${this.getNameRecord}`
    },
  },
  methods: {
    async showModal() {
      await this.getRecord()
      this.dialog = true
    },
    async getRecord() {
      try {
        this.showLoadingMixin()
        const switchService = this.isRequirement ? 
          getSingleRequirement(this.idRecord) : getSingleSubrequirement(this.idRecord)
        const { data } = await switchService
        this.text = data.data.acceptance
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style scoped>
.btn-link {
  padding: 3px;
}
</style>