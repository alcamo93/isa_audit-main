<template>
  <div class="mb-2">
    <loading :show="loadingMixin" />
    <filter-customers 
      ref="customersRef"
      @fieldSelected="getCustomers"
    />
    <b-row>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Materia</label>
          <v-select 
            v-model="filters.id_matter"
            :options="matters"
            required
            label="matter"
            :reduce="e => e.id_matter"
            :clearable="true"
            placeholder="Todos"
            :append-to-body="true"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Aspecto</label>
          <v-select 
            v-model="filters.id_aspect"
            :options="aspects"
            required
            label="aspect"
            :reduce="e => e.id_aspect"
            :clearable="true"
            placeholder="Todos"
            :append-to-body="true"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <label>No. Requerimiento</label>
        <b-form-input
          v-model="filters.no_requirement"
          placeholder="Búsqueda por No. Requerimiento"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <label>Requerimiento</label>
        <b-form-input
          v-model="filters.requirement"
          placeholder="Búsqueda por Requerimiento"
          debounce="500"
        ></b-form-input>
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
    </b-row>
  </div>
</template>

<script>
import FilterCustomers from '../components/customers/FilterCustomers.vue'
import { getApplicationTypesSpecific, getMatters, getAspects } from '../../../services/catalogService'

export default {
  components: {
    FilterCustomers
  },
  async mounted() {
    await this.getMatters()
    await this.getApplicationTypesSpecific()
  },
  data() {
    return {
      applicationTypes: [],
      matters: [],
      aspects: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        id_matter: null,
        id_aspect: null,
        no_requirement: null,
        requirement: null,
        id_application_type: null
      },
    }
  },
  watch: {
    'filters.id_customer': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_corporate': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.no_requirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.requirement': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_application_type': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_matter': function(value) {
      this.filters.id_aspect = null
      this.aspects = []
      if (value != null) {
        this.getAspects()
      }
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_aspect': function() {
      this.$emit('fieldSelected', this.filters)
    }
  },
  methods: {
    async getApplicationTypesSpecific() {
      try {
        this.showLoadingMixin()
        const { data } = await getApplicationTypesSpecific()
        this.applicationTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getMatters() {
      try {
        this.showLoadingMixin()
        const { data } = await getMatters()
        this.matters = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getAspects() {
      try {
        this.showLoadingMixin()
        const filters = { id_matter: this.filters.id_matter }
        const { data } = await getAspects({}, filters)
        this.aspects = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getCustomers({ id_customer, id_corporate }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
    },
  }
}
</script>

<style>

</style>