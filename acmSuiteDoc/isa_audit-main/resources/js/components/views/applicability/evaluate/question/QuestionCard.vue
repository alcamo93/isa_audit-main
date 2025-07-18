<template>
  <div class="d-flex flex-column">
    <div class="d-flex flex-column justify-content-center align-items-center">
      <p v-for="(evaluation, key, index) in evaluates" :key="index" class="badge badge-danger text-center text-white font-italic">
        <span v-if="evaluation.key != 'complete'">{{ evaluation.message }}</span>
      </p>
    </div>
    <b-row cols="1" cols-sm="1" cols-md="2" cols-lg="2" cols-xl="2" class="justify-content-center">
      <b-col class="d-flex flex-column">
        <div class="d-flex flex-wrap justify-content-center align-items-start">
          <span class="font-weight-bold font-italic mb-1">
            Pregunta
          </span>
        </div>
        <p class="text-justify">
          {{ question.question }}
        </p>
      </b-col>
      <b-col class="d-flex flex-column">
        
        <div class="d-flex flex-wrap justify-content-center align-items-center mx-1">
          <span class="font-weight-bold font-italic mb-1">
            Respuestas
          </span>
          <div class="d-flex flex-wrap justify-content-center align-items-center">
            <help-modal :question="question" />
            <article-modal :question="question" />
            <comment-modal 
              :idAuditProcess="idAuditProcess"
              :idAplicabilityRegister="idAplicabilityRegister"
              :idContractMatter="idContractMatter"
              :idContractAspect="idContractAspect"
              :idEvaluateQuestion="idEvaluateQuestion"
              :question="question" 
              :comment="comment" 
              @successfully="emitSuccessfully"
            />
          </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center align-items-start">
          <multiple-answer v-if="allowMultipleAnswers"
            :idAuditProcess="idAuditProcess"
            :idAplicabilityRegister="idAplicabilityRegister"
            :idContractMatter="idContractMatter"
            :idContractAspect="idContractAspect"
            :idEvaluateQuestion="idEvaluateQuestion"
            :options="answers"
            :applicability="applicability"
            :disabled="blockAnswers"
            @successfully="emitSuccessfully"
          />
          <single-answer v-else
            :idAuditProcess="idAuditProcess"
            :idAplicabilityRegister="idAplicabilityRegister"
            :idContractMatter="idContractMatter"
            :idContractAspect="idContractAspect"
            :idEvaluateQuestion="idEvaluateQuestion"
            :options="answers"
            :applicability="applicability"
            :disabled="blockAnswers"
            @successfully="emitSuccessfully"
          />
        </div>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import HelpModal from './HelpModal.vue'
import ArticleModal from './ArticleModal.vue'
import CommentModal from './CommentModal.vue'
import SingleAnswer from './SingleAnswer.vue'
import MultipleAnswer from './MultipleAnswer.vue'

export default {
  components: { 
    HelpModal, 
    ArticleModal, 
    CommentModal,
    SingleAnswer,
    MultipleAnswer
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true
    },
    idAplicabilityRegister: {
      type: Number,
      required: true
    },
    idContractMatter: {
      type: Number,
      required: true
    },
    idContractAspect: {
      type: Number,
      required: true
    },
    idEvaluateQuestion: {
      type: Number,
      required: true
    },
    evaluates: {
      type: Array,
      required: true,
    },
    question: {
      type: Object,
      required: true,
    },
    applicability: {
      type: Array,
      required: true,
    },
    comment: {
      type: String,
      required: true,
    }
  },
  computed: {
    allowMultipleAnswers() {
      return this.question.allow_multiple_answers == 1
    },
    answers() {
      return this.question.answers.map(item => ({ text: item.description, value: item.id_answer_question }))
    },
    blockAnswers() {
      const found = this.evaluates.find(item => item.key == 'blocked')
      return  found ? true : false
    }
  },
  methods: {
    emitSuccessfully() {
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>