<template>
  <div class="mb-2">
    <b-row>
      <b-col>
        <label>Razón Social</label>
        <b-form-input
          v-model="filters.customer_name"
          placeholder="Filtrar por nombre comercial o razón social"
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
        customer_name: null
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