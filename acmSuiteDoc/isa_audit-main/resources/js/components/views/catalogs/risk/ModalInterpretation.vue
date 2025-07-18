<template>
  <fragment>
    <loading :show="loadingMixin" />

    <b-button
      v-b-tooltip.hover.left
      title="Interpretaciones"
      class="btn-link"
      variant="success"
      @click="showModal"
    >
      <b-icon icon="flag-fill" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row v-for="(item, index) in interpretations" :key="item.interpretation">
              <b-col>
                <b-form-group
                  :label="`Rango de valores para interpretación: ${item.interpretation}`"
                >
                <div class="d-flex">
                  <validation-provider class="flex-fill mr-1 ml-1"
                    #default="{ errors }"
                    :rules="`required|integer|min_value:${getMinValue(index)}|max_value:${getMaxValue(index)}`"
                    :name="`Valor mínimo para ${item.interpretation}`"
                  >
                    <b-form-input
                      v-model.number="item.interpretation_min"
                      type="number"
                      :placeholder="`Mínimo para ${item.interpretation}`"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                  <validation-provider class="flex-fill mr-1 ml-1"
                    #default="{ errors }"
                    :rules="`required|integer|min_value:${getMinValue(index)}|max_value:${getMaxValue(index)}`"
                    :name="`Valor máximo para ${item.interpretation}`"
                  >
                    <b-form-input
                      v-model.number="item.interpretation_max"
                      type="number"
                      :placeholder="`Máximo para ${item.interpretation}`"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </div>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <b-alert :show="!isValid" variant="danger">
                  Los rangos deben sumar <span class="font-weight-bold">100</span> y no deben solaparse.
                  A continuación una guía de ejemplo de su uso: <br>
                  <span class="font-weight-bold">Bajo:</span> 1-30 <br>
                  <span class="font-weight-bold">Medio:</span> 31-60 <br>
                  <span class="font-weight-bold">Alto:</span> 61-100 <br>
                </b-alert>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button
            variant="success"
            class="float-right"
            @click="setRiskInterpretation"
          >
            Registrar
          </b-button>
          <b-button
            variant="danger" 
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cancelar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, integer, min_value, max_value } from '../../../validations'
import { getRiskInterpretations, storeRiskInterpretation } from '../../../../services/riskService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    register: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      required,
      integer,
      min_value,
      max_value,
      dialog: false,
      interpretations: [
        { interpretation: 'Bajo', interpretation_min: 0, interpretation_max: 0 },
        { interpretation: 'Medio', interpretation_min: 0, interpretation_max: 0 },
        { interpretation: 'Alto', interpretation_min: 0, interpretation_max: 0 },
      ],
    }
  },
  computed: {
    titleModal() {
      return `Interpretaciones para categoría de riesgo: ${this.register.risk_category}`
    },
    isValid() {
      return this.validateRanges()
    },  
  },
  methods: {
    getMinValue(index) {
      const interpretation = this.interpretations[index]
      if (interpretation.interpretation == 'Bajo') {
        return 1
      }
      if (interpretation.interpretation == 'Medio') {
        const indexLow = index - 1
        const interpretationLow = this.interpretations[indexLow]
        return interpretationLow.interpretation_max + 1
      }
      if (interpretation.interpretation == 'Alto') {
        const indexHigh = index - 1
        const interpretationHigh = this.interpretations[indexHigh]
        return interpretationHigh.interpretation_max + 1
      }
    },
    getMaxValue(index) {
      const interpretation = this.interpretations[index]
      if (interpretation.interpretation == 'Bajo') {
        const indexMedium = index + 1
        const interpretationMedium = this.interpretations[indexMedium]
        const maxValue = interpretationMedium.interpretation_min - 1
        return maxValue < 0 ? 100 : maxValue
      }
      if (interpretation.interpretation == 'Medio') {
        const indexHigh = index + 1
        const interpretationHigh = this.interpretations[indexHigh]
        const maxValue = interpretationHigh.interpretation_min - 1
        return maxValue < 0 ? 100 : maxValue
      }
      if (interpretation.interpretation == 'Alto') {
        return 100
      }
    },
    validateRanges() {
      return this.interpretations.reduce((sum, item) => sum + (item.interpretation_max - item.interpretation_min + 1), 0) === 100;
    },
    async showModal() {
      this.reset()
      this.loadValues()
      this.dialog = true
    },
    async loadValues() {
      try {
        this.showLoadingMixin()
        const { data } = await getRiskInterpretations(this.register.id_risk_category)
        data.data.forEach(int => {
          const index = this.interpretations.findIndex(item => item.interpretation == int.interpretation)
          this.interpretations[index].interpretation_min = int.interpretation_min
          this.interpretations[index].interpretation_max = int.interpretation_max
        })
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setRiskInterpretation() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        if (!this.isValid) {
          this.alertMessageOk('Por favor revisa el ejemplo de uso de rangos para que todos los campos sumen un total de 100', 'info')
          return
        }
        this.showLoadingMixin()
        const form = { interpretations: this.interpretations }
        const { data } = await storeRiskInterpretation(this.register.id_risk_category, form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.interpretations.forEach((item, index) => {
        this.interpretations[index].interpretation_min = 0
        this.interpretations[index].interpretation_max = 0
      });
    }
  }
}
</script>

<style>

</style>