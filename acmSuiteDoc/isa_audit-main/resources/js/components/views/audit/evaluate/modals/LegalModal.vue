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
      <b-icon icon="journal-bookmark-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <div class="mt-2 mb-2" v-for="article in articles" :key="article.id_legal_basis">
          <h6 class="font-weight-bold">{{ article.legal_basis }}</h6>
          <h5 class="font-weight-bold">{{ article.guideline.guideline }}</h5>
          <rich-text-edit class="mt-2 mb-2"
            :initial-content="article.legal_quote_env"
            :disabled="true"
            :onlyPresentation="true"
          />
        </div>
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
      articles: []
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
      return getNoRequirementText(this.record)
    },
    titleModal() {
      if (this.record == null) return ''
      return `Fundamentos legales: ${this.getNameRecord}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Ver fundamentos legales: ${this.getNameRecord}`
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
        const params = { included: 'articles.guideline' }
        const switchService = this.isRequirement ? 
          getSingleRequirement(this.idRecord, params) : getSingleSubrequirement(this.idRecord, params)
        const { data } = await switchService
        this.articles = data.data.articles
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