<template>
  <div class="mb-2">
    <loading :show="loadingMixin" />
    <b-row>
      <b-col sm="12" md="4">
        <label>Nombre</label>
        <b-form-input
          v-model="filters.risk_help"
          placeholder="Búsqueda por Nombre"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <label>Criterio</label>
        <b-form-input
          v-model="filters.standard"
          placeholder="Búsqueda por Criterio"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="4">
        <label>Atributo</label>
        <v-select 
          v-model="filters.id_risk_attribute"
          :options="riskAttributes"
          required
          label="risk_attribute"
          :reduce="e => e.id_risk_attribute"
          :clearable="true"
          placeholder="Todos"
          :append-to-body="true"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { getRiskAttributesSource } from '../../../../../services/catalogService'

export default {
  async mounted() {
    await this.getRiskAttributesSource()
  },
  props: {
    record: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      riskAttributes: [],
      filters: {
        risk_help: null,
        standard: null,
        id_risk_attribute: null,
      },
    }
  },
  watch: {
    'filters.risk_help': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.standard': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.id_risk_attribute': function() {
      this.$emit('fieldSelected', this.filters)
    },
  },
  computed: {
    riskCategoryName() {
      if (this.record == null) return ''
      const { risk_category } = this.record
      return risk_category
    },
  },
  methods: {
    async getRiskAttributesSource() {
      try {
        this.showLoadingMixin()
        const { data } = await getRiskAttributesSource()
        this.riskAttributes = data.data
        this.showLoadingMixin()
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