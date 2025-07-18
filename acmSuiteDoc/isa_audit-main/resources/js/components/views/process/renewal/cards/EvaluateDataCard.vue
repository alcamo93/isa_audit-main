<template>
  <b-card>
    <div v-if="title" class="font-weight-bold m-1">
      {{ title }}
    </div>
    <div class="d-flex flex-wrap">
      
      <div class="flex-grow-1 mx-3 my-2">
        <b-form-group>
          <label>
            Periodo de registro
            <span 
              v-if="endDateFutureComputed"
              class="font-weight-bold"
            > 
              ({{ formatDateMixin(date) }} - {{ formatDateMixin(endDateFutureComputed) }})
            </span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Especificar periodo"
          >
            <vue-date-picker 
              input-class="form-control"
              value-type="YYYY-MM-DD"
              format="DD/MM/YYYY"
              :value="date"
              @input="updateDate"
            ></vue-date-picker>
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </div>

      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Evaluar Nivel de riesgo
        </label>
        <div class="font-weight-bold">
          <b-form-checkbox 
            v-b-tooltip.hover.leftbottom
            :title="labelOption(evaluateRisk, 'Nivel de Riesgo')"
            :checked="evaluateRisk" 
            :value="1"
            :unchecked-value="0"
            switch size="lg"
            @input="updateOptionLevelRisk"
          ></b-form-checkbox>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Evaluar Requerimientos Especificos
        </label>
        <div class="font-weight-bold">
          <b-form-checkbox 
            v-b-tooltip.hover.leftbottom
            :title="labelOption(evaluateSpecific, 'Requerimientos Especificos')"
            :checked="evaluateSpecific" 
            switch size="lg"
            @input="updateOptionSpecific"
          ></b-form-checkbox>
        </div>
      </div>

      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Mantener valores de Nivel de riesgo
        </label>
        <div class="font-weight-bold">
          <b-form-checkbox 
            v-b-tooltip.hover.leftbottom
            :title="labelOptionKeep(keepRisk, 'valores de Nivel de riesgo')"
            :checked="keepRisk"
            :disabled="disabledKeepRisk"
            switch size="lg"
            @input="updateOptionKeepLevelRisk"
          ></b-form-checkbox>
        </div>
      </div>
      <div class="flex-grow-1 mx-3 my-2">
        <label>
          Mantener Evidencias cargadas
        </label>
        <div class="font-weight-bold">
          <b-form-checkbox 
            v-b-tooltip.hover.leftbottom
            :title="labelOptionKeep(keepFiles, 'Evidencias de cargadas')"
            :checked="keepFiles" 
            switch size="lg"
            @input="updateOptionKeepFiles"
          ></b-form-checkbox>
        </div>
      </div>

    </div>
  </b-card>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { required } from '../../../../validations'
import { endDateFuture } from '../../../components/scripts/dates'

export default {
  components: {
    ValidationProvider
  },
  props: {
    title: {
      type: String,
      required: false
    },
    evaluateRisk: {
      type: Number,
      required: true,
    },
    evaluateSpecific: {
      type: Boolean,
      required: true,
    },
    date: {
      type: String,
      required: true
    },
    keepRisk: {
      type: Boolean,
      required: true,
    },
    keepFiles: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      required
    }
  },
  computed: {
    endDateFutureComputed() {
      if (this.date == '' ) return null
      return endDateFuture(this.date) 
    },
    disabledKeepRisk() {
      return this.evaluateRisk == 0
    }
  },
  methods: {
    labelOption(value, message) {
      return `${(value == 1 ? 'No evaluar' : 'Evaluar')} ${message}`
    },
    labelOptionKeep(value, message) {
      return `${(value ? 'No mantener' : 'Mantener')} ${message}`
    },
    updateOptionLevelRisk(event) {
      this.$emit('update:evaluate-risk', event)
      if ( !event ) this.$emit('update:keep-risk', false)
    },
    updateOptionSpecific(event) {
      this.$emit('update:evaluate-specific', event)
    },
    updateDate(event) {
      this.$emit('update:date', event)
    },
    updateOptionKeepLevelRisk(event) {
      this.$emit('update:keep-risk', event)
    },
    updateOptionKeepFiles(event) {
      this.$emit('update:keep-files', event)
    }
  }
}
</script>

<style></style>