<template>
  <div class="mb-2">
    <loading :show="loadingMixin" />
    <b-row>
      <b-col sm="12" md="4">
        <label>
          Formulario
        </label>
        <div class="font-weight-bold">
          {{ formName }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Materia
        </label>
        <div class="font-weight-bold">
          {{ matterName }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Aspecto
        </label>
        <div class="font-weight-bold">
          {{ aspectName }}
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="6">
        <label>Pregunta</label>
        <b-form-input
          v-model="filters.question"
          placeholder="Búsqueda por Pregunta"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <label>Tipo de Pregunta</label>
        <v-select 
          v-model="filters.id_question_type"
          :options="questionTypes"
          required
          label="question_type"
          :reduce="e => e.id_question_type"
          :clearable="true"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <b-form-group>
         <label>Estatus</label>
          <v-select 
            v-model="filters.id_status"
            :options="status"
            :reduce="e => e.id_status"
            label="status"
            placeholder="Todos"
            :append-to-body="true"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Estado
        </label>
        <v-select 
          v-model="filters.id_state"
          :options="states"
          :reduce="e => e.id_state"
          label="state"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Ciudad
        </label>
        <v-select 
          v-model="filters.id_city"
          :options="cities"
          :reduce="e => e.id_city"
          label="city"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { getForm } from '../../../../../services/FormService'
import { getStates, getCities, getQuestionTypes, getStatus } from '../../../../../services/catalogService'

export default {
  async mounted() {
    await this.getForm()
    await this.getStatus()
    await this.getStates()
    await this.getQuestionTypes()
  },
  props: {
    idForm: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      form: null,
      questionTypes: [],
      status: [],
      states: [],
      cities: [],
      filters: {
        question: null,
        id_question_type: null,
        id_state: null,
        id_city: null,
        id_status: null,
      },
    }
  },
  watch: {
    'filters.question': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_question_type': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_state': function(value) {
      this.cities = []
      if (value != null) this.getCities()
      this.filters.id_city = null 
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_city': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_status': function() {
      this.$emit('fieldSelected', this.filters)
    },
  },
  computed: {
    formName() {
      if (this.form == null) return ''
      const { name } = this.form
      return name
    },
    matterName() {
      if (this.form == null) return ''
      const { matter } = this.form.matter
      return matter
    },
    aspectName() {
      if (this.form == null) return ''
      const { aspect } = this.form.aspect
      return aspect
    },
  },
  methods: {
    async getForm() {
      try {
        this.showLoadingMixin()
        const { data } = await getForm(this.idForm)
        this.form = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getQuestionTypes() {
      try {
        this.showLoadingMixin()
        const params = { option: 'main' }
        const { data } = await getQuestionTypes(params)
        this.questionTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getStates() {
      try {
        this.showLoadingMixin()
        const { data } = await getStates()
        this.states = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getCities() {
      try {
        this.showLoadingMixin()
        const filters = { id_state: this.filters.id_state }
        const { data } = await getCities({}, filters)
        this.cities = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getStatus() {
      try {
        const filters = { group: 1 }
        const { data } = await getStatus({}, filters)
        this.status = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>