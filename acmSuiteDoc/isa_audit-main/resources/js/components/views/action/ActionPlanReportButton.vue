<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      :class="classButton"
      variant="success"
      v-b-tooltip.hover.left
      title="Descargar reporte"
      @click="showModal"
    >
      <b-icon icon="file-earmark-excel" aria-hidden="true"></b-icon> 
      {{ titleButton }}
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>

        <b-row>
          <b-col sm="12" md="6">
            <b-form-group>
              <label>Materia</label>
              <multiselect 
                v-model="filters.matters" 
                :options="matters" 
                multiple 
                placeholder="Todos"
                label="matter" 
                track-by="id_matter"
                selectLabel="Clic para seleccionar"
                selectedLabel="Seleccionado"
                deselectLabel="Clic para remover"
              >
                <template slot="noOptions">
                  No se encontraron registros
                </template>
              </multiselect>
            </b-form-group>
          </b-col>
          <b-col sm="12" md="6">
            <b-form-group>
              <label>Aspecto</label>
              <multiselect 
                v-model="filters.aspects" 
                :options="aspects" 
                multiple 
                placeholder="Todos"
                label="aspect" 
                track-by="id_aspect"
                selectLabel="Clic para seleccionar"
                selectedLabel="Seleccionado"
                deselectLabel="Clic para remover"
              >
                <template slot="noOptions">
                  No se encontraron registros
                </template>
              </multiselect>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12" md="6">
            <b-form-group>
              <label>
                Estatus
              </label>
              <multiselect 
                v-model="filters.status" 
                :options="status" 
                multiple 
                placeholder="Todos"
                label="status" 
                track-by="id_status"
                selectLabel="Clic para seleccionar"
                selectedLabel="Seleccionado"
                deselectLabel="Clic para remover"
              >
                <template slot="noOptions">
                  No se encontraron registros
                </template>
              </multiselect>
            </b-form-group>
          </b-col>
          <b-col sm="12" md="6">
            <b-form-group>
              <label>
                Prioridad
              </label>
              <multiselect 
                v-model="filters.priorities" 
                :options="priorities" 
                multiple 
                placeholder="Todos"
                label="priority" 
                track-by="id_priority"
                selectLabel="Clic para seleccionar"
                selectedLabel="Seleccionado"
                deselectLabel="Clic para remover"
              >
                <template slot="noOptions">
                  No se encontraron registros
                </template>
              </multiselect>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12" md="6">
            <b-form-group>
              <label>
                Rango de fechas
              </label>  
              <vue-date-picker 
                input-class="form-control"
                format="DD/MM/YYYY"
                value-type="YYYY-MM-DD"
                range 
                v-model="filters.range"
                placeholder="Todos"
              ></vue-date-picker>
            </b-form-group>
          </b-col>
          <b-col sm="12" md="6">
            <label>Responsable de aprobación/tareas/subtareas</label>
            <multiselect 
              v-model="filters.users" 
              :options="users" 
              multiple 
              placeholder="Todos"
              label="full_name" 
              track-by="id"
              selectLabel="Clic para seleccionar"
              selectedLabel="Seleccionado"
              deselectLabel="Clic para remover"
            >
              <template slot="noOptions">
                No se encontraron registros
              </template>
            </multiselect>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <b-form-group>
              <b-form-checkbox v-model="filters.with_task" size="lg">
                Incluir Tareas
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.with_subtask" size="lg">
                Incluir Subtareas
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.with_level_risk" size="lg">
                Incluir Nivel de Riesgo
              </b-form-checkbox>
            </b-form-group>
          </b-col>
        </b-row>

      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button
            variant="success" 
            class="float-right mr-2"
            @click="getActionPlanReport"
          >
            Desacargar
          </b-button>
          <b-button
            variant="danger" 
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cancelar
          </b-button>
          <b-button
            variant="primary" 
            class="float-right mr-2"
            @click="resetFields"
          >
            Limpiar Filtros
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import { getPriorities, getStatus } from '../../../services/catalogService'
import { getSingleProcess } from '../../../services/catalogSingleService'
import { getActionPlanReport } from '../../../services/ActionRegisterService'
import { allOrigins } from '../constants/origins'

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
    titleButton: {
      type: String,
      required: false,
      default: 'Reporte'
    },
    size: {
      type: String,
      required: false,
      default: 'sm'
    },
    classButton: {
      type: String,
      required: false,
      default: ''
    }
  },
  data() {
    return {
      dialog: false,
      process: null,
      status: [],
      priorities: [],
      filters: {
        matters: [],
        aspects: [],
        status: [],
        priorities: [],
        users: [],
        range: [],
        with_task: true,
        with_subtask: true,
        with_level_risk: true,
      }
    }
  },
  computed: {
    titleModal() {
      return `Reporte de Plan de Acción de ${ allOrigins(this.origin) }`
    },
    matters() {
      this.filters.aspects = []
      const { contract_matters } = this.process?.aplicability_register ?? {}
      return contract_matters?.map(item => item.matter).sort((a,b) => a.order - b.order) ?? []
    },
    aspects() {
      const { contract_matters } = this.process?.aplicability_register ?? {}
      this.filters.aspects = []
      if (this.filters.matters.length) {
        const aspects = contract_matters.filter(matter => {
          return this.filters.matters.some(matterSelected => matterSelected.id_matter === matter.id_matter)
        }).flatMap(matterBack => matterBack.contract_aspects).map(aspect => aspect.aspect).sort((a,b) => a.order - b.order)
        return aspects
      }
      return contract_matters?.flatMap(matter => matter.contract_aspects.map(aspect => aspect.aspect).sort((a,b) => a.order - b.order)) ?? []
    },
    users() {
      const { users } = this.process?.corporate ?? {}
      return users?.map( ({id_user, person}) => ({id: id_user, full_name: person.full_name}) ) ?? []
    },
  },
  methods: {
    async showModal() {
      this.resetFields()
      await this.getProcess()
      await this.getPriorities()
      await this.getStatus()
      this.dialog = true
    },
    async getProcess() {
      try {
        const params = { scope: 'users' }
        const { data } = await getSingleProcess(this.idAuditProcess, params)
        this.process = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getPriorities() {
      try {
        const { data } = await getPriorities()
        this.priorities = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getStatus() {
      try {
        const filters = { group: 7 }
        const { data } = await getStatus({}, filters)
        this.status = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getActionPlanReport() {
      try {
        this.showLoadingMixin()
        const filters = this.normalizeFilters()
        const { data, headers } = await getActionPlanReport(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, filters)
        this.responseFileMixin(data, headers)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    normalizeFilters() {
      return {
        matters: this.filters.matters.flatMap(item => item.id_matter),
        aspects: this.filters.aspects.flatMap(item => item.id_aspect),
        status: this.filters.status.flatMap(item => item.id_status),
        priorities: this.filters.priorities.flatMap(item => item.id_priority),
        users: this.filters.users.flatMap(item => item.id),
        range: this.filters.range,
        with_task: this.filters.with_task.toString(),
        with_subtask: this.filters.with_subtask.toString(),
        with_level_risk: this.filters.with_level_risk.toString(),
      }
    },
    resetFields() {
      this.filters.matters = []
      this.filters.aspects = []
      this.filters.status = []
      this.filters.priorities = []
      this.filters.users = []
      this.filters.range = []
      this.filters.with_task = true
      this.filters.with_subtask = true
      this.filters.with_level_risk = true
    }
  }
}
</script>

<style>

</style>