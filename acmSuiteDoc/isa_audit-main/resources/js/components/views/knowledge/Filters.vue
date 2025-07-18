<template>
  <div class="mb-2">
    <loading :show="loadingMixin" />
    <b-row>
      <b-col sm="12" md="6">
        <label>Marco jurídico (Ley, Reglamento, Norma, etc)</label>
        <b-form-input v-model="filters.guideline" placeholder="Búsqueda por Marco jurídico"
          debounce="500"></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <label>Siglas</label>
        <b-form-input v-model="filters.initials_guideline" placeholder="Búsqueda por siglas"
          debounce="500"></b-form-input>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Clasificación</label>
          <v-select v-model="filters.id_legal_classification" :options="classifications" required
            label="legal_classification" :reduce="e => e.id_legal_c" :clearable="true" placeholder="Todos"
            :append-to-body="true">
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Competencia</label>
          <v-select v-model="filters.id_application_type" :options="application_types" required label="application_type"
            :reduce="e => e.id_application_type" :clearable="true" placeholder="Todos" :append-to-body="true">
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Estado
          </label>
          <v-select v-model="filters.id_state" :options="states" :reduce="e => e.id_state" label="state"
            placeholder="Todos" :append-to-body="true">
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Ciudad
          </label>
          <v-select v-model="filters.id_city" :options="cities" :reduce="e => e.id_city" label="city"
            placeholder="Todos" :append-to-body="true">
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { getStates, getCities, getApplicationTypes, getLegalClassifications } from '../../../services/catalogService'

export default {
  async mounted() {
    await this.getStates()
    await this.getApplicationTypes()
    await this.getLegalClassifications()
  },
  data() {
    return {
      classifications: [],
      application_types: [],
      states: [],
      cities: [],
      filters: {
        guideline: null,
        initials_guideline: null,
        id_legal_classification: null,
        id_application_type: null,
        id_state: null,
        id_city: null
      },
    }
  },
  watch: {
    'filters.guideline': function () {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.initials_guideline': function () {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_legal_classification': function () {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_application_type': function () {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_state': function (value) {
      this.cities = []
      if (value != null) this.getCities()
      this.filters.id_city = null
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_city': function () {
      this.$emit('fieldSelected', this.filters)
    },
  },
  methods: {
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
    async getApplicationTypes() {
      try {
        this.showLoadingMixin()
        const { data } = await getApplicationTypes()
        this.application_types = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
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
  }
}
</script>