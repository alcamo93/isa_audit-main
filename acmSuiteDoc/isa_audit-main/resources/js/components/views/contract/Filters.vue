<template>
  <div class="mb-2">
    <filter-customers 
      ref="customersRef"
      @fieldSelected="getCustomers"
    ></filter-customers>
    <b-row>
      <b-col sm="12" md="6">
        <label>Contrato</label>
        <b-form-input
          v-model="filters.contract"
          placeholder="Búsqueda por Contrato"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
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
    </b-row>
  </div>
</template>

<script>
import FilterCustomers from '../components/customers/FilterCustomers'
import { getStatus } from '../../../services/catalogService'

export default {
  mounted() {
    this.getStatus()
  },
  components: {
    FilterCustomers
  },
  data() {
    return {
      status: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        contract: null,
        id_status: null,
      }
    }
  },
  watch: {
    filters: {
      handler() {
        this.$emit('filterSelected', this.filters)
      },
      deep: true,
    }
  },
  methods: {
    getCustomers({ id_customer, id_corporate }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
      this.$emit('filterSelected', this.filters)
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