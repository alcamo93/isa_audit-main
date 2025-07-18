<template>
  <fragment>
    <loading :show="loadingMixin" />
    <div class="d-flex" v-if="!with_data">
      <b-card class="flex-fill text-center">
        <h5>No se cuenta con datos para mostrar aún Permisos Críticos</h5>
      </b-card>
    </div>
    <filter-area v-else :title="`Permisos Críticos ${year} - ${customer.corp_tradename}`" :opened="true" :custom-title="true">
      <records-obligations ref="modalRecordsObligation" 
        :id-audit-process="idAuditProcess"
        :id-aplicability-register="idAplicabilityRegister"
        :id-obligation-register="obligationRegisterId"
        :filters="modal" 
      />
      <template v-slot:action>
        <corporate-obligation-report-button class="mr-2"
          :id-audit-process="idAuditProcess"
          :id-aplicability-register="idAplicabilityRegister"
          :obligation-register-id="obligationRegisterId"
          title-button="Descargar reporte"
        />
      </template>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <!-- card-image -->
        <b-card class="flex-fill text-center m-1 w-100"
          header-tag="header"
          footer-tag="footer"
          :no-body="true"
        >
          <template #header>
            <h5 
              class="global-dashboard-title text-break"
              v-b-tooltip.hover.left
              :title="customer.cust_tradename"
            >
              {{ customer.corp_tradename }}
            </h5>
          </template>
          <b-card-body>
            <image-item
              :item="{image: customer.full_path}"
              type="generic"
              :size="15"
            />
          </b-card-body>
          <template #footer>
            <h6 
              class="text-break"
              v-b-tooltip.hover.left
              :title="customer.address"
            >
              {{ customer.address }}
            </h6>
          </template>
        </b-card>
        <!-- card-image -->
        <b-card class="flex-fill text-center m-1 w-100" v-if="matters[0] != undefined">
          <h5 class="global-dashboard-title text-break">
            {{ `Aspectos ${matters[0].matter}` }}
          </h5>
          <aspects-current-year :id-matter="matters[0].id_matter" :series="matters[0].serie" @clickedIndex="showRecords" />
          <!-- percentages -->
          <div class="d-flex">
            <b-card class="flex-fill text-center m-1 clic-handler"
              v-b-tooltip.hover="matters[0].matter"
            >
            <h6 class="global-dashboard-title text-break">
              {{ `${matters[0].total}%` }}
            </h6>
            </b-card>
          </div>
          <!-- percentages -->
        </b-card>
        <b-card class="flex-fill text-center m-1 w-100" v-if="matters[1] != undefined">
          <h5 class="global-dashboard-title text-break">
            {{ `Aspectos ${matters[1].matter}` }}
          </h5>
          <aspects-current-year :id-matter="matters[1].id_matter" :series="matters[1].serie" @clickedIndex="showRecords" />
          <!-- percentages -->
          <div class="d-flex">
            <b-card class="flex-fill text-center m-1 clic-handler"
              v-b-tooltip.hover="matters[1].matter"
            >
            <h6 class="global-dashboard-title text-break">
              {{ `${matters[1].total}%` }}
            </h6>
            </b-card>
          </div>
          <!-- percentages -->
        </b-card>
      </div>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <b-card class="flex-fill text-center m-1 w-100">
          <h5 class="global-dashboard-title">
            permisos críticos - {{ year }}
          </h5>
          <current-year :series="compliance.series" :labels="compliance.labels" :colors="compliance.colors">
          </current-year>
        </b-card>
        <b-card class="flex-fill text-center m-1 w-100" v-if="matters[2] != undefined">
          <h5 class="global-dashboard-title text-break">
            {{ `Aspectos ${matters[2].matter}` }}
          </h5>
          <aspects-current-year :id-matter="matters[2].id_matter" :series="matters[2].serie" @clickedIndex="showRecords" />
          <!-- percentages -->
          <div class="d-flex">
            <b-card class="flex-fill text-center m-1 clic-handler"
              v-b-tooltip.hover="matters[2].matter"
            >
            <h6 class="global-dashboard-title text-break">
              {{ `${matters[2].total}%` }}
            </h6>
            </b-card>
          </div>
          <!-- percentages -->
        </b-card>
        <b-card class="flex-fill text-center m-1 w-100" v-if="matters[3] != undefined">
          <h5 class="global-dashboard-title text-break">
            {{ `Aspectos ${matters[3].matter}` }}
          </h5>
          <aspects-current-year :id-matter="matters[3].id_matter" :series="matters[3].serie" @clickedIndex="showRecords" />
          <!-- percentages -->
          <div class="d-flex">
            <b-card class="flex-fill text-center m-1 clic-handler"
              v-b-tooltip.hover="matters[3].matter"
            >
            <h6 class="global-dashboard-title text-break">
              {{ `${matters[3].total}%` }}
            </h6>
            </b-card>
          </div>
          <!-- percentages -->
        </b-card>
      </div>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <b-card class="flex-fill text-center m-1">
          <h5 class="global-dashboard-title text-break">
            histórico permisos críticos
          </h5>
          <last-three-years :customer-id="idAuditProcess" :chart-id="`last-three-years-customer-id-${idAuditProcess}`"
            :series="historical_years.series" :colors="historical_years.colors" :text="'Requerimientos'" />
        </b-card>
        <b-card class="flex-fill text-center m-1">
          <h5 class="global-dashboard-title text-break">
            permisos críticos - cumplimiento mensual {{ year }}
          </h5>
          <months-current-year :series="historicalMonth.data" :colors="historicalMonth.colors" />
        </b-card>
      </div>
    </filter-area>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import CurrentYear from '../graphics/pie-chart/CurrentYear'
