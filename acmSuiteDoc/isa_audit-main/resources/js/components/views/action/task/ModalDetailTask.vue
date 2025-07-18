<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      variant="info"
      v-b-tooltip.hover.left
      title="Mostrar detalles"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="eye-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        
        <b-row>
          <b-col cols="6">
            <label>
              No. Requerimiento
            </label>
            <div class="font-weight-bold">
              {{ form.title }}
            </div>
          </b-col>
          <b-col cols="6">
            <label>
              Tipo de Tarea
            </label>
            <div class="font-weight-bold">
              <b-badge 
                pill 
                :variant="form.main_task ? 'success' : 'primary'"
              >
                {{ form.main_task ? 'Tarea' : 'Subtarea' }}
              </b-badge>
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="6">
            <label>
              Fecha de Inicio
            </label>
            <div class="font-weight-bold">
              {{ form.init_date }}
            </div>
          </b-col>
          <b-col cols="6">
            <label>
              Fecha de Cierre
            </label>
            <div class="font-weight-bold">
              {{ form.close_date }}
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <label>
              Tarea
            </label>
            <div class="font-weight-bold">
              {{ form.task }}
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <b-table-simple hover small caption-top responsive>
              <b-thead>
                <b-tr class="text-center font-weight-bold text-uppercase">
                  <b-th variant="info" colspan="2">
                    Resposables de Tarea
                  </b-th>
                </b-tr>
                <b-tr>
                  <b-th class="text-center">Tipo</b-th>
                  <b-th class="text-center">Nombre</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>
                <b-tr v-for="auditor in form.auditors" :key="auditor.id_user">
                  <b-td class="text-center">
                    {{ getTypeResponsible(auditor.pivot.level) }}
                  </b-td>
                  <b-td class="text-center">
                    {{ auditor.person.full_name }}
                  </b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </b-col>
        </b-row>

      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
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
import { getTask } from '../../../../services/taskService'

export default {
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
  },
  data() {
    return {
      dialog: false,
      form: {
        title: null,
        task: null,
        init_date: null,
        close_date: null,
        main_task: false, 
        auditors: []
      },
    }
  },
  computed: {
    titleModal() {
      if (this.form.title == null) return ''
      const type = this.form.main_task ? 'Tarea' : 'Subtarea'
      return `${type}: ${this.form.title}`
    },
  },
  methods: {
    async getTask() {
      try {
        this.showLoadingMixin()
        const { data } = await getTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.idTask)
        const { title, task, init_date_format, close_date_format, main_task, auditors } = data.data
        this.form.title = title
        this.form.task = task
        this.form.init_date = init_date_format
        this.form.close_date = close_date_format
        this.form.main_task = main_task
        this.form.auditors = auditors
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async showModal() {
      this.showLoadingMixin()  
      await this.getTask()    
      this.showLoadingMixin()
      this.dialog = true
    },
    getTypeResponsible(level) {
      const levels = {
        1: 'Responsable de cierre',
        2: 'Responsable secundario',
        3: 'Responsable terciario',
      }
      return levels[level]
    },
  }
}
</script>

<style>

</style>