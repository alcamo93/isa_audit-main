<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover
      :title="tootipTitle"
      :variant="variantButton"
      :disabled="disabledButton"
      @click="storeBackup"  
    >
    <b-icon icon="file-zip-fill" aria-hidden="true"></b-icon>
      Generar Respaldo
    </b-button>
  </fragment>
</template>

<script>
import { storeBackup } from '../../../services/backupService'

export default {
  props: {
    idAuditProcess: {
      type: Number,
      required: true
    }
  },
  computed: {
    variantButton() {
      return this.idAuditProcess == 0 ? 'secondary' : 'success'
    },
    disabledButton() {
      return this.idAuditProcess == 0
    },
    tootipTitle() {
      return this.idAuditProcess == 0 ? 'Primero selecciona una evaluación' : 'Generar Respaldo'
    }
  },
  methods: {
    async storeBackup() {
      try {
        this.showLoadingMixin()
        const form = { id_audit_processes: this.idAuditProcess }
        const { data } = await storeBackup(form)
        this.showLoadingMixin()
        this.alertMessageOk(data.message, 'info')
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>