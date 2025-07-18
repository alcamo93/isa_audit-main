<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      variant="secondary"
      class="float-right"
      @click="showModal"
    >
      Evaluaciones 
      <b-icon icon="diagram3-fill" aria-hidden="true"></b-icon> 
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
      hide-footer
    >
      <b-container fluid>
        <!-- aplicability card -->
        <template v-if="!processIsEmpty">
          <aplicability-card
            :aplicability-register="process.aplicability_register"
          />
        </template>
        <!-- obligation card -->
        <template v-if="!processIsEmpty">
          <obligation-card
            @successfully="getProcess"
            :process="process"
          />
        </template>
         <!-- audit card -->
         <template v-if="!processIsEmpty">
          <audit-card
            @successfully="getProcess"
            :process="process"
          />
        </template>
      </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import AplicabilityCard from './cards/AplicabilityCard.vue'
import ObligationCard from './cards/ObligationCard.vue'
import AuditCard from './cards/AuditCard.vue'
import { getProcess } from '../../../services/processService'

export default {
  components: {
    AplicabilityCard,
    ObligationCard,
    AuditCard,
  },
  props: {
    record: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialog: false,
      process: null,
    }
  },
  computed: {
    processIsEmpty() {
      return this.process == null
    },
    titleModal() {
      return `Evaluaciones para: ${this.record.audit_processes}`
    },
  },
  methods: {
    async showModal() {
      await this.getProcess()
      this.dialog = true
    },
    async getProcess() {
      try {
        this.showLoadingMixin()
        const { data } = await getProcess(this.record.id_audit_processes)
        this.process = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style scoped>
.card {
  margin-bottom: 10px !important;
  margin-top: 5px !important;
}
</style>