import AspectsCurrentYear from '../graphics/bar-chart/AspectsCurrentYear'
import LastThreeYears from '../graphics/bar-chart/LastThreeYears'
import MonthsCurrentYear from '../graphics/area-chart/MonthsCurrentYear'
import RecordsObligations from '../modals/RecordsObligations'
import CorporateObligationReportButton from '../reports/CorporateObligationReportButton'
import ImageItem from '../../components/customers/ImageItem'
import { getCorporateObligation } from '../../../../services/dashboardService'

export default {
  components: {
    FilterArea,
    CurrentYear,
    AspectsCurrentYear,
    LastThreeYears,
    MonthsCurrentYear,
    RecordsObligations,
    CorporateObligationReportButton,
    ImageItem,
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
    obligationRegisterId: {
      type: Number,
      required: true,
    },
  },
  mounted() {
    this.getCorporateObligation()
  },
  data() {
    return {
      idAspect: 3,
      showArea: true,
      year: '',
      customer: {
        address: '',
        corp_tradename: '',
        full_path: '',
        id_audit_processes: '',
        obligation_register_id: null,
      },
      with_data: false,
      matters: [],
      compliance: {
        series: [],
        labels: [],
        colors: [],
      },
      historicalMonth: {
        data: [],
        colors: []
      },
      historical_years: {
        series: [],
        colors: [],
      },
      modal: {
        id_aspect: null,
        aspect_name: '',
      }
    }
  },
  methods: {
    async getCorporateObligation() {
      try {
        this.showLoadingMixin()
        const { data } = await getCorporateObligation(this.idAuditProcess, this.idAplicabilityRegister)
        const { year, with_data, customer, compliance,
          historical_month, historical_last_years } = data.data
        this.year = year
        this.with_data = with_data
        if (!with_data) {
          this.showLoadingMixin()
          return
        }
        this.setCustomer(customer)
        this.setMatters(customer)
        this.setCompliance(compliance)
        this.setYears(historical_last_years)
        this.setMonth(historical_month)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setCustomer({ address, corp_tradename, corp_full_path, id_audit_processes }) {
      this.customer.address = address
      this.customer.corp_tradename = corp_tradename
      this.customer.full_path = corp_full_path
      this.customer.id_audit_processes = id_audit_processes
    },
    setMatters({ matters }) {
      this.matters = matters.map(matter => {
        return {
          total: matter.total,
          id_matter: matter.id_matter,
          matter: matter.matter,
          serie: [{
            name: 'Requerimientos',
            data: matter.aspects.map(aspect => {
              return {
                x: aspect.aspect,
                y: aspect.total,
                fillColor: aspect.color,
                id_aspect: aspect.id_aspect
              }
            })
          }]
        }
      })
    },
    setCompliance(compliance) {
      this.compliance.series = compliance.map(item => item.total)
      this.compliance.labels = compliance.map(item => item.label)
      this.compliance.colors = compliance.map(item => item.color)
    },
    setYears({ years }) {
      this.historical_years.colors = years.map(item => item.color)
      this.historical_years.series = years.map((year, index, arr) => {
        return {
          name: year.year.toString(),
          data: arr.filter(item => item.year == year.year).map(item =>
            ({ x: '3 últimos años', y: item.total, fillColor: item.color })
          )
        }
      })
    },
    setMonth(historical_month) {
      const data = historical_month.map(({ key, total }) => {
        return { x: key, y: total }
      })
      this.historicalMonth.colors = ['#009299']
      this.historicalMonth.data = [{ name: 'CUMPLIMIENTO GLOBAL', data: data }]
    },
    showRecords({ id_aspect, aspect_name }) {
      this.modal.id_aspect = id_aspect
      this.modal.aspect_name = aspect_name
      this.$refs.modalRecordsObligation.showModal()
    }
  }
}
</script>

<style scoped>
  .img-fluid.customer {
    max-width: 95% !important;
    height: auto;
  }
  .text-break {
    width: auto;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .global-dashboard-title {
    text-transform: capitalize !important;
    color: #113C53;
    font-family: Verdana;
    font-weight: 600;
    font-size: 18px;
  }
</style>