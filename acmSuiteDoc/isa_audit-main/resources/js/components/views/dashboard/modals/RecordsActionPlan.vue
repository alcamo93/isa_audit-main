<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <b-row>
          <b-col>
            <button-go-to-section
              :encrypt="true"
              :url="`/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${sectionNameUrl}/${idSectionRegister}/action/${idActionRegister}/plan/view`"
              title-button="Plan de acción"
              :right="true"
            />
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <!-- Table -->
            <b-table 
              responsive 
              striped 
              hover 
              show-empty
              empty-text="No hay registros que mostrar"
              :fields="headerTable" 
              :items="items"
            >
              <template #cell(no_requirement)="data">
                <span> {{ getNoRequirementText(data.item) }} </span>
              </template>
              <template #cell(requirement)="data">
                <span> {{ getRequirementText(data.item) }} </span>
              </template>
              <template #cell(status)="data">
                <b-badge 
                  v-if="data.item.status != null"
                  pill 
                  :variant="data.item.status.color"
                >
                  {{ data.item.status.status }}
                </b-badge>
              </template>
              <template #cell(date)="data">
                <span> {{ data.item.close_date_format }} </span>
              </template>
              <template #cell(risk)="data">
                <div class="d-flex align-items-center flex-column">
                  <b-badge v-for="category in data.item.risk_totals" :key="category.id_risk_category"        
                    class="mb-1" variant="info"
                  >
                    {{ category.category.risk_category }}: {{ category.interpretation }}
                  </b-badge>
                </div>
              </template>
            </b-table>
            <!-- End table -->
            <!-- Paginator -->
            <app-paginator
              :data-list="paginate"
              @pagination-data="changePaginate"
            />
            <!-- End Paginator -->
          </b-col>
        </b-row>
      </b-container>
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
import AppPaginator from '../../components/app-paginator/AppPaginator'
import ButtonGoToSection  from '../../components/action_buttons/ButtonGoToSection'
import { getRecordsDashboardActionPlan } from '../../../../services/dashboardService'
import { getNoRequirementText, getRequirementText } from '../../components/scripts/texts'

export default {
  components: {
    AppPaginator,
    ButtonGoToSection,
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
    sectionName: {
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
    filters: {
      type: Object,
      required: true
    },
    titleType: {
      type: String,
      required: false,
      default: 'action'
    }
  },
  data() {
    return {
      dialog: false,
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
    }
  },
  computed: {
    titleModal() {
      if (this.titleType == 'action') {
        const { status_name, matter_name } = this.filters
        return `Requerimientos - ${matter_name} - ${status_name}`  
      }
      if (this.titleType == 'findings') {
        const { condition_name, aspect_name } = this.filters
        return `Hallazgos por condición ${condition_name} del aspecto ${aspect_name}`  
      }
      
    },
    sectionNameUrl() {
      return this.sectionName == 'compliance' ? 'audit' : this.sectionName
    },
    headerTable() {
      return [
        {
          key: 'no_requirement',
          label: 'Número de Requerimiento',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'requirement',
          label: 'Requerimiento Legal',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estatus',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'date',
          label: 'Fecha de vencimiento',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'risk',
          label: 'Nivel de Riesgo',
          class: 'text-center',
          sortable: false,
        },
      ]
    },
  },
  watch: {
    'paginate.page': function() {
      this.getRecords()
    }
  },
  methods: {
    getNoRequirementText,
    getRequirementText,
    async showModal() {
      await this.getRecords()
      this.dialog = true
    },
    async getRecords() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        const { data } = await getRecordsDashboardActionPlan(this.idAuditProcess, this.idAplicabilityRegister, this.sectionNameUrl, this.idSectionRegister, this.idActionRegister, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
  }
}
</script>

<style>

</style>