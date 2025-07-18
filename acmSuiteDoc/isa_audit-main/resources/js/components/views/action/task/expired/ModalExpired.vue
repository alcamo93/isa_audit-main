<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-if="showExpired"
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="danger"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="calendar2-x" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <form-expired v-if="validateExpired"
          ref="formExpired"
          :record="record"
          :cause.sync="form.cause"
          :extension-date.sync="form.extension_date"
        />
        <table-expired v-if="hasHistorialCasuse"
          :historicals="historicals"
        />
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button v-if="validateExpired"
            variant="success"
            class="float-right"
            @click="setExpired"
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
import { getTask, updateTaskExpired } from '../../../../../services/taskService'
import FormExpired from './FormExpired.vue'
import TableExpired from './TableExpired.vue'

export default {
  components: { FormExpired, TableExpired },
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
    idTask: {
      type: Number,
      required: true,
    },
    keyStatus: {
      type: String,
      required: true,
    },
    hasHistorialCasuse: {
      type: Boolean,
      required: true,
    }
  },
  data() {
    return {
      dialog: false,
      record: null,  
      historicals: [],
      form: {
        cause: null,
        extension_date: null
      }
    }
  },
  computed: {
    titleModal() {
      return `Causas de Vencimiento de Tarea`
    },
    titleTooltip() {
      const text = this.keyStatus == 'EXPIRED_TASK' ? 'Establecer' : 'Mostrar'
      return `${text} causas de vencimiento`
    },
    validateExpired() {
      return this.keyStatus == 'EXPIRED_TASK'
    },
    showExpired() {
      return this.keyStatus == 'EXPIRED_TASK' || this.hasHistorialCasuse
    }
    
  },
  methods: {
    async showModal() {
      this.reload()
      this.showLoadingMixin()
      await this.getTask()
      this.showLoadingMixin()
      this.dialog = true
    },
    async setExpired() {
      try {
        const isValid = await this.$refs.formExpired.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await updateTaskExpired(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.idTask, this.form)
        const response = data
        this.dialog = false
        this.showLoadingMixin()
        this.$emit('successfully')
        this.responseMixin(response)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getTask() {
      try {
        this.showLoadingMixin()
        const { data } = await getTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.idTask, this.form)
        this.record = data.data
        this.historicals = data.data.expired
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reload() {
      this.form.cause = null
      this.form.extension_date = null
    }
  }
}
</script>

<style>

</style>