<template>
  <div class="mb-2">
    <filter-customers 
      ref="customersRef"
      @fieldSelected="getCustomers"
    />
    <b-row>
      <b-col sm="12" md="4">
        <label>perfil</label>
        <b-form-input
          v-model="filters.profile_name"
          placeholder="Búsqueda por perfil"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>Tipo</label>
          <v-select 
            v-model="filters.id_profile_type"
            :options="types"
            :reduce="e => e.id_profile_type"
            label="type"
            placeholder="Todos"
            :append-to-body="true"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4">
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
import { getStatus, getProfileTypes } from '../../../services/catalogService'

export default {
  mounted() {
    this.getStatus()
    this.getProfileTypes()
  },
  components: {
    FilterCustomers
  },
  data() {
    return {
      status: [],
      types: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        profile_name: null,
        id_profile_type: null,
        id_status: null,
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
    'filters.profile_name': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_profile_type': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_status': function() {
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
    async getProfileTypes() {
      try {
        const { data } = await getProfileTypes()
        this.types = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>