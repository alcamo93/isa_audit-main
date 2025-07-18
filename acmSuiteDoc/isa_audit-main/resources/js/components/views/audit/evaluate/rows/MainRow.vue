<template>
  <fragment>
    <row-info-evaluate v-if="collapsed"
      :row="row"
      :params="params"
      :evaluate-risk="evaluateRisk"
      @successfully="successfully"
    />
    <row-answer-evaluate v-if="collapsed"
      :row="row"
      :params="params"
      :recursive-answer="row.recursive"
      @successfully="successfully"
    />
  </fragment>
</template>

<script>
import RowInfoEvaluate from '../rows/RowInfoEvaluate'
import RowAnswerEvaluate from '../rows/RowAnswerEvaluate'

export default {
  components: {
    RowInfoEvaluate,
    RowAnswerEvaluate,
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
    inSubtable: {
      type: Boolean,
      required: false,
      default: false
    },
    collapse: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  computed: {
    collapsed() {
      return !this.inSubtable || (this.inSubtable && this.collapse)
    }
  },
  methods: {
    successfully() {
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>