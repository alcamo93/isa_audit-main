<template>
  <fragment>
    <b-tr v-if="showRowCollapse" 
      class="collapse-row" 
    >
      <b-td 
        class="text-center cursor-pointer collapse-column" 
        :colspan="colspan"
        @click="collapseRow"
        v-b-tooltip.hover
        :title="`${row.actions.opened ? 'Cerrar' : 'Abrir'} Subrequeriemientos`"
      >
        <b-icon 
          :icon="`chevron-compact-${row.actions.opened ? 'up' : 'down'}`"
          aria-hidden="true" font-scale="2"
        ></b-icon>
      </b-td>
    </b-tr>
    <main-row v-for="subRow in row.childs" :key="`sub-row-${subRow.id}`"
      :row="subRow"
      :params="params"
      :evaluate-risk="evaluateRisk"
      :in-subtable="true"
      :collapse="row.actions.opened"
      @successfully="successfully"
    /> 
  </fragment>
</template>

<script>
import MainRow from '../rows/MainRow'

export default {
  components: {
    MainRow,
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
  computed: {
    showRowCollapse() {
      return this.row.actions.show_details
    }
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

<style scoped>
.collapse-row {
  background-color: #e0e0e0 !important;
}
.collapse-column {
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}
.cursor-pointer {
  cursor: pointer;
}
</style>