<template>
    <div class="mb-2">
      <b-row>
        <b-col sm="12" md="6">
          <label>Titular</label>
          <b-form-input
            v-model="filters.headline"
            placeholder="Búsqueda por titular"
            debounce="500"
          ></b-form-input>
        </b-col>
        <b-col sm="12" md="6">
          <label>Resumen</label>
          <b-form-input
            v-model="filters.description"
            placeholder="Búsqueda por resumen"
            debounce="500"
          ></b-form-input>
        </b-col>
      </b-row>
    </div>
  </template>
  
  <script>

  export default {
    props: {
    },
    components: {
    },
    data() {
      return {
        specialsFilters: [
          { id: 'PUBLISHED', name: 'Publicadas' },
          { id: 'CURRENT', name: 'Fecha vigente' },
          { id: 'HISTORY', name: 'En historial' },
          { id: 'OUT_RANGE', name: 'Fuera de fecha' },
        ],
        filters: {
          custom_filter: null,
          headline: null,
          description: null
        }
      }
    },
    computed: {

    },
    watch: {
      filters: {
        handler() {
          this.$emit('filterSelected', this.filters)
        },
        deep: true,
      }
    },
  }
  </script>