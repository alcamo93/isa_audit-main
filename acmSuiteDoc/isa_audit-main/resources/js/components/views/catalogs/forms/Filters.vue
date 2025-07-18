<template>
  <div class="mb-2">
    <b-row>
      <b-col sm="12" md="6">
        <label>Formulario</label>
        <b-form-input
          v-model="filters.name"
          placeholder="Búsqueda por nombre"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Vigentes</label>
          <v-select 
            v-model="filters.is_current"
            :options="mainAspects"
            required
            label="current"
            :reduce="e => e.is_current"
            :clearable="true"
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
    <b-row>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Materia</label>
          <v-select 
            v-model="filters.id_matter"
            :options="matters"
            required
            label="matter"
            :reduce="e => e.id_matter"
            :clearable="true"
            placeholder="Todos"
            :append-to-body="true"
            :loading="progress.matters"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>Aspecto</label>
          <v-select 
            v-model="filters.id_aspect"
            :options="aspects"
            required
            label="aspect"
            :reduce="e => e.id_aspect"
            :clearable="true"
            placeholder="Todos"
            :append-to-body="true"
            :loading="progress.aspects"
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
import { getMatters, getAspects } from '../../../../services/catalogService'

export default {
  mounted() {
    this.getMatters()
    this.getAspects()
  },
  data() {
    return {
      matters: [],
      aspects: [],
      mainAspects: [
        { is_current: '1', current: 'Activos' },
        { is_current: '0', current: 'Inactivos' },
      ],
      filters: {
        name: '',
        id_matter: null,
        id_aspect: null,
        is_current: null,
      },
      progress: {
        matters: false,
        aspects: false,
      }
    }
  },
  watch: {
    'filters.id_matter': function() {
      this.filters.id_aspect = null
      this.getAspects()
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_aspect': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.is_current': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.name': function() {
      this.$emit('fieldSelected', this.filters)
    },
  },
  methods: {
    async getMatters(){
      try {
        this.progress.matters = true
        const { data } = await getMatters()
        this.matters = data.data
        this.progress.matters = false
      } catch (error) {
        this.progress.matters = false
        this.responseMixin(error)
      }
    },
    async getAspects(){
      try {
        this.progress.aspects = true
        const filters = { id_matter: this.filters.id_matter }
        const { data } = await getAspects({}, filters)
        this.aspects = data.data
        this.progress.aspects = false
      } catch (error) {
        this.progress.aspects = false
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>