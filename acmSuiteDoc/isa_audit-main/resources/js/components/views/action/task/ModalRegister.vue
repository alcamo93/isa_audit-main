<template>
  <span v-if="userCanModifyTask">
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
      title="Editar"
      variant="warning"
      class="btn-link"
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
                    No. Requerimiento 
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    placeholder="Identificación de Tarea"
                    rules="required"
                    name="No. Requerimiento"
                  >
                    <b-form-input
                      :disabled="true"
                      v-model="form.title"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
             <!-- range dates -->
             <range-dates 
              :init-date.sync="form.init_date"
              :end-date.sync="form.close_date"
              :dates.sync="form.notify_dates"
              :max-date="maxDate"
            />
            <b-row>
              <b-col cols="12">
                <b-form-group>
                  <label>
                    {{ typeTask }}
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    :name="typeTask"
                  >
                    <b-form-textarea
                      v-model="form.task"
                      :placeholder="`Descripción de ${typeTask}`"
                      :disabled="mainTask"
                      rows="3"
                      max-rows="6"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <!-- users -->
            <users-task v-if="dialog"
              :auditors.sync="form.auditors"
              :id-audit-process="idAuditProcess"
              :is-main="Boolean(form.main_task)"
            />
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button v-if="isNew"
            variant="success"
            class="float-right"
            @click="setTask"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateTask"
          >
            Actualizar
          </b-button>
          <b-button
            variant="success" 
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cancelar
          </b-button>
        </div>
      </template>
    </b-modal>
  </span>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import RangeDates from './components/RangeDates.vue'
import UsersTask from './UsersTask.vue'
import { required } from '../../../validations'
import { getTask, setTask, updateTask } from '../../../../services/taskService'
import { getNoRequirementText } from '../../components/scripts/texts'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    RangeDates,
    UsersTask,
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
    origin: {
      type: String,
      required: true,
    },
    idSectionRegister: {
      type: Number,
      required: true,
    },
    idActionRegister: {
      type: Number,
      required: true,
    },
    idActionPlan: {
      type: Number,
      required: true,
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
    noRequirement: {
      type: String,
      required: false,
      default: ''
    },
    permissions: {
      type: Object,
      required: false,
      default: function () {
        return {
          can_approve: true,
          can_upload: true
        }
      }
    },
    maxDate: {
      type: [String, null],
      required: false,
    },
  },
  data() {
    return {
      required,
      dialog: false,
      show: false,
      users: [],
      form: {
        title: null,
        task: null,
        init_date: null,
        close_date: null,
        main_task: false, 
        auditors: [],
        notify_dates: [],
      },
    }
  },
  computed: {
    typeTask() {
      return this.form.main_task ? 'Tarea' : 'Subtarea'
    },
    mainTask() {
      return Boolean(this.form.main_task)
    },
    titleModal() {
      return this.isNew ? `Agregar ${this.typeTask}` : `Editar ${this.typeTask}: ${this.register.title}`
    },
    userCanModifyTask() {
      const { can_approve, can_upload } = this.permissions
      return can_upload || can_approve
    },
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
      this.form.title = this.noRequirement
      this.showLoadingMixin()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.register.id_task)
        const { title, task, init_date, close_date, main_task, auditors, notifications } = data.data
        this.form.title = title
        this.form.task = task
        this.form.init_date = init_date
        this.form.close_date = close_date
        this.form.main_task = main_task
        this.form.auditors = auditors
        this.form.notify_dates = notifications.map(item => item.date)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setTask() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await setTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateTask() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.register.id_task, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    disabledDateMin(date) {
      return date < new Date(this.form.init_date)
    },
    reset() {
      this.form.title = null
      this.form.task = null
      this.form.init_date = null
      this.form.close_date = null
      this.form.main_task = false
      this.form.auditors = []
      this.form.notify_dates = []
    }
  }
}
</script>

<style>

</style>