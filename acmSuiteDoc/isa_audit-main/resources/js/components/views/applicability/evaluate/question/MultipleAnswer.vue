<template>
  <b-form-group>
    <b-form-checkbox-group
      :options="options"
      v-model="values"
      stacked
      :disabled="disabled"
      @change="setAnswerValue"
    >
    </b-form-checkbox-group>
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
      values: null
    }
  },
  computed: {
    answerValue() {
      return this.applicability.map(item => item.id_answer_question)
    }
  },
  watch: {
    applicability: {
      immediate: true,
      handler(newVal) {
        this.values = this.answerValue
      }
    }
  },
  methods: {
    async setAnswerValue(values) {
      try {
        this.showLoadingMixin()
        const form = values.map(item => ({ id_answer_question: item }))
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

<style>

</style>