<template>
  <b-row>
    <b-col>
      <b-card body-class="text-center" class="text-center">
        <template #header>
          <h6 class="font-italic font-weight-bold mb-0">Evaluación: Aplicabilidad</h6>
        </template>
        <b-card-text class="font-italic">
          Como primer paso contesta el cuestionario para obtener requerimientos de cada aspecto a evaluar.
        </b-card-text>
        <b-card-text v-if="aplicabilityRegister?.status" class="font-weight-bold">
          Estatus actual de aplicabilidad: 
          <b-badge pill :variant="color">
            {{ status }}
          </b-badge>
        </b-card-text>
        <b-button 
          @click="openAplicability"
          block pill variant="info"
        >
          Ir a Aplicabilidad
          <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
        </b-button>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
export default {
  props: {
    aplicabilityRegister: {
      type: Object,
      required: true
    }
  },
  computed: {
    color() {
      return this.aplicabilityRegister?.status?.color ?? 'light'
    },
    status() {
      return this.aplicabilityRegister?.status?.status ?? ''
    }
  },
  methods: {
    openAplicability() {
      if ( !this.aplicabilityRegister?.id_aplicability_register ) return
      const host = window.location.origin
      const idAuditProcess = this.aplicabilityRegister.id_audit_processes
      const idAplicabilityRegister = this.aplicabilityRegister.id_aplicability_register
      const url = `${host}/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/view`
      window.open(url, '_blank')
    },
  }
}
</script>

<style>

</style>