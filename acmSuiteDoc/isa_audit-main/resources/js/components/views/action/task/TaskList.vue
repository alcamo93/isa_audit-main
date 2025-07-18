<template>
  <fragment>
    <loading :show="loadingMixin" />
    <filter-area :title="originNameFilter" opened>
      <template v-slot:action>
        <b-button class="mt-1 mr-2"
          variant="success"
          v-b-tooltip.hover.left
          title="Regresar a panel de Plan de Acción"
          @click="backProcess"
        >
          Regresar
        </b-button>
      </template>
      <filters
        :headers="headers"
        :origin="origin"
        ref="filtersArea"
      />
    </filter-area>
    <b-card>
      <b-card-text>
        <modal-register 
          v-if="!blockTasks"
          @successfully="getTasks"
          :id-audit-process="idAuditProcess"
          :id-aplicability-register="idAplicabilityRegister"
          :origin="origin"
          :id-section-register="idSectionRegister"
          :id-action-register="idActionRegister"
          :id-action-plan="idActionPlan"
          :no-requirement="headers.no_requirement"
          :max-date="headers.last_expired.extension_date"
          :is-new="true"
        />
      </b-card-text>
      <b-card-text>
        <b-table 
          responsive 
          striped 
          hover 
          show-empty
          empty-text="No hay registros que mostrar"
          :fields="headerTable" 
          :items="items"
        >
          <template #cell(main_task)="data">
            <b-badge 
              pill 
              :variant="data.item.main_task ? 'success' : 'primary'"
            >
              {{ getTextTypeTask(data.item) }}
            </b-badge>
          </template>
          <template #cell(title)="data">
            {{ data.item.title }}
          </template>
          <template #cell(task)="data">
            <modal-task-description
              :record="data.item"
            ></modal-task-description>
          </template>
          <template #cell(users)="data">
            <template>
              <b-avatar 
                variant="primary" 
                :src="getImageByUser(data.item)"
                size="4em"
              ></b-avatar>
              <br>
              <b-button class="btn-link go-to-process">
                {{  getNameByUser(data.item) }}
              </b-button>
            </template>
          </template>
          <template #cell(id_status)="data">
            <b-badge 
              v-if="data.item.status != null"
              pill 
              :variant="data.item.status.color"
            >
              {{ data.item.status.status }}
            </b-badge>
          </template>
          <template #cell(init_date)="data">
            <span>
              {{ data.item.init_date_format }}
            </span>
          </template>
          <template #cell(close_date)="data">
            <span>
              {{ data.item.close_date_format }}
            </span>
          </template>
          <template #cell(actions)="data">
            <!-- files -->
            <adapter-file-buttons 
              @successfully="getTasks"
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :id-section-register="idSectionRegister"
              :item="data.item"
              origin="Task"
              :show-library="Boolean(data.item.main_task)"
              :evaluateable-id="data.item.id_task"
            />
            <!-- button update -->
            <template v-if="!blockTasks">
              <modal-register 
                :permissions="data.item.permissions"
                :id-audit-process="idAuditProcess"
                :id-aplicability-register="idAplicabilityRegister"
                :origin="origin"
                :id-section-register="idSectionRegister"
                :id-action-register="idActionRegister"
                :id-action-plan="idActionPlan"
                :is-new="false"
                :register="data.item"
                :no-requirement="headers.no_requirement"
                :max-date="headers.last_expired.extension_date"
                @successfully="getTasks"
              />
              <!-- button comments -->
              <modal-comments
                :name-parent="data.item.title"
                module-name="task"
                :params-url="{
                idAuditProcess: idAuditProcess,
                idAplicabilityRegister: idAplicabilityRegister,
                section: origin,
                idSectionRegister: idSectionRegister,
                idActionRegister: idActionRegister,
                idActionPlan: idActionPlan,
                idTask: data.item.id_task,
              }"
              />
              <!-- button delete -->
              <b-button v-if="(!data.item.main_task) && (data.item.permissions.can_approve || data.item.permissions.can_upload)"
                variant="danger"
                v-b-tooltip.hover.left
                title="Eliminar"
                class="btn-link"
                @click="alertRemove(data.item)"
              >
                <b-icon icon="x-lg" aria-hidden="true"></b-icon>
              </b-button>
            </template>
            <template v-else>
              <modal-detail-task
                :id-task="data.item.id_task"
              />
            </template>
            <modal-expired
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :origin="origin"
              :id-section-register="idSectionRegister"
              :id-action-register="idActionRegister"
              :id-action-plan="data.item.id_action_plan"
              :id-task="data.item.id_task"
              :key-status="data.item.status.key"
              :has-historial-casuse="data.item.expired_exists"
              @successfully="getTasks"
            />
          </template>
        </b-table>
        <!-- Paginator -->
        <app-paginator
          :data-list="paginate"
          @pagination-data="changePaginate"
        />
        <!-- End Paginator -->
      </b-card-text>
    </b-card>
  </fragment>
