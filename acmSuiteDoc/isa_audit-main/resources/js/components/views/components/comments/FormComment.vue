<template>
  <b-container>
    <loading :show="loadingMixin" />
    <validation-observer ref="rulesForm">
      <b-form
        ref="formRegister"
        autocomplete="off"
      >
        <b-row>
          <b-col cols="12">
            <b-form-group>
              <label>
                Comentario
                <span class="text-danger">*</span>
              </label>
              <validation-provider
                #default="{ errors }"
                rules="required"
                name="Comentario"
              >
                <b-form-textarea
                  v-model="form.comment"
                  placeholder="Escribe el comentario"
                  rows="3"
                  max-rows="6"
                ></b-form-textarea>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
        </b-row>
        <div class="d-flex flex-wrap justify-content-end w-100">
           <b-button 
            variant="danger"
            class="ml-2 mr-2"
            @click="$emit('reset')"
          >
            Cancelar
          </b-button>
          <b-button v-if="isNew"
            variant="success"
            class="ml-2 mr-2"
            @click="setComment"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="ml-2 mr-2"
            @click="updateComment"
          >
            Actualizar
          </b-button>
        </div>
      </b-form>
    </validation-observer>
  </b-container>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '../../../validations'
import { setTaskComment, updateTaskComment } from '../../../../services/taskCommentService'

export default {
  mounted() {
    if ( this.isNew ) return
    this.form.comment = this.content
  },
   components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    moduleName: {
      type: String,
      required: true,
       validator: value => {
        const types = ['task']
        return types.indexOf(value) !== -1
      }
    },
    paramsUrl: {
      type: Object,
      required: true,
    },
    idComment: {
      type: Number,
      required: false,
    },
    content: {
      type: String,
      required: false,
    },
  },
  data() {
    return {
      required,
      form: {
        comment: '',
      }
    }
  },
  computed: {
    isNew() {
      return this.idComment == 0
    }
  },
  methods: {
    async setComment() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return

        this.showLoadingMixin()
        let response = null
        if ( this.moduleName == 'task' ) {
          const { idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask } = this.paramsUrl
          const { data } = await setTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, this.form)
          response = data
        }
        this.responseMixin(response)
        this.$emit('successfully')
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateComment() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return

        this.showLoadingMixin()
        let response = null
        if ( this.moduleName == 'task' ) {
          const { idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask } = this.paramsUrl
          const { data } = await updateTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, this.idComment, this.form)
          response = data
        }
        this.responseMixin(response)
        this.$emit('successfully')
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