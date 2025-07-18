<template>
  <fragment>

    <b-button 
      class="mb-0 p-1"
      v-b-tooltip.hover.top 
      title="Ayuda"
      variant="link"
      @click="showModal"
    >
      <b-icon icon="question-circle-fill" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="xl"
      :title="'Ayuda para pgunta'"
      no-close-on-backdrop
    >
      <b-container fluid>
        <p class="font-weight-bold font-italic">{{ this.questionText }}</p>
        <rich-text-edit
          :initial-content="helpQuestion"
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
import RichTextEdit from '../../../components/rich_text/RichTextEdit.vue'

export default {
  props: {
    question: {
      type: Object,
      required: true
    }
  },
  components: { 
    RichTextEdit 
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    questionText() {
      return this.question.question
    },
    helpQuestion() {
      return this.question.help_question_env
    }
  },
  methods: {
    showModal() {
      this.dialog = true
    }
  }
}
</script>

<style>

</style>