</template>

<script>
import AppPaginator from '../../components/app-paginator/AppPaginator.vue'
import FilterArea from '../../components/slots/FilterArea.vue'
import Filters  from './Filters.vue'
import ModalRegister from './ModalRegister.vue'
import ModalTaskDescription from './ModalTaskDescription.vue'
import ModalDetailTask from './ModalDetailTask.vue'
import ModalExpired from './expired/ModalExpired.vue'
import ModalComments from '../../components/comments/ModalComments.vue'
import AdapterFileButtons from '../../components/files/AdapterFileButtons.vue'
import { getTasks, deleteTask } from '../../../../services/taskService'
import { getTextTypeTask } from '../../components/scripts/texts'
import { allOrigins } from '../../constants/origins'

export default {
  components: {
    AppPaginator,
    FilterArea,
    Filters,
    ModalRegister,
    ModalTaskDescription,
    ModalComments,
    ModalDetailTask,
    AdapterFileButtons,
    ModalExpired,
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
  },
  created() {
    this.titlePage = this.originName
  },
  mounted() {
    document.querySelector('#titlePage').innerHTML = this.originName
    this.getTasks()
  },
  data() {
    return {
      titlePage: '',
      action_plan: {},
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        id_matter: null,
        id_aspect: null,
        no_requirement: null,
        id_status: null,
        id_priority: null,
        dates: null,
        name: null,
      },
      headers: {
        status: {
          status: '---',
          color: 'light',
        },
        audit_process: '---',
        customer_name: '---',
        corporate_name: '---',
        scope: '---',
        full_requirement: '---',
        no_requirement: '---',
        matter: '---',
        aspect: '---',
        last_expired: {
          original_date_format: '---',
          extension_date_format: '---',
          extension_date: null,
          cause: '',
        }
      }
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'main_task',
          label: 'Tipo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'title',
          label: 'No. Requerimiento',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'task',
          label: 'tarea',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'users',
          label: 'Responsable de tarea',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'id_status',
          label: 'Estatus',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'init_date',
          label: 'Fecha Inicio',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'close_date',
          label: 'Fecha Cierre',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'actions',
          label: 'Acciones',
          class: 'text-center td-actions',
          sortable: false,
        }
      ]
    },
    blockTasks() {
      const statusKey = this.action_plan?.status?.key ?? ''
      return statusKey == 'EXPIRED_AP' || statusKey == 'CLOSED_AP' 
    },
    statusParent() {
      return this.action_plan?.status?.key ?? ''
    },
    idStatusExpiredTask() {
      return 14
    },
    originName() {
      return `Tareas plan de acción de ${ allOrigins(this.origin) }`
    },
    originNameFilter() {
      return `Tareas: ${ allOrigins(this.origin) }`
    },
  },
  watch: {
    'paginate.page': function() {
      this.getTasks()
    }
  },
  methods: {
    getTextTypeTask,
    backProcess() {
      window.location.href = `/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/${this.origin}/${this.idSectionRegister}/action/${this.idActionRegister}/plan/view`
    },
    async getTasks() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        const { data } = await getTasks(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.setInfo(data.info)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setInfo({ status, audit_process, customer_name, corporate_name, scope, full_requirement, no_requirement, matter, aspect, last_expired }) {
      const { original_date_format, extension_date_format, extension_date, cause } = last_expired
      this.headers.status.status = status.status
      this.headers.status.color = status.color
      this.headers.audit_process = audit_process
      this.headers.customer_name = customer_name
      this.headers.corporate_name = corporate_name
      this.headers.scope = scope
      this.headers.no_requirement = no_requirement
      this.headers.full_requirement = full_requirement
      this.headers.matter = matter
      this.headers.aspect = aspect
      this.headers.last_expired.original_date_format = original_date_format
      this.headers.last_expired.extension_date_format = extension_date_format
      this.headers.last_expired.extension_date = extension_date
      this.headers.last_expired.cause = cause
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    getImageByUser({ auditors }) {
      const find = auditors.find(item => item.pivot.level == 1)
      return find?.image?.full_path ?? ''
    },
    getNameByUser({ auditors }) {
      const find = auditors.find(item => item.pivot.level == 1)
      return find?.person?.full_name ?? ''
    },
    async alertRemove({ id_task, title }) {
      try {
        const question = `¿Estás seguro de eliminar tarea: '${title}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteTask(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, id_task)
          this.responseMixin(data)
          await this.getTasks()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>