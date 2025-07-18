<template>
  <b-row>
    <b-col sm="12" md="4" lg="4">
      <label>
        Cliente
      </label>
      <div class="font-weight-bold">
        {{ info.customer_name }}
      </div>
    </b-col>
    <b-col sm="12" md="4" lg="4">
      <label>
        Planta
      </label>
      <div class="font-weight-bold">
        {{ info.corporate_name }}
      </div>
    </b-col>
    <b-col sm="12" md="4" lg="4">
      <label>
        Nombre de evaluación
      </label>
      <div class="font-weight-bold">
        {{ info.audit_process }}
      </div>
    </b-col>
    <b-col sm="12" md="6" lg="4">
      <label>No. Requerimiento</label>
      <b-form-input
        v-model="filters.several_no_requirement"
        placeholder="Búsqueda por número de requerimiento"
        debounce="500"
      ></b-form-input>
    </b-col>
    <b-col sm="12" md="6" lg="4">
      <label>Requerimiento</label>
      <b-form-input
        v-model="filters.requirement"
        placeholder="Búsqueda por requerimiento"
        debounce="500"
      ></b-form-input>
    </b-col>
    <b-col sm="12" md="6" lg="4">
      <label>Documento en Especifico</label>
      <b-form-input
        v-model="filters.document"
        placeholder="Búsqueda por documento en específico"
        debounce="500"
      ></b-form-input>
    </b-col>
    <b-col sm="12" md="6" lg="4">
      <label>Subrequerimiento</label>
      <b-form-input
        v-model="filters.subrequirement"
        placeholder="Búsqueda por subrequerimiento"
        debounce="500"
      ></b-form-input>
    </b-col>
    <b-col sm="12" md="6" lg="4">
      <b-form-group>
        <label>
          Tipo de evidencia
        </label>
        <v-select 
          :append-to-body="true"
          v-model="filters.id_evidence"
          :options="evidences"
          :reduce="e => e.id_evidence"
          label="evidence"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-form-group>
    </b-col>
    <b-col sm="12" md="6" lg="4">
      <b-form-group>
        <label>
          Condición
        </label>
        <v-select 
          :append-to-body="true"
          v-model="filters.id_condition"
          :options="conditions"
          :reduce="e => e.id_condition"
          label="condition"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-form-group>
    </b-col>
    <b-col sm="12" md="6" lg="6">
      <b-form-group>
        <label>
          Respuestas
        </label>
        <v-select 
          :append-to-body="true"
          v-model="filters.answer"
          :options="answers"
          :reduce="e => e.answer"
          label="label_answer"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-form-group>
    </b-col>
    <b-col sm="12" md="6" lg="6">
      <b-form-group>
        <label>
          Requerimiento Evaluados
        </label>
        <v-select 
          :append-to-body="true"
          v-model="filters.complete"
          :options="completes"
          :reduce="e => e.complete"
          label="label_complete"
          placeholder="Todos"
        >
          <div slot="no-options">
            No se encontraron registros
          </div>
        </v-select>
      </b-form-group>
    </b-col>
    <b-col sm="12" md="12" lg="12">
      <div class="text-center">{{ totalProgress }}%</div>
      <b-progress class="mt-2" variant="success" show-progress striped :animated="true">
        <b-progress-bar :value="totalProgress" :label="`${totalProgress}%`"></b-progress-bar>
      </b-progress>
    </b-col>
  </b-row>
</template>

<script>
import { getConditions, getEvidences } from '../../../../services/catalogService'

export default {
  mounted() {
    this.getConditions()
    this.getEvidences()
  },
  props: {
    info: {
      type: Object,
      required: true,
    },
    totalProgress: {
      type: Number,
      required: false,
      default: 0
    }
  },
  data() {
    return {
      evidences: [],
      conditions: [],
      answers: [
        { answer: '0', label_answer: 'No cumple' },
        { answer: '1', label_answer: 'Cumple' },
        { answer: '2', label_answer: 'No aplica' }
      ],
      completes: [
        { complete: '1', label_complete: 'Evaluados' },
        { complete: '0', label_complete: 'No evaluados' }
      ],
      filters: {
        several_no_requirement: null,
        requirement: null,
        document: null,
        subrequirement: null,
        id_evidence: null,
        id_condition: null,
        answer: null,
        complete: null
      }
    }
  },
  watch: {
    'filters.several_no_requirement': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.requirement': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.document': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.subrequirement': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_evidence': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.id_condition': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.answer': function() {
      this.$emit('filterSelected', this.filters)
    },
    'filters.complete': function() {
      this.$emit('filterSelected', this.filters)
    },
  },
  methods: {
    async getConditions() {
      try {
        const { data } = await getConditions()
        this.conditions = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getEvidences() {
      try {
        const { data } = await getEvidences()
        this.evidences = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>