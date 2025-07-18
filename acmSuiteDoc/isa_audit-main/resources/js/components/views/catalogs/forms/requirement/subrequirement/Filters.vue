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
      <b-col sm="12" md="4">
        <label>
          Requerimiento
        </label>
        <div class="font-weight-bold">
          {{ requirementName }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Tipo de Requerimiento
        </label>
        <div class="font-weight-bold">
          {{ requirementType }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Competencia
        </label>
        <div class="font-weight-bold">
          {{ applicationType }}
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
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <label>Tipo de Subrequerimiento</label>
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
      <b-col sm="12" md="4">
        <label>
          Tipo de condición
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
    </b-row>
  </div>
</template>

<script>
import { getEvidences, getRequirementTypes, getConditions } from '../../../../../../services/catalogService'
import { getRequirement } from '../../../../../../services/requirementsService'

export default {
  async mounted() {
    await this.getRequirement()
    await this.getEvidences()
    await this.getConditions()
    await this.getRequirementTypes()
  },
  props: {
    idForm: {
      type: Number,
      required: true
    },
    idRequirement: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      requirement: null,
      requirementTypes: [],
      conditionTypes: [],
      evidences: [],
      filters: {
        no_subrequirement: null,
        subrequirement: null,
        id_requirement_type: null,
        id_condition: null,
        id_evidence: null,
      },
    }
  },
  watch: {
    'filters.no_subrequirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.subrequirement': function() {
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
  },
  computed: {
    formName() {
      if (this.requirement == null) return ''
      const { name } = this.requirement.form
      return name
    },
    matterName() {
      if (this.requirement == null) return ''
      const { matter } = this.requirement.matter
      return matter
    },
    aspectName() {
      if (this.requirement == null) return ''
      const { aspect } = this.requirement.aspect
      return aspect
    },
    requirementName() {
      if (this.requirement == null) return ''
      const { no_requirement, requirement } = this.requirement
      return `${no_requirement} ${requirement}`
    },
    requirementType() {
      if (this.requirement == null) return ''
      const { requirement_type } = this.requirement.requirement_type
      return requirement_type
    },
    applicationType() {
      if (this.requirement == null) return ''
      const { application_type } = this.requirement.application_type
      return application_type
    }
  },
  methods: {
    async getRequirement() {
      try {
        this.showLoadingMixin()
        const { data } = await getRequirement(this.idForm, this.idRequirement)
        this.requirement = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getRequirementTypes() {
      try {
        this.showLoadingMixin()
        const params = { option: 'sub', idRequirement: this.idRequirement }
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
  }
}
</script>

<style>

</style>