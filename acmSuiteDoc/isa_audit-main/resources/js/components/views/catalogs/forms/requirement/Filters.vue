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
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <label>Tipo de Requerimiento</label>
        <v-select 
          v-model="filters.id_requirement_type"
          :options="requirementTypes"
          required
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
      <b-col sm="12" md="4">
        <label>Competencia</label>
        <v-select 
          v-model="filters.id_application_type"
          :options="applicationTypes"
          required
          label="application_type"
          :reduce="e => e.id_application_type"
          :clearable="true"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="4">
        <label>Evidencia</label>
        <v-select 
          v-model="filters.id_evidence"
          :options="evidences"
          required
          label="evidence"
          :reduce="e => e.id_evidence"
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
        <label>
          Tipo de Condición
        </label>
        <v-select 
          v-model="filters.id_condition"
          :options="conditionTypes"
          required
          label="condition"
          :reduce="e => e.id_condition"
          :clearable="true"
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
import { getStates, getCities, getEvidences, getApplicationTypes, getRequirementTypes, getConditions } from '../../../../../services/catalogService'

export default {
  async mounted() {
    await this.getForm()
    await this.getStates()
    await this.getEvidences()
    await this.getConditions()
    await this.getApplicationTypes()
    await this.getRequirementTypes()
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
      applicationTypes: [],
      requirementTypes: [],
      conditionTypes: [],
      evidences: [],
      states: [],
      cities: [],
      filters: {
        no_requirement: null,
        requirement: null,
        id_application_type: null,
        id_requirement_type: null,
        id_state: null,
        id_city: null,
        id_condition: null,
        id_evidence: null,
      },
    }
  },
  watch: {
    'filters.no_requirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.requirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_application_type': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_requirement_type': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_evidence': function() {
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
    'filters.id_condition': function() {
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
    async getApplicationTypes() {
      try {
        this.showLoadingMixin()
        const { data } = await getApplicationTypes()
        this.applicationTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getRequirementTypes() {
      try {
        this.showLoadingMixin()
        const params = { option: 'main' }
        const { data } = await getRequirementTypes(params)
        this.requirementTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getEvidences() {
      try {
        this.showLoadingMixin()
        const { data } = await getEvidences()
        this.evidences = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getConditions() {
      try {
        this.showLoadingMixin()
        const { data } = await getConditions()
        this.conditionTypes = data.data
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
  }
}
</script>

<style>

</style>