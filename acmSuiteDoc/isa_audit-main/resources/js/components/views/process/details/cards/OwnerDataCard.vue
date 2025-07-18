<template>
  <b-card>
    <div v-if="title" class="font-weight-bold m-1">
      {{ title }}
    </div>
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
          Evaluación
        </label>
        <div class="font-weight-bold">
          {{ evaluationType }}
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
    customerName() {
      return this.record?.customer.cust_tradename ?? '----'
    },
    corporateName() {
      return this.record?.corporate.corp_tradename ?? '----'
    },
    processName() {
      return this.record?.audit_processes ?? '----'
    },
    evaluationType() {
      return this.record?.evaluation_type.name ?? '----'
    },
    scopeName() {
      const { scope, specification_scope } = this.record ?? {}
      return scope?.id_scope === 2 ? `${scope.scope}: ${specification_scope}` : scope?.scope ?? '----'
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