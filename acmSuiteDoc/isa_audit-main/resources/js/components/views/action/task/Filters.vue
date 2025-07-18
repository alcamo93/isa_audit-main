<template>
  <div class="mb-2">
    <div class="d-flex flex-wrap">
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Cliente
        </label>
        <div class="font-weight-bold">
          {{ customerName }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Planta
        </label>
        <div class="font-weight-bold">
          {{ corporateName }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Nombre de evaluación
        </label>
        <div class="font-weight-bold">
          {{ processName }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Alcance
        </label>
        <div class="font-weight-bold">
          {{ scopeName }}
        </div>
      </div>
    </div>
    <div class="d-flex flex-wrap">
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Materia
        </label>
        <div class="font-weight-bold">
          {{ matterName }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Aspecto
        </label>
        <div class="font-weight-bold">
          {{ aspectName }}
        </div>
      </div>      
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Estatus Requerimiento
        </label>
        <div class="font-weight-bold">
          <b-badge 
            pill 
            :variant="statusColorName"
          >
            {{ statusName }}
          </b-badge>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2" v-if="headers.last_expired.extension_date">
        <label>
          Última fecha de extensión
        </label>
        <div class="font-weight-bold">
          <b-badge variant="info">
            {{ headers.last_expired.extension_date_format }}
          </b-badge>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Requerimiento
        </label>
        <div class="font-weight-bold">
          {{ noRequirement }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    headers: {
      type: Object,
      required: true,
    },
    origin: {
      type: String,
      required: true,
    }
  },
  data() {
    return {
      process: null,
    }
  },
  computed: {
    customerName() {
      return this.headers.customer_name
    },
    corporateName() {
      return this.headers.corporate_name
    },
    processName() {
      return this.headers.audit_process
    },
    scopeName() {
      return this.headers.scope
    },
    matterName() {
      return this.headers.matter
    },
    aspectName() {
      return this.headers.aspect
    },
    noRequirement() {
      return this.headers.full_requirement
    },
    statusName() {
      return this.headers.status?.status ?? '---'
    },
    statusColorName() {
      return this.headers.status?.color ?? 'dark'
    },
  },
}
</script>

<style>

</style>