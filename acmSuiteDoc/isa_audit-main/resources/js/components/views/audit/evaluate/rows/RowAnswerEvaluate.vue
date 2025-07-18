<template>
  <b-tr>
    <b-td class="text-center" colspan="5" >
      <b-form-group label="Respuestas" v-slot="{ ariaDescribedby }">
        <b-form-radio-group
          v-model="answerComputed"
          :options="options"
          @change="setAuditAnswer"
          :disabled="disabledAnswer"
          :aria-describedby="ariaDescribedby"
        />
      </b-form-group>
    </b-td>
  </b-tr>
</template>

<script>
import { setAuditAnswer } from '../../../../../services/AuditService'
export default {
  props: {
    params: {
      type: Object,
      required: true
    },
    row: {
      type: Object,
      required: true
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    recursiveAnswer: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data() {
    return {
      form: {
        answer: null,
        recursive: false
      },
      options: [
        { text: 'No cumple', value: '0' },
        { text: 'Cumple', value: '1' },
        { text: 'No aplica', value: '2' }
      ],
    }
  },
  computed: {
    noRequirement() {
      return this.row.requirement.no_requirement
    },
    disabledAnswer() {
      const isSubrequirement = this.row.id_subrequirement == null
      const hasSubrequirements = Boolean(this.row.requirement.has_subrequirement)
      return this.disabled || (hasSubrequirements && isSubrequirement)
    },
    answerComputed: {
      get: function () {
        if (this.row.audit == null) return null
        const { answer } = this.row.audit
        return answer
      },
      // setter
      set: function (newValue) {
        this.form.answer = newValue
      }
    }
  },
  methods: {
    async setAuditAnswer() {
      try {
        const answerNoApply = this.form.answer == 2
        if (this.recursiveAnswer && answerNoApply) {
          const question = `¿Deseas establecer todos los subrequerimientos como "No aplica"?`
          const text = `Solo los subrequerimientos que pertenecen al requerimiento ${this.noRequirement} serán "No aplica"`
          const { value } = await this.alertQuestionMixin(question, text, 'question')
          this.form.recursive = value != undefined
        }
        this.showLoadingMixin()
        const { idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect } = this.params
        const { data } = await setAuditAnswer(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, this.row.id, this.form)
        this.$emit('successfully')
        this.responseMixin(data)
        this.showLoadingMixin()
      } catch (error) { 
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style scoped>
  .card label {
    text-transform: capitalize !important;
  }
</style>