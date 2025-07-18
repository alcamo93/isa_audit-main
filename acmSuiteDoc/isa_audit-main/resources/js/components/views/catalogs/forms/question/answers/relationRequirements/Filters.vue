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
        <label>No. Requerimiento</label>
        <b-form-input
          v-model="filters.no_requirement"
          placeholder="Búsqueda por No. Requerimiento"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <label>Requerimiento</label>
        <b-form-input
          v-model="filters.requirement"
          placeholder="Búsqueda por Requerimiento"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>Tipo de Requerimiento</label>
        <v-select 
          v-model="filters.id_requirement_type"
          :options="requirementTypes"
          label="requirement_type"
          :reduce="e => e.id_requirement_type"
          :clearable="true"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
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
    </b-row>
  </fragment>
</template>

<script>
import { getRelationRequirementTypes } from '../../../../../../../services/answerRequirementsService'
import { getConditions, getEvidences } from '../../../../../../../services/catalogService'

export default {
  mounted() {
    this.getConditions()
    this.getEvidences()
    this.getRequirementType();
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
    init: {
      type: Boolean,
      required: true,
      default: false
    }
  },
  data() {
    return {
      requirementTypes: [],
      evidences: [],
      conditions: [],
      optionRelations: [
        { has_relation: '0', option: 'Solo artículos no asignados' },
        { has_relation: '1', option: 'Solo artículos asignados' },
      ],
      filters: {
        no_requirement: null,
        requirement: null,
        id_requirement_type: null,
        id_evidence: null,
        id_condition: null,
        has_relation: null,
      }
    }
  },
  watch: {
    'filters.no_requirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.requirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_requirement_type': function() {
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
    init(value) {
      if (value) this.getRequirementType() 
    }
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
    async getRequirementType() {
      try {
        this.showLoadingMixin()
        const { data } = await getRelationRequirementTypes(this.idForm, this.idQuestion, this.idAnswerQuestion)
        this.requirementTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.filters.no_requirement = null
      this.filters.requirement = null
      this.filters.id_requirement_type = null
      this.filters.id_evidence = null
      this.filters.id_condition = null
      this.filters.has_relation = null
    }
  }
}
</script>

<style>

</style>