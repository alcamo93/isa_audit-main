<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-if="validateOpenTask && validateOpenMainTask"
      v-b-tooltip.hover.left
      title="Panel de Tareas"
      class="btn-link"
      variant="success"
      @click="showTasks"
    >
      <b-icon icon="folder2-open" aria-hidden="true"></b-icon>
    </b-button>
    <b-button
      v-if="validateOpenTask && !validateOpenMainTask"
      v-b-tooltip.hover.left
      title="Comenzar tareas"
      class="btn-link"
      variant="success"
      @click="showModal"
    >
      <b-icon icon="folder2-open" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      no-close-on-backdrop
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <!-- range dates -->
            <range-dates 
              :init-date.sync="form.init_date"
              :end-date.sync="form.close_date"
              :dates.sync="form.notify_dates"
            />
            <!-- users -->
            <users-task v-if="dialog"
              :auditors.sync="form.auditors"
              :id-audit-process="idAuditProcess"
              is-main
            />
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button
            variant="success"
            class="float-right"
            @click="updateMainTask"
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
import { required } from '../../../validations'
import { updateMainTask } from '../../../../services/taskService'
import { getNoRequirementText } from '../../components/scripts/texts'
import UsersTask from './UsersTask.vue'
import RangeDates from './components/RangeDates.vue'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    UsersTask,
    RangeDates,
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
    action: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      required,
      dialog: false,
      form: {
        init_date: null,
        close_date: null,
        notify_dates: [],
        auditors: [],
        main_task: true
      },
    }
  },
  computed: {
    titleModal() {
      const name = getNoRequirementText(this.action)
      return `Especificar Fechas y Resposables de Tarea para ${name}` 
    },
    validateOpenTask() {
      const hasAuditor = this.action.auditors.length > 0
      return hasAuditor
    },
    validateOpenMainTask() {
      return  this.action.main_task_is_complete == 1 ? true : false
    },
  },
  methods: {
    async showModal() {
      this.reset()
      this.dialog = true
    },
    async updateMainTask() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await updateMainTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.action.main_task_id, this.form)
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
      this.form.init_date = null
      this.form.close_date = null
      this.form.auditors = []
      this.form.notify_dates = []
    },
    showTasks() {
      const host = window.location.origin
      const idActionPlan = this.action.id_action_plan
      const url = `${host}/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/${this.origin}/${this.idSectionRegister}/action/${this.idActionRegister}/plan/${idActionPlan}/task/view`
      window.location.href = url
    }
  }
}
</script>

<style>
  
</style>
