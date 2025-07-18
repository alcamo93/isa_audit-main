<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-if="isNew"
      variant="success"
      class="float-right"
      @click="showModal"
    >
      Agregar 
      <b-icon icon="plus" aria-hidden="true"></b-icon>
    </b-button>

    <b-button v-else
      v-b-tooltip.hover.left
      title="Editar valoración de Riesgo"
      class="btn-link"
      variant="warning"
      @click="showModal"
    >
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
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
            ref="formrecord"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Nombre <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Nombre"
                  >
                    <b-form-input
                      v-model="form.risk_help"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Valor <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|double"
                    name="Valor"
                  >
                    <b-form-input
                      v-model="form.value"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Atributo <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Atributo"
                  >
                    <v-select 
                      v-model="form.id_risk_attribute"
                      :options="riskAttributes"
                      label="risk_attribute"
                      :reduce="e => e.id_risk_attribute"
                      :disabled="!isNew"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Criterio
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Criterio"
                  >
                    <b-form-textarea
                      max-rows="9"
                      v-model="form.standard"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button v-if="isNew"
            variant="success"
            class="float-right"
            @click="setRiskCataegory"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateRiskHelp"
          >
            Actualizar
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
import { required, max, double } from '../../../../validations'
import { getRiskHelp, storeRiskHelp, updateRiskHelp } from '../../../../../services/riskService'
import { getRiskAttributesSource } from '../../../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    idRiskCategory: {
      type: Number,
      required: true
    },
    record: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      required,
      max,
      double,
      dialog: false,
      form: {
        risk_help: null,
        standard: null,
        value: null,
        id_risk_attribute: null
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar valoración de riesgo ' : `Editar: ${this.record.risk_help}`
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.getRiskAttributesSource()
      if (!this.isNew) await this.loadUpdaterecord()
      this.dialog = true
    },
    async loadUpdaterecord() {
      try {
        this.showLoadingMixin()
        const { data } = await getRiskHelp(this.idRiskCategory, this.record.id_risk_help)
        const { risk_help, standard, value, id_risk_attribute } = data.data
        this.form.risk_help = risk_help
        this.form.standard = standard
        this.form.value = value
        this.form.id_risk_attribute = id_risk_attribute
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setRiskCataegory() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeRiskHelp(this.idRiskCategory, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateRiskHelp() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateRiskHelp(this.idRiskCategory, this.record.id_risk_help, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
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
    reset() {
      this.form.risk_help = null
      this.form.standard = null
      this.form.value = null
      this.form.id_risk_attribute = null
    }
  }
}
</script>

<style>

</style>