<template>
  <b-card>
    <div v-if="title" class="font-weight-bold m-1">
      {{ title }}
    </div>
    <div class="d-flex flex-wrap">
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Periodo de registro
        </label>
        <div class="font-weight-bold">
          <div class="py-1 my-1 rounded text-white text-center" :class="`bg-${dateColor}`">
            {{ dateFormat }}
          </div>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Evaluar Nivel de riesgo
        </label>
        <div class="font-weight-bold">
          <div class="py-1 my-1 rounded text-white text-center" :class="`bg-${defineColor(evaluateRisk)}`">
            {{ evaluateRisk }}
          </div>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Evaluar Requerimientos Especificos
        </label>
        <div class="font-weight-bold">
          <div class="py-1 my-1 rounded text-white text-center" :class="`bg-${defineColor(evaluateSpecific)}`">
            {{ evaluateSpecific }}
          </div>
        </div>
      </div>
    </div>
  </b-card>
</template>

<script>

export default {
  props: {
    title: {
      type: String,
      required: false,
    },
    record: {
      type: null || Object,
      required: true,
    }
  },
  computed: {
    evaluationType() {
      return this.record?.evaluation_type.name ?? '----'
    },
    evaluateRisk() {
      if (!this.record) return '----'
      return Boolean(this.record.evaluate_risk) ? 'Si' : 'No'
    },
    dateFormat() {
      return this.record?.dates_format ?? '--/--/--'
    },
    dateColor() {
      if (!this.record) return 'secondary'
      return this.record.is_in_current_year ? 'success' : 'danger'
    },
    evaluateSpecific() {
      if (!this.record) return '----'
      return Boolean(this.record.evaluate_especific) ? 'Si' : 'No'
    },
  },
  methods: {
    defineColor(word) {
      if (word != 'Si' && word != 'No') return 'secondary'
      return word == 'Si' ? 'success' : 'danger'
    }
  }
}
</script>

<style></style>