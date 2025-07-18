<template>
  <div class="mb-2">
    <b-row>
      <b-col>
        <label class="text-uppercase">Giro</label>
        <b-form-input
          v-model="filters.industry"
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
      filters: {
        industry: null
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