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
      <b-col sm="12" md="12" lg="4">
        <b-form-group>
          <label>
            Materia
          </label>
          <v-select 
            :append-to-body="true"
            v-model="filters.id_audit_matter"
            :options="matters"
            :reduce="e => e.id_audit_matter"
            label="matter"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="12" lg="4">
        <b-form-group>
          <label>
            Aspecto
          </label>
          <v-select 
            :append-to-body="true"
            v-model="filters.id_audit_aspect"
            :options="aspects"
            :reduce="e => e.id_audit_aspect"
            label="aspect"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="12" lg="4">
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
    info: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      filters: {
        id_audit_matter: null,
        id_audit_aspect: null,
        id_status: null
      },
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
      return this.info.matters
    },
    aspects() {
      if (this.filters.id_audit_matter != null) {
        const matter = this.info.matters.find(matter => matter.id_audit_matter == this.filters.id_audit_matter)
        const found = matter.aspects.some(item => item.id_audit_aspect == this.filters.id_audit_aspect)
        if ( !found ) this.filters.id_audit_aspect = null
        return matter.aspects
      }
      return this.info.matters.flatMap(matter => matter.aspects)
    },
    status() {
      return this.info.status
    },
    customerName() {
      return this.info.customer_name
    },
    corporateName() {
      return this.info.corporate_name
    },
    processName() {
      return this.info.audit_process
    },
    scopeName() {
      return this.info.scope
    }, 
  },
}
</script>

<style>

</style>