<template>
  <div class="mb-2">
    <b-row>
      <b-col sm="12" md="6">
        <label>Licencia</label>
        <b-form-input
          v-model="filters.name"
          placeholder="Búsqueda por Licencia"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Estatus</label>
          <v-select 
            v-model="filters.status_id"
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
        name: null,
        status_id: null,
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