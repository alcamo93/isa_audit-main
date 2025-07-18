<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      class="mb-0 p-1"
      v-b-tooltip.hover.top 
      title="Fundamentos legales"
      variant="link"
      @click="showModal"
    >
      <b-icon icon="journal-bookmark-fill" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      no-close-on-backdrop
    >
      <b-container fluid>
        <p class="font-weight-bold font-italic">{{ this.questionText }}</p>
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
import { getQuestion } from '../../../../../services/catalogSingleService'

export default {
  components: {
    RichTextEdit
  },
  props: {
    question: {
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
    idRecord() {
      return this.question.id_question
    },
    questionText() {
      return this.question.question
    },
    titleModal() {
      return `Fundamentos legales`
    },
    titleTooltip() {
      return `Ver fundamentos legales`
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
        const { data } = await getQuestion(this.idRecord, params)
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

<style></style>