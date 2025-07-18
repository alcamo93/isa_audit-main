<template>
  <div class="mb-2">
    <b-row>
      <b-col sm="12" md="12">
        <label>Aspecto</label>
        <b-form-input
          v-model="filters.aspect"
          placeholder="Filtrar por nombre"
          debounce="500"
        ></b-form-input>
      </b-col>
    </b-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      record: null,
      filters: {
        aspect: null
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
}
</script>

<style>

</style>