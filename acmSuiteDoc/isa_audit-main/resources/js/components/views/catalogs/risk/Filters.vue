<template>
  <div class="mb-2">
    <b-row>
      <b-col>
        <label class="text-uppercase">Catagoría</label>
        <b-form-input
          v-model="filters.risk_category"
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
        risk_category: null
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