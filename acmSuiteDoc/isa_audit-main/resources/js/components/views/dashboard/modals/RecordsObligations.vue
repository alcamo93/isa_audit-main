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
            <button-report-obligation
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :id-obligation-register="idObligationRegister"
              title-button="Reporte"
            />
          </b-col>
          <b-col>
            <button-go-to-section
              :url="`/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/view`"
              title-button="Permisos Críticos"
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
                <p class="text-justify"> {{ getRequirementText(data.item) }} </p>
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
                <span> {{ data.item.end_date_format }} </span>
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
            class="btn btn-danger float-right mr-2"
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
import ButtonReportObligation  from '../../obligation/ButtonReport'
import ButtonGoToSection  from '../../components/action_buttons/ButtonGoToSection'
import { getRecordsDashboardObligation } from '../../../../services/dashboardService'
import { getNoRequirementText, getRequirementText } from '../../components/scripts/texts'

export default {
  components: {
    AppPaginator,
    ButtonReportObligation,
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
    idObligationRegister: {
      type: Number,
      required: true,
    },
    filters: {
      type: Object,
      required: true
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
      return `Permisos Críticos Aspecto - ${this.filters.aspect_name}`
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
        const { data } = await getRecordsDashboardObligation(this.idAuditProcess, this.idAplicabilityRegister, this.idObligationRegister, params, this.filters)
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