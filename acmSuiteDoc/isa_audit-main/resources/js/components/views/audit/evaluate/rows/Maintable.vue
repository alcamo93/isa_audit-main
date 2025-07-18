<template>
  <fragment>
    <main-row
      :row="row"
      :params="params"
      :evaluate-risk="evaluateRisk"
      @successfully="successfully"
    />
    <subtable
      :row="row"
      :params="params"
      :evaluate-risk="evaluateRisk"
      :colspan="colspan"
      @successfully="successfully"
      @handlerCollapse="collapseRow"
    />
  </fragment>
</template>

<script>
import MainRow from './MainRow'
import Subtable from "./Subtable";
export default {
  components: {
    MainRow,
    Subtable,
  },
  props: {
    row: {
      type: Object,
      required: true
    },
    params: {
      type: Object,
      required: true
    },
    evaluateRisk: {
      type: Boolean,
      required: true
    },
    colspan: {
      type: Number,
      required: true
    },
  },
  methods: {
    collapseRow() {
      this.$emit('handlerCollapse', this.row.id)
    },
    successfully() {
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>