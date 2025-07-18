<template>
  <div class="mb-2">
    <filter-customers 
      ref="customersRef"
      @fieldSelected="getCustomers"
    />
    <b-row>
      <b-col sm="12" md="6">
        <label>Nombre</label>
        <b-form-input
          v-model="filters.name"
          placeholder="Búsqueda por nombre"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <label>Correo</label>
        <b-form-input
          v-model="filters.email"
          placeholder="Búsqueda por correo"
          debounce="500"
        ></b-form-input>
      </b-col>
    </b-row>
    <b-row>
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
      <b-col sm="12" md="6">
        <label>Teléfono</label>
        <b-form-input
          v-model="filters.phone"
          placeholder="Búsqueda por teléfono"
          debounce="500"
        ></b-form-input>
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
        name: null,
        email: null,
        id_status: null,
        phone: null,
      }
    }
  },
  watch: {
    'filters.id_customer': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_corporate': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.name': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.email': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_status': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.phone': function() {
      this.$emit('filterSelected', this.filters)
    }
  },
  methods: {
    getCustomers({ id_customer, id_corporate }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
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