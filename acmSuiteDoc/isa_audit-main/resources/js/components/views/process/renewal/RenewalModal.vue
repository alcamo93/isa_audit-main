<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover.leftbottom
      title="Renovar evaluación"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="columns" aria-hidden="true"></b-icon> 
    </b-button>
    
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
      hide-footer
    >
      <b-container fluid>
        <data-spread-sheet
          title="Evaluación anterior"
          :record="record"
        />
      </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import { getProcess } from '../../../../services/processService'
import DataSheet from '../details/DataSheet.vue'
import DataSpreadSheet from '../renewal/DataSpreadSheet.vue'

export default {
  components: { 
    DataSheet,
    DataSpreadSheet,
  },
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      dialog: false,
      loading: false,
      record: null,
      auditors: [],
      matters: [],
    }
  },
  computed: {
    titleModal() {
      return `Detalles de evaluación: ${this.record?.audit_processes ?? ''}`
    },
    
  },
  methods: {
    async showModal() {
      try {
        this.showLoadingMixin()
        const { data } = await getProcess(this.id)
        this.record = data.data
        this.showLoadingMixin()
        this.dialog = true
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    
  }
}
</script>

<style></style>