<template>
  <fragment>
    <b-row>
      <b-col sm="12" md="12">
        <label>
          Marco jurídico (Ley, Reglamento, Norma, etc)
        </label>
        <div class="font-weight-bold">
          {{ guidelineName }}
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <b-form-group>
          <label>Materia</label>
          <v-select 
            v-model="filters.id_matter"
            :options="matters"
            required
            label="matter"
            :reduce="e => e.id_matter"
            :clearable="false"
            placeholder="Selecciona materia"
            :append-to-body="true"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col v-if="filters.id_matter" sm="12" md="4">
        <label>Aspecto</label>
        <b-form-input
          v-model="filters.aspect_name"
          placeholder="Búsqueda por Aspecto"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col v-if="filters.id_matter" sm="12" md="4">
        <label>Asignados</label>
        <v-select 
          v-model="filters.has_relation"
          :options="optionRelations"
          :reduce="e => e.has_relation"
          label="option"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { getMatters, getAspects } from '../../../../../services/catalogService'

export default {
  name: 'FiltersRelationAspect',
  mounted() {
    this.reset()
    this.getMatters()
  },
  props: {
    record: {
      type: Object,
      required: true,
      default: null
    },
    // idMatter: {
    //   type: Number,
    //   default: null
    // }
  },
  data() {
    return {
      matters: [],
      aspects: [],
      optionRelations: [
        { has_relation: '0', option: 'Solo aspectos no asignados' },
        { has_relation: '1', option: 'Solo aspectos asignados' },
      ],
      filters: {
        id_matter: null,
        aspect_name: null,
        has_relation: null,
      }
    }
  },
  watch: {
    filters: {
      handler() {
        this.$emit('filterSelected', this.filters)
      },
      deep: true,
    },
    // idMatter(newIdMatter) {
    //   if (newIdMatter !== null) {
    //     this.reset();
    //   }
    // }
  },
  computed: {
    guidelineName() {
      if (this.record.guideline == null) return ''
      return this.record.guideline
    },
  },
  methods: {
    async getMatters(){
      try {
        const { data } = await getMatters()
        this.matters = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async reset() {
      this.filters.id_matter = null
      this.filters.aspect_name = null
      this.filters.has_relation = null
      if(this.record.aspects.length > 0){
        this.filters.id_matter = this.record.aspects[0].matter.id_matter
      }
      if(this.filters.id_matter != null){
        this.$emit('filterSelected', this.filters)
      }
    }
  }
}
</script>

<style>

</style>