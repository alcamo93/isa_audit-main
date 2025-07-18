<template>
  <div class="mx-2 mb-2">
    <b-row>
      <b-col
        sm="12"
        md="6"
        class="d-flex align-items-center justify-content-center justify-content-sm-center justify-content-md-start"
      >
        <span class="mr-1">
          Mostrando {{ dataList.rows }} 
          registros de {{ dataList.total }}
        </span>
      </b-col>
      <b-col
        sm="12"
        md="6"
        class="d-flex align-items-center justify-content-center justify-content-sm-center justify-content-md-end"
      >
        <b-pagination
          v-model="paginationData.page"
          :total-rows="dataList.total"
          :per-page="paginationData.perPage"
          first-number
          last-number
          class="mb-0 mt-1 mt-sm-0"
          prev-class="prev-item"
          next-class="next-item"
          @click="$emit('pagination-data', paginationData)"
        >
          <template #prev-text>
            Anterior
          </template>
          <template #next-text>
            Siguiente
          </template>
        </b-pagination>
      </b-col>
    </b-row>
  </div>
</template>
<script>

export default {
  name: 'AppPaginator',
  props: {
    dataList: {
      type: [Array, Object],
      default: null,
    },
  },
  data() {
    return {
      paginationData: {
        page: 1,
        perPage: 15,
      },
      perPageOptions: [15, 25, 50, 100],
    }
  },
  watch: {
    'paginationData.page': function() {
      this.$emit('pagination-data', this.paginationData)
    },
    'paginationData.perPage': function() {
      this.$emit('pagination-data', this.paginationData)
    },
  },
}
</script>
<style>

</style>
