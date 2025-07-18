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
      <b-col sm="12" md="6">
        <label>Temas</label>
        <b-form-input
          v-model="filters.topic_name"
          placeholder="Búsqueda por Tema"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
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

export default {
  mounted() {
    this.reset()
  },
  props: {
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      guidelines: [],
      optionRelations: [
        { has_relation: '0', option: 'Solo temas no asignados' },
        { has_relation: '1', option: 'Solo temas asignados' },
      ],
      filters: {
        topic_name: null,
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
    }
  },
  computed: {
    guidelineName() {
      if (this.record.guideline == null) return ''
      return this.record.guideline
    },
  },
  methods: {
    reset() {
      this.filters.topic_name = null
      this.filters.has_relation = null
      this.$emit('filterSelected', this.filters)
    }
  }
}
</script>

<style>

</style>