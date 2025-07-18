<template>
  <fragment>
    <div class="d-flex flex-wrap">
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Cliente
        </label>
        <div class="font-weight-bold">
          {{ info.customer_name }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Planta
        </label>
        <div class="font-weight-bold">
          {{ info.corporate_name }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Nombre de evaluación
        </label>
        <div class="font-weight-bold">
          {{ info.audit_process }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Alcance
        </label>
        <div class="font-weight-bold">
          {{ info.scope }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Materia
        </label>
        <div class="font-weight-bold">
          {{ info.matter }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Aspecto
        </label>
        <div class="font-weight-bold">
          {{ info.aspect }}
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Estatus
        </label>
        <div class="font-weight-bold">
          <b-badge :variant="info.status.color">
            {{ info.status.status }}
          </b-badge>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Competencia
        </label>
        <div class="font-weight-bold">
          <b-badge variant="primary">
            {{ info.application_type }}
          </b-badge>
        </div>
      </div>
    </div>
  </fragment>
</template>

<script>
export default {
  props: {
    info: {
      type: Object,
      required: true,
    },
  },
}
</script>

<style></style>