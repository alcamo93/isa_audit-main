<template>
  <fragment>
    <b-row class="mb-2">
      <b-col sm="12" md="3">
        <label>
          Tipo de Requerimento
        </label>
        <div class="font-weight-bold">
          {{ requirementType }}
        </div>
      </b-col>
      <b-col sm="12" md="3">
        <label>
          Competencia
        </label>
        <div class="font-weight-bold">
          {{ applicationType }}
        </div>
      </b-col>
      <b-col sm="12" md="3">
        <label>
          Estado
        </label>
        <div class="font-weight-bold">
          {{ stateName }}
        </div>
      </b-col>
      <b-col sm="12" md="3">
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
        <label>Clasificación</label>
        <v-select 
          v-model="filters.id_legal_classification"
          :options="classifications"
          label="legal_classification"
          :reduce="e => e.id_legal_c"
          :clearable="true"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="6">
        <label>Marco jurídico (Ley, Reglamento, Norma, etc)</label>
        <v-select 
          v-model="filters.id_guideline"
          :options="guidelines"
          :reduce="e => e.id_guideline"
          label="guideline"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
      <b-col sm="12" md="4">
        <label>Artículo</label>
        <b-form-input
          v-model="filters.legal_basis"
          placeholder="Búsqueda por Artículo"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <label>Cita Legal</label>
        <b-form-input
          v-model="filters.legal_quote"
          placeholder="Búsqueda por Cita Legal"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
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
import { getRelationGuideline } from '../../../../../../../services/subrequirementLegalsService'
import { getLegalClassifications } from '../../../../../../../services/catalogService'

export default {
  mounted() {
    this.getLegalClassifications()
    this.getGuidelines();
    this.reset()
  },
  props: {
    idForm: {
      type: Number,
      required: true,
    },
    idRequirement: {
      type: Number,
      required: true,
    },
    record: {
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
      guidelines: [],
      classifications: [],
      optionRelations: [
        { has_relation: '0', option: 'Solo artículos no asignados' },
        { has_relation: '1', option: 'Solo artículos asignados' },
      ],
      filters: {
        id_guideline: null,
        legal_basis: null,
        legal_quote: null,
        has_relation: null,
        id_legal_classification: null,
      }
    }
  },
  watch: {
    'filters.id_guideline': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.legal_basis': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.legal_quote': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.has_relation': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_legal_classification': function() {
      this.filters.id_guideline = null
      this.getGuidelines()
      this.$emit('fieldSelected', this.filters)
    },
    init(value) {
      if (value) this.getGuidelines() 
    }
  },
  computed: {
    idSubrequirement() {
      return this.record.id_subrequirement
    },
    requirementType() {
      const string = this.record.requirement_type.requirement_type
      return string
    },
    applicationType() {
      const string = this.record.application_type.application_type
      return string
    },
    stateName() {
      if (this.record.state == null) return '---'
      const string = this.record.state.state
      return string
    },
    cityName() {
      if (this.record.city == null) return '---'
      const string = this.record.city.city
      return string
    },
    initFilters() {
      return this.init
    }
  },
  methods: {
    async getLegalClassifications() {
      try {
        this.showLoadingMixin()
        const { data } = await getLegalClassifications()
        this.classifications = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getGuidelines() {
      try {
        this.showLoadingMixin()
        const filters = { id_legal_classification: this.filters.id_legal_classification}
        const { data } = await getRelationGuideline(this.idForm, this.idRequirement, this.idSubrequirement, {}, filters)
        this.guidelines = data.data.map(item => {
          return {
            id_guideline: item.id_guideline,
            guideline: `${item.guideline} (${item.initials_guideline})`
          }
        })
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.filters.id_guideline = null
      this.filters.legal_basis = null
      this.filters.legal_quote = null
      this.filters.has_relation = null
      this.filters.id_legal_classification = null
    }
  }
}
</script>

<style>

</style>