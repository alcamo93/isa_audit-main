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
            <button-report-audit
              :id-audit-process="idAuditProcess"
              :id-aplicability-register="idAplicabilityRegister"
              :id-audit-register="idAuditRegister"
              title-button="Reporte"
            />
          </b-col>
          <b-col>
            <button-go-to-section
              :url="`/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/audit/${this.idAuditRegister}/view`"
              title-button="Auditoría"
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
              <template #cell(finding)="data">
                <p class="text-justify"> {{ data.item.finding }} </p>
              </template>
              <template #cell(legal_basi)="data">
                <legal-modal 
                  :record="data.item"
                />
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
import ButtonReportAudit  from '../../audit/reports/ButtonReport'
import LegalModal from '../../audit/evaluate/modals/LegalModal'
import { getRecordsDashboardAudit } from '../../../../services/dashboardService'
import { getNoRequirementText, getRequirementText } from '../../components/scripts/texts'

export default {
  components: {
    AppPaginator,
    ButtonGoToSection,
    ButtonReportAudit,
    LegalModal
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
    idAuditRegister: {
      type: Number,
      required: true,
    },
    filters: {
      type: Object,
      required: true
    },
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
      const { aspect_name } = this.filters
      return `Hallazgos/Recomendaciones para el aspecto ${aspect_name}`
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
          key: 'finding',
          label: 'Hallazgo/Recomendación',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'legal_basi',
          label: 'Fundamento Legal',
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
        const { data } = await getRecordsDashboardAudit(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister, params, this.filters)
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