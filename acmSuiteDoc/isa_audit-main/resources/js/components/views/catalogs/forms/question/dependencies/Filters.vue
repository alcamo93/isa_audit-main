<template>
  <fragment>
    <b-row class="mb-2">
      <b-col sm="12" md="12">
        <label>
          Pregunta
        </label>
        <div class="font-weight-bold">
          {{ questionString }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Tipo de Pregunta
        </label>
        <div class="font-weight-bold">
          {{ questionType }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Estado
        </label>
        <div class="font-weight-bold">
          {{ stateName }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Ciudad
        </label>
        <div class="font-weight-bold">
          {{ cityName }}
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <label>Respuesta</label>
        <b-form-input
          v-model="filters.description"
          placeholder="Búsqueda por Respuesta"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <label>Valor de Respuesta</label>
        <v-select 
          v-model="filters.id_answer_value"
          :options="optionValues"
          :reduce="e => e.id_answer_value"
          label="answer_value"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col class="d-flex justify-content-center align-items-center">
        <b-button class="mt-2" variant="info" size="lg"
          @click="alertRemove"
        >
          Remover todos los bloqueos
        </b-button>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { getAnswerValue } from '../../../../../../services/catalogService'
import { removeAnswerQuestionDependency } from '../../../../../../services/dependencyService'

export default {
  mounted() {
    this.getAnswerValue()
    this.reset()
  },
  props: {
    idForm: {
      type: Number,
      required: true
    },
    question: {
      type: Object,
      required: true,
      default: null
    },
    init: {
      type: Boolean,
      required: true,
      default: false
    }
  },
  data() {
    return {
      optionValues: [],
      filters: {
        id_answer_value: null,
        description: null,
      }
    }
  },
  watch: {
    'filters.id_answer_value': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.description': function() {
      this.$emit('fieldSelected', this.filters)
    },
    init(value) {
      if (value) this.getAnswerValue() 
    }
  },
  computed: {
    idQuestion() {
      return this.question.id_question
    },
    questionString() {
      const string = this.question.question
      return string
    },
    questionType() {
      const string = this.question.type.question_type
      return string
    },
    stateName() {
      if (this.question.state == null) return '---'
      const string = this.question.state.state
      return string
    },
    cityName() {
      if (this.question.city == null) return '---'
      const string = this.question.city.city
      return string
    },
    initFilters() {
      return this.init
    }
  },
  methods: {
    async getAnswerValue() {
      try {
        this.showLoadingMixin()
        const { data } = await getAnswerValue()
        this.optionValues = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async alertRemove() {
      try {
        const question = `¿Estás seguro de eliminar todas las preguntas bloqueadas de las respuestas de esta pregunta?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await removeAnswerQuestionDependency(this.idForm, this.idQuestion)
          this.responseMixin(data)
          this.$emit('successfully')
          this.showLoadingMixin()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.filters.id_answer_value = null
      this.filters.description = null
    }
  }
}
</script>

<style>

</style>