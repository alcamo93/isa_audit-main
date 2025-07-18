<template>
  <fragment>
    <filter-customers 
      ref="customersRef"
      @fieldSelected="getCustomers"
    />
    <b-row>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Nombre de evaluación
          </label>
          <v-select 
            v-model="filters.id_audit_processes"
            :options="processList"
            label="audit_processes"
            :reduce="e => e.id_audit_processes"
            append-to-body
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4">
        <label>Nombre de Archivo</label>
        <b-form-input
          v-model="filters.name"
          placeholder="Búsqueda por Nombre de Archivo"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Clasificación
          </label>
          <v-select 
            v-model="filters.id_category"
            :options="categories"
            :reduce="e => e.id_category"
            label="category"
            append-to-body
            placeholder="Todos"
          >
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
            Materia
          </label>
          <v-select 
            append-to-body
            v-model="filters.id_matter"
            :options="matters"
            :reduce="e => e.id_matter"
            label="matter"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Aspecto
          </label>
          <v-select 
            append-to-body
            v-model="filters.id_aspect"
            :options="aspects"
            :reduce="e => e.id_aspect"
            label="aspect"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import FilterCustomers from '../components/customers/FilterCustomers'
import { getCategories, getProcessSource } from '../../../services/catalogService'

export default {
  components: {
    FilterCustomers
  },
  mounted() {
    this.getCategories()
  },
  data() {
    return {
      process: null,
      processList: [],
      categories: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        id_audit_processes: null,
        id_category: null,
        name: '',
        id_matter: null,
        id_aspect: null
      }
    }
  },
  watch: {
    'filters.id_customer': function(value) {
      this.filters.id_corporate = null
      this.filters.id_audit_processes = null
      this.process = null
      this.filters.id_matter = null
      this.filters.id_aspect = null
      this.getListProcess()
    },
    'filters.id_corporate': function(value) {
      this.filters.id_audit_processes = null
      this.process = null
      this.filters.id_matter = null
      this.filters.id_aspect = null
      this.getListProcess()
    },
    'filters.id_audit_processes': function(value) {
      if (value == null) {
        this.process = null
        this.$emit('filterSelected', this.filters)
        return 
      }
      this.process = this.processList.find(item => item.id_audit_processes == value) ?? null
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_category': function(value) {
      this.$emit('filterSelected', this.filters)
    },
    'filters.name': function(value) {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_matter': function(value) {
      this.filters.id_aspect = null
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_aspect': function(value) {
      this.$emit('filterSelected', this.filters)
    },
  },
  computed: {
    matters() {
      if (this.process == null) return []
      const { contract_matters } = this.process.aplicability_register
      return contract_matters.map(item => item.matter).sort((a,b) => a.order - b.order)
    },
    aspects() {
      if (this.process == null) return []
      const { contract_matters } = this.process.aplicability_register
      this.filters.id_aspect = null
      if (this.filters.id_matter != null) {
        const matter = contract_matters.find(matter => matter.id_matter == this.filters.id_matter)
        return matter.contract_aspects.map(aspect => aspect.aspect ).sort((a,b) => a.order - b.order)
      }
      return contract_matters.map(matter => matter.contract_aspects.map(aspect => aspect.aspect).sort((a,b) => a.order - b.order)).flat()
    },
  },
  methods: {
    getCustomers({ id_customer, id_corporate }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
    },
    async getListProcess() {
      try {
        const params = { scope: 'library' }
        const filters = { id_customer: this.filters.id_customer, id_corporate: this.filters.id_corporate }
        const { data } = await getProcessSource(params, filters)
        this.processList = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getCategories() {
      try {
        this.showLoadingMixin()
        const { data } = await getCategories()
        this.categories = data.data
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