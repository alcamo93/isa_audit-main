<template>
  <fragment>
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
      title="Editar Respuesta"
      variant="warning"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      no-close-on-backdrop
    >
      <loading :show="loadingMixin" />
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Respuesta <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Respuesta"
                  >
                    <b-form-input
                      v-model="form.description"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Orden <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|integer"
                    name="Orden"
                  >
                    <b-form-input
                      v-model="form.order"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Valor de Respuesta <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Valor de Respuesta"
                  >
                    <v-select 
                      v-model="form.id_answer_value"
                      :options="answerValues"
                      :reduce="e => e.id_answer_value"
                      label="answer_value"
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
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button v-if="isNew"
            variant="success"
            class="float-right"
            @click="setQuestionAnswer"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateQuestionAnswer"
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
import { required, integer } from '../../../../../validations'
import { getQuestionAnswer, setQuestionAnswer, updateQuestionAnswer } from '../../../../../../services/questionAnswerService'
import { getAnswerValue } from '../../../../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    idForm: {
      type: Number,
      required: true
    },
    idQuestion: {
      type: Number,
      required: true
    },
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    register: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      required,
      integer,
      dialog: false,
      answerValues: [],
      form: {
        description: null,
        order: null,
        id_answer_value: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Respuesta ' : `Editar: ${this.register.description}`
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadNewRegister()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadNewRegister() {
      this.showLoadingMixin()
      await this.getAnswerValue()
      this.showLoadingMixin()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getQuestionAnswer(this.idForm, this.idQuestion, this.register.id_answer_question)
        const { description, order, id_answer_value } = data.data
        this.form.description = description
        this.form.order = order
        this.form.id_answer_value = id_answer_value
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getAnswerValue() {
      try {
        const { data } = await getAnswerValue()
        this.answerValues = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setQuestionAnswer() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await setQuestionAnswer(this.idForm, this.idQuestion, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateQuestionAnswer() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateQuestionAnswer(this.idForm, this.idQuestion, this.register.id_answer_question, this.form)
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
      this.form.description = null
      this.form.id_answer_value = null
      this.form.order = null
    }
  }
}
</script>

<style>

</style>