<template>
  <fragment>
    <b-row class="mb-2">
      <b-col sm="12" md="6">
        <label>
          Pregunta
        </label>
        <div class="font-weight-bold">
          {{ questionString }}
        </div>
      </b-col>
      <b-col sm="12" md="6">
        <label>
          Respuesta
        </label>
        <div class="font-weight-bold">
          {{ answerString }}
        </div>
      </b-col>
      <b-col sm="12" md="12">
        <label>
          Requerimiento
        </label>
        <div class="font-weight-bold">
          {{ requirementString }}
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
      <b-col sm="12" md="6">
        <label>No. Subrequerimiento</label>
        <b-form-input
          v-model="filters.no_subrequirement"
          placeholder="Búsqueda por No. Subrequerimiento"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <label>Subrequerimiento</label>
        <b-form-input
          v-model="filters.subrequirement"
          placeholder="Búsqueda por Subrequerimiento"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>Evidencia</label>
        <v-select 
          v-model="filters.id_evidence"
          :options="evidences"
          :reduce="e => e.id_evidence"
          label="evidence"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>Condición</label>
        <v-select 
          v-model="filters.id_condition"
          :options="conditions"
          :reduce="e => e.id_condition"
          label="condition"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>Asignados</label>
        <v-select 
          v-model="filters.has_relation"
          :options="optionRelations"
          :reduce="e => e.has_relation"
          label="option"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <b-button class="mt-2" :variant="allSelectedObserver ? 'warning': 'success'"
          @click="handlerAllRelation"
        >
          {{ `${allSelectedObserver ? 'Remover': 'Relacionar'} todos los subrequerimientos` }}
        </b-button>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { getConditions, getEvidences } from '../../../../../../../../services/catalogService'
import { setRelationAllSubrequirements } from '../../../../../../../../services/answerRequirementsService'

export default {
  mounted() {
    this.getConditions()
    this.getEvidences()
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
    answer: {
      type: Object,
      required: true,
      default: null
    },
    requirement: {
      type: Object,
      required: true,
      default: null
    },
    allSelected: {
      type: Boolean,
      required: true
    },
    init: {
      type: Boolean,
      required: true,
      default: false
    }
  },
  data() {
    return {
      evidences: [],
      conditions: [],
      optionRelations: [
        { has_relation: '0', option: 'Solo artículos no asignados' },
        { has_relation: '1', option: 'Solo artículos asignados' },
      ],
      filters: {
        no_subrequirement: null,
        subrequirement: null,
        id_evidence: null,
        id_condition: null,
        has_relation: null,
      }
    }
  },
  watch: {
    'filters.no_subrequirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.subrequirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_evidence': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_condition': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.has_relation': function() {
      this.$emit('fieldSelected', this.filters)
    },
  },
  computed: {
    idQuestion() {
      return this.question.id_question
    },
    idAnswerQuestion() {
      return this.answer.id_answer_question
    },
    questionType() {
      const string = this.question.type.question_type
      return string
    },
    questionString() {
      const string = this.question.question
      return string
    },
    answerString() {
      const string = this.answer.description
      return string
    },
    requirementString() {
      const {no_requirement, requirement} = this.requirement
      return `${no_requirement} - ${requirement}`
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
    },
    allSelectedObserver() {
      return this.allSelected
    }
  },
  methods: {
    async getConditions() {
      try {
        const { data } = await getConditions()
        this.conditions = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getEvidences() {
      try {
        const { data } = await getEvidences()
        this.evidences = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async handlerAllRelation() {
      try {
        this.showLoadingMixin()
        const request = {'selected': !this.allSelectedObserver}
        const { data } = await setRelationAllSubrequirements(this.idForm, this.idQuestion, this.answer.id_answer_question, this.requirement.id_requirement, request)
        this.$emit('successfully')
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.filters.no_subrequirement = null
      this.filters.subrequirement = null
      this.filters.id_evidence = null
      this.filters.id_condition = null
      this.filters.has_relation = null
    }
  }
}
</script>

<style>

</style>