<template>
  <div class="mb-2">
    <filter-customers 
      ref="customersRef"
      @fieldSelected="getCustomers"
    />
    <b-row>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Alcance
          </label>
          <v-select 
            v-model="filters.id_scope"
            :options="scopes"
            :reduce="e => e.id_scope"
            label="scope"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Opciones de Aplicabilidad
          </label>
          <v-select 
            v-model="filters.evaluation_type_id"
            :options="evaluationTypes"
            :reduce="e => e.id"
            label="name"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Periodo de registro
          </label>  
          <vue-date-picker 
            input-class="form-control"
            format="DD/MM/YYYY"
            value-type="YYYY-MM-DD"
            range 
            v-model="filters.range"
            placeholder="Filtrar por Periodo de registro"
          ></vue-date-picker>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" :md="columnWithSpecialFilter">
        <label>Nombre de evaluación</label>
        <b-form-input
          v-model="filters.process"
          placeholder="Búsqueda por nombre"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col v-if="showSpecialFilter" sm="12" :md="columnWithSpecialFilter">
        <b-form-group>
          <label>
            Caracteristicas especificas
          </label>
          <v-select 
            v-model="filters.custom_filter"
            :options="specialsFilters"
            :reduce="e => e.id"
            label="name"
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
  </div>
</template>

<script>
import FilterCustomers from '../components/customers/FilterCustomers.vue'
import { getScopes, getEvaluationTypes } from '../../../services/catalogService'

export default {
  mounted() {
    this.getScopes()
    this.getEvaluationTypes()
  },
  props: {
    specialFilter: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  components: {
    FilterCustomers
  },
  data() {
    return {
      customers: [],
      corporates: [],
      scopes: [],
      evaluationTypes: [],
      specialsFilters: [
        { id: 'DASHBOARD', name: 'Graficables' },
        { id: 'USE_KPI', name: 'Evaluación completa' },
        { id: 'NO_USE_KPI', name: 'Evaluación parcial' },
        { id: 'IN_YEAR', name: 'Registro vigente' },
      ],
      filters: {
        id_customer: null,
        id_corporate: null,
        process: '',
        id_scope: null,
        evaluation_type_id: null,
        date: null,
        range: [],
        custom_filter: null
      }
    }
  },
  computed: {
    showSpecialFilter() {
      return this.specialFilter
    },
    columnWithSpecialFilter() {
      return this.showSpecialFilter ? 6 : 12
    }
  },
  watch: {
    filters: {
      handler({range}) {
        if (range.length) {
          const isNull = range.some(item => item == null)
          if (isNull) this.filters.date = null
          else this.filters.date = this.filters.range
        }
        this.$emit('filterSelected', this.filters)
      },
      deep: true,
    }
  },
  methods: {
    getCustomers({ id_customer, id_corporate }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
    },
    async getScopes() {
      try {
        const { data } = await getScopes()
        this.scopes = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getEvaluationTypes() {
      try {
        const { data } = await getEvaluationTypes()
        this.evaluationTypes = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>