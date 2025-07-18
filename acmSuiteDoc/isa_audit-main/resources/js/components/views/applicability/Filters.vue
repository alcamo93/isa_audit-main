<template>
  <div class="mb-2">
    <b-row>
      <b-col sm="12" md="6" lg="3">
        <label>
          Cliente
        </label>
        <div class="font-weight-bold">
          {{ customerName }}
        </div>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>
          Planta
        </label>
        <div class="font-weight-bold">
          {{ corporateName }}
        </div>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>
          Nombre de evaluación
        </label>
        <div class="font-weight-bold">
          {{ processName }}
        </div>
      </b-col>
      <b-col sm="12" md="6" lg="3">
        <label>
          Alcance
        </label>
        <div class="font-weight-bold">
          {{ scopeName }}
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4" lg="4">
        <b-form-group>
          <label>
            Materia
          </label>
          <v-select 
            :append-to-body="true"
            v-model="filters.id_contract_matter"
            :options="matters"
            :reduce="e => e.id_contract_matter"
            label="matter"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4" lg="4">
        <b-form-group>
          <label>
            Aspectos
          </label>
          <v-select 
            :append-to-body="true"
            v-model="filters.id_contract_aspect"
            :options="aspects"
            :reduce="e => e.id_contract_aspect"
            label="aspect"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="4" lg="4">
        <b-form-group>
          <label>
            Estatus
          </label>
          <v-select 
            :append-to-body="true"
            v-model="filters.id_status"
            :options="status"
            :reduce="e => e.id_status"
            label="status"
            placeholder="Todos"
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
export default {
  props: {
    headers: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      filters: {
        id_contract_matter: null,
        id_contract_aspect: null,
        id_status: null
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
  computed: {
    matters() {
      return this.headers.matters
    },
    aspects() {
      if (this.filters.id_contract_matter != null) {
        const matter = this.headers.matters.find(matter => matter.id_contract_matter == this.filters.id_contract_matter)
        const found = matter.aspects.some(item => item.id_contract_aspect == this.filters.id_contract_aspect)
        if ( !found ) this.filters.id_contract_aspect = null
        return matter.aspects
      }
      return this.headers.matters.flatMap(matter => matter.aspects)
    },
    matters() {
      return this.headers.matters.sort((a,b) => a.order - b.order)
    },
    status() {
      return this.headers.status
    },
    customerName() {
      return this.headers.customer_name
    },
    corporateName() {
      return this.headers.corporate_name
    },
    processName() {
      return this.headers.audit_process
    },
    scopeName() {
      return this.headers.scope
    },    
  },
}
</script>

<style>

</style>