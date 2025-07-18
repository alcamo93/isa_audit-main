<template>
  <b-form-group>
    <b-form-radio v-for="answer in options" 
      :key="answer.value" 
      v-model="value"
      :value="answer.value"
      :disabled="disabled"
      @change="setAnswerValue(answer.value)"
    >
      {{ answer.text }}
    </b-form-radio>
  </b-form-group>
</template>

<script>
import { setAnswerValue } from '../../../../../services/applicabilityService.js'

export default {
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
    options: {
      type: Array,
      required: true,
    },
    applicability: {
      type: Array,
      required: true
    },
    disabled: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      value: null
    }
  },
  computed: {
    answerValue() {
      return this.applicability.length ? this.applicability[0].id_answer_question : null
    }
  },
  watch: {
    applicability: {
      immediate: true,
      handler(newVal) {
        this.value = this.answerValue
      }
    }
  },
  methods: {
    async setAnswerValue(value) {
      try {
        this.showLoadingMixin()
        const form = [ { id_answer_question: value } ]
        const { data } = await setAnswerValue(this.idAuditProcess, this.idAplicabilityRegister, this.idContractMatter, this.idContractAspect, this.idEvaluateQuestion, form)
        this.responseMixin(data)
        this.showLoadingMixin()
        this.$emit('successfully')
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
        this.$emit('successfully')
      }
    },
  }
}
</script>
