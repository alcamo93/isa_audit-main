<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-if="isNew"
      class="float-right"
      variant="success"
      @click="showModal"
    >
      Agregar 
      <b-icon icon="plus" aria-hidden="true"></b-icon>
    </b-button>

    <b-button v-else
      v-b-tooltip.hover.left 
      title="Editar Pregunta"
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
      :no-close-on-backdrop="true"
      :no-enforce-focus="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="4">
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
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Permitir respuesta multiple
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Permitir respuesta multiple"
                  >
                    <v-select 
                      v-model="form.allow_multiple_answers"
                      :options="options"
                      :reduce="e => e.id_option"
                      label="option"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Tipo de Pregunta
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Tipo de Pregunta"
                  >
                    <v-select 
                      v-model="form.id_question_type"
                      :options="questionTypes"
                      :reduce="e => e.id_question_type"
                      label="question_type"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" :md="columnsFormLocation" v-if="this.useStateOptions">
                <b-form-group>
                 <label>
                    Estado <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Estado"
                  >
                    <v-select 
                      v-model="form.id_state"
                      :options="states"
                      :reduce="e => e.id_state"
                      label="state"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" :md="columnsFormLocation" v-if="useCityOptions">
                <b-form-group>
                 <label>
                    Ciudad <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Ciudad"
                  >
                    <v-select 
                      v-model="form.id_city"
                      :options="cities"
                      :reduce="e => e.id_city"
                      label="city"
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
                    Pregunta
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Pregunta"
                  >
                    <b-form-textarea
                      max-rows="9"
                      v-model="form.question"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Ayuda 
                  </label>
                  <rich-text-edit 
                    field="help_question"
                    placeholder="Especifica el formato del texto del Ayuda..."
                    :initial-content="form.help_question"
                    :enable-image="true"
                    @input="getContent"
                  />
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
            class="float-right"
            variant="success"
            @click="setQuestion"
          >
            Registrar
          </b-button>
          <b-button v-else
            class="float-right"
            variant="success"
            @click="updateQuestion"
          >
            Actualizar
          </b-button>
          <b-button 
            class="float-right mr-2"
            variant="danger"
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
import { required, max, integer,  } from '../../../../validations'
import { getQuestion, storeQuestion, updateQuestion } from '../../../../../services/questionsService'
import { getStates, getCities, getQuestionTypes } from '../../../../../services/catalogService'
import RichTextEdit from '../../../components/rich_text/RichTextEdit'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    RichTextEdit,
  },
  props: {
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    idForm: {
      type: Number,
      required: true
    },
    register: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      required,
      max,
      integer,
      dialog: false,
      loadDataActive: false,
      questionTypes: [],
      state: [],
      cities: [],
      options: [
        { id_option: '1', option: 'Sí, si permitir' },
        { id_option: '0', option: 'No, no permitir' }
      ],
      form: {
        order: null,
        question: null,
        help_question: '',
        allow_multiple_answers: null,
        id_question_type: null,
        id_state: null,
        id_city: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Pregunta ' : `Editar: ${this.register.question}`
    },
    useStateOptions() {
      return this.form.id_question_type == 2 || this.form.id_question_type == 4
    },
    useCityOptions() {
      return this.form.id_question_type == 4
    },
    columnsFormLocation() {
      const columns = this.useStateOptions & this.useCityOptions ? 2 : 1
      return (12 / columns)
    },
  },
  watch: {
    'form.id_question_type': function(value) {
      if (this.loadDataActive) return
      this.form.id_state = null
      this.form.id_city = null
      this.cities = []
    },
    'form.id_state': function(value) {
      if (!this.loadDataActive) {
        this.id_city = null
        this.cities = []
      }
      if (value != null & this.useCityOptions) {
        this.getCities()
      } else {
        this.cities = []
      }
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadLists()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadLists() {
      await this.getStates()
      await this.getQuestionTypes()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        this.loadDataActive = true
        const { data } = await getQuestion(this.idForm, this.register.id_question)
        const { order, question, help_question_env, allow_multiple_answers, 
          id_question_type, id_state, id_city } = data.data
        this.form.order = order
        this.form.question = question
        this.form.help_question = help_question_env ?? ''
        this.form.allow_multiple_answers = allow_multiple_answers.toString()
        this.form.id_question_type = id_question_type
        this.form.id_state = id_state
        this.form.id_city = id_city
        setTimeout(() => {
          this.loadDataActive = false
        }, 500);
        this.showLoadingMixin()
      } catch (error) {
        this.loadDataActive = false
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setQuestion() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeQuestion(this.idForm, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateQuestion() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateQuestion(this.idForm, this.register.id_question, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getQuestionTypes() {
      try {
        this.showLoadingMixin()
        const params = { option: 'main' }
        const { data } = await getQuestionTypes(params)
        this.questionTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getStates() {
      try {
        this.showLoadingMixin()
        const { data } = await getStates()
        this.states = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getCities() {
      try {
        this.showLoadingMixin()
        const filters = { id_state: this.form.id_state }
        const { data } = await getCities({}, filters)
        this.cities = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getContent(field, content) {
      if (field == 'help_question') {
        this.form.help_question = content 
      }
    },
    reset() {
      this.form.order = null
      this.form.question = null
      this.form.help_question = ''
      this.form.allow_multiple_answers = null
      this.form.id_question_type = null
      this.form.id_state = null
      this.form.id_city = null
    },
  }
}
</script>

<style>

</style>