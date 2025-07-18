<template>
  <fragment>
    <b-row>
      <b-col sm="12" md="12">
        <label>Nombre</label>
        <b-form-input
          v-model="filters.corporate_name"
          placeholder="Filtrar por nombre comercial o razón social"
          debounce="500"
        ></b-form-input>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Estatus
          </label>
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
      <b-col sm="12" md="4">
        <b-form-group>
          <label>
            Tipo de Planta
          </label>
          <v-select 
            v-model="filters.type"
            :options="types"
            :reduce="e => e.id_type"
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
          <label>
            Giro
          </label>
          <v-select 
            v-model="filters.id_industry"
            :options="industries"
            :reduce="e => e.id_industry"
            label="industry"
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
  </fragment>
</template>

<script>
import { getStatus, getIndustries } from '../../../../services/catalogService'

export default {
  mounted() {
    this.getStatus()
    this.getIndustries()
  },
  data() {
    return {
      status: [],
      industries: [],
      types: [
        { id_type: 0, type: 'Operativa' },
        { id_type: 1, type: 'Nueva' },
      ],
      filters: {
        corporate_name: null,
        id_status: null,
        type: null,
        id_industry: null,
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
        this.showLoadingMixin()
        const { data } = await getStatus({}, { group: 1 })
        this.status = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getIndustries() {
      try {
        this.showLoadingMixin()
        const { data } = await getIndustries()
        this.industries = data.data
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