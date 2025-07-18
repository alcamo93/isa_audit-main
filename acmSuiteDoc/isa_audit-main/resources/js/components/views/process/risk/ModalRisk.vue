<template>
  <span>
    <loading :show="loadingMixin" />
    <b-button v-if="!riskComplete"
      :disabled="disabledModal"
      v-b-tooltip.hover.left
      :title="titleTooltip"
      :variant="variant"
      @click="showModal"
      :size="size"
    >
      {{ titleButton }}
    </b-button>
    <b-link v-else
      :disabled="disabledModal"
      v-b-tooltip.hover.left
      :title="titleTooltip"
      @click="showModal" 
    >
      <div class="d-flex align-items-center flex-column">
        <b-badge v-for="category in record.risk_totals" :key="category.id_risk_category"        
          class="mb-1" variant="info"
        >
          {{ category.category.risk_category }}: {{ category.interpretation }}
        </b-badge>
      </div>
    </b-link>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true" 
      @hidden="closeModal"
    >
      <b-container fluid>
        <b-row>
          <b-col v-for="(category, categoryIndex) in categories" :key="category.id_risk_category" 
            col-sm="12" col-md="6" col-lg="4"
            class="justify-content-center"
          >
            <h5 class="title text-center">
              {{ category.risk_category }}
            </h5>
            <h6 class="title text-center badge-risk">
              {{ categories[categoryIndex].value_category }}
            </h6>
            <b-row cols="3" class="justify-content-center" 
              v-for="(attribute, attributeIndex) in category.attributes" :key="attribute.id_risk_attribute"
            >
              <b-col cols="10">
                <b-form-group>
                  <div class="d-flex justify-content-start">
                    <label>{{ attribute.risk_attribute }}</label>
                    <modal-risk-help
                      :record="attribute"
                    />
                  </div>
                  <b-form-select
                    v-model="categories[categoryIndex].attributes[attributeIndex].value_attribute"
                    @change="setRiskAnswer(
                      categories[categoryIndex].attributes[attributeIndex].value_attribute, 
                      category.id_risk_category, 
                      attribute.id_risk_attribute
                    )"
                    :options="attribute.helps"
                    value-field="value"
                    text-field="risk_help"
                  ></b-form-select>
                </b-form-group>
              </b-col>
            </b-row>
          </b-col>
        </b-row>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="closeModal"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </span>
</template>

<script>
import ModalRiskHelp from './ModalRiskHelp.vue'
import { getCategoryRisksSource } from '../../../../services/catalogService'
import { getNoRequirementText } from '../../components/scripts/texts'
import { setRiskEvaluate, getRiskEvaluate } from '../../../../services/riskEvaluateService'

export default {
  components: {
    ModalRiskHelp
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    idAplicabilityRegister: {
      type: Number,
      required: true,
    },
    idRegisterSection: {
      type: Number,
      required: true,
    },
    registerableType: {
      type: String,
      required: true,
    },
    registerableId: {
      type: Number,
      required: true,
    },
    record: {
      type: Object,
      required: true,
    },
    disabledModal: {
      type: Boolean,
      required: false,
      default: false
    },
    size: {
      type: String,
      required: false,
      default: 'sm'
    },
    titleButton: {
      type: String,
      required: false,
      default: 'Nivel de Riesgo'
    },
    variant: {
      type: String,
      required: false,
      default: 'info'
    }
  },
  data() {
    return {
      dialog: false,
      categories: []
    }
  },
  computed: {
    titleModal() {
      const title = 'Nivel de Riesgo'
      if ( !this.record.hasOwnProperty('id_obligation') || !this.record.hasOwnProperty('id_audit') ) return title
      const name = getNoRequirementText(this.record)
      return `${title} para ${name}`
    },
    riskComplete() {
      if ( !this.record.hasOwnProperty('risk_totals') ) return false
      const totalCategories = 3
      return this.record.risk_totals.length == totalCategories
    },
    titleTooltip() {
      const word = this.riskComplete ? 'Abrir' : 'Evaluar'
      return `${word} Nivel de Riesgo`
    },
  },
  methods: {
    async showModal() {
      await this.getRiskCategories()
      await this.getRiskAnswers()
      this.dialog = true
    },
    closeModal() {
      this.$emit('successfully')
      this.dialog = false
    },
    async getRiskCategories() {
      try {
        this.showLoadingMixin()
        const { data } = await getCategoryRisksSource()
        this.categories = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
      }
    },
    async getRiskAnswers() {
      try {
        this.showLoadingMixin()
        const { data } = await getRiskEvaluate(this.idAuditProcess, this.idAplicabilityRegister, this.registerableType, this.idRegisterSection, this.registerableId)
        this.getRisk(data.data)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
      }
    },
    async setRiskAnswer(value, idRiskCategory, idRiskAttribute) {
      try {
        this.showLoadingMixin()
        const request = {
          answer: value,
          id_risk_category: idRiskCategory,
          id_risk_attribute: idRiskAttribute
        }
        const { data } = await setRiskEvaluate(this.idAuditProcess, this.idAplicabilityRegister, this.registerableType, this.idRegisterSection, this.registerableId, request)
        await this.getRiskAnswers()
        this.responseMixin(data)
        this.showLoadingMixin()
      } catch (error) {
        this.responseMixin(error)
        this.showLoadingMixin()
      }
    },
    getRisk({risk_answers, risk_totals}) {
      risk_answers.forEach(answer => {
        const findCategory = this.categories.findIndex(category => answer.id_risk_category == category.id_risk_category)
        const findAttribute = this.categories[findCategory].attributes.findIndex(attribute => attribute.id_risk_attribute == answer.id_risk_attribute)
        this.categories[findCategory].attributes[findAttribute].value_attribute = answer.answer
      })
      risk_totals.forEach(total => {
        const findCategory = this.categories.findIndex(category => total.id_risk_category == category.id_risk_category)
        this.categories[findCategory].value_category = total.interpretation
      })
    }
  }
}
</script>

<style>
  .badge-risk {
    color: #fff;
    background-color: #28a745;
    border-radius: 10px;
  }
</style>