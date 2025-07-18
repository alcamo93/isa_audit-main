<template>
  <fragment>

    <b-button 
      class="mb-0 p-1 btn-link"
      v-b-tooltip.hover.top 
      title="Comentarios"
      :variant="btnVariant"
      @click="showModal"
    >
      <b-icon icon="chat-quote-fill" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      no-close-on-backdrop
    >
      <b-container fluid>
        <span class="font-weight-bold text-justify mb-1">
          {{ question.question }}
        </span>

        <validation-observer ref="rulesFormProcess">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Comentario
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Comentario"
                  >
                    <b-form-textarea
                      v-model="form.comment"
                      placeholder="Escriba un comentario para esta pregunta..."
                      rows="3"
                      max-rows="25"
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
          <b-button
            variant="success"
            class="float-right"
            @click="setComment"
          >
            Registrar
          </b-button>
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>

  </fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, max } from '../../../../validations'
import { setComment } from '../../../../../services/applicabilityService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true
    },
    idAplicabilityRegister: {
      type: Number,
      required: true
    },
    idContractMatter: {
      type: Number,
      required: true
    },
    idContractAspect: {
      type: Number,
      required: true
    },
    idEvaluateQuestion: {
      type: Number,
      required: true
    },
    question: {
      type: Object,
      required: true,
      default: null
    },
    comment: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      required,
      max,
      dialog: false,
      form: {
        comment: null
      }
    }
  },
  computed: {
    titleModal() {
      return `Comentarios para pregunta: `
    },
    btnVariant() {
      return this.comment != '' ? 'success' : 'secondary'
    },
  },
  methods: {
    showModal() {
      this.form.comment = this.comment
      this.dialog = true
    },
    async setComment() {
      try {
        this.showLoadingMixin()
        const { data } = await setComment(this.idAuditProcess, this.idAplicabilityRegister, this.idContractMatter, this.idContractAspect, this.idEvaluateQuestion, this.form)
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
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