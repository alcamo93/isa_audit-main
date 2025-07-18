<template>
  <fragment>
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
      <b-col sm="12" md="6" lg="3">
        <b-form-group>
          <label>
            Materia
          </label>
          <v-select 
            :append-to-body="true"
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
      <b-col sm="12" md="6" lg="3">
        <b-form-group>
          <label>
            Aspecto
          </label>
          <v-select 
            :append-to-body="true"
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
      <b-col sm="12" md="6" lg="3">
        <label>Requerimiento</label>
        <b-form-input
          v-model="filters.no_requirement"
          placeholder="Búsqueda por requerimiento"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6" lg="3">
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
    <b-row>
      <b-col sm="12" md="6" lg="4">
        <b-form-group>
          <label>
            Prioridad
          </label>
          <v-select 
            :append-to-body="true"
            v-model="filters.id_priority"
            :options="priorities"
            :reduce="e => e.id_priority"
            label="priority"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6" lg="4">
        <b-form-group>
          <label>
            Rango de fechas
          </label>  
          <vue-date-picker 
            input-class="form-control"
            format="DD/MM/YYYY"
            value-type="YYYY-MM-DD"
            range 
            v-model="filters.range"
            placeholder="Fecha de inicio a fecha de vencimiento"
          ></vue-date-picker>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6" lg="4">
        <label>Responsable de aprobación</label>
        <b-form-input
          v-model="filters.name"
          placeholder="Búsqueda por Responsable"
          debounce="500"
        ></b-form-input>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { getPriorities } from '../../../services/catalogService'

export default {
  props: {
    headers: {
      type: Object,
      required: true
    }
  },
  mounted() {
    this.getPriorities()
  },
  data() {
    return {
      priorities: [],
      filters: {
        id_matter: null,
        id_aspect: null,
        no_requirement: '',
        id_status: null,
        id_priority: null,
        range: [],
        dates: null,
        name: ''
      }
    }
  },
  methods: {
    async getPriorities() {
      try {
        const { data } = await getPriorities()
        this.priorities = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
  },
  watch: {
    filters: {
      handler({range}) {
        if (range.length) {
          const isNull = range.some(item => item == null)
          if (isNull) this.filters.dates = null
          else this.filters.dates = this.filters.range
        }
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
      if (this.filters.id_matter != null) {
        const matter = this.headers.matters.find(matter => matter.id_matter == this.filters.id_matter)
        const found = matter.aspects.some(item => item.id_aspect == this.filters.id_aspect)
        if ( !found ) this.filters.id_aspect = null
        return matter.aspects
      }
      return this.headers.matters.flatMap(matter => matter.aspects)
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