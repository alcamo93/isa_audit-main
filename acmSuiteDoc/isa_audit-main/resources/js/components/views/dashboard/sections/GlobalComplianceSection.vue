<template>
  <fragment>
    <loading :show="loadingMixin" />
    <div class="d-flex" v-if="!with_data">
      <b-card class="flex-fill text-center">
        <h5>No se cuenta con datos de Cumplimiento Legal EHS</h5>
      </b-card>
    </div>
    <filter-area v-else
      :title="`Cumplimiento global EHS ${year}`"
      :opened="true"
      :custom-title="true"
    >
      <template v-slot:action>
        <global-compliance-report-button class="mr-2"
          :id-customer="idCustomer"
          title-button="Descargar reporte"
        />
      </template>
      <div class="d-flex flex-column flex-md-row flex-wrap flex-sm-wrap flex-md-nowrap">
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
              {{ customer.cust_tradename }}
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
        <!-- Card matters -->
        <b-card class="flex-fill text-center m-1 clic-handler w-100" v-for="matter in matters" :key="matter.id_matter" 
          @click="setUniqueSerie(matter.matter)"
          header-tag="header"
          footer-tag="footer"
          :no-body="true"
        >
          <template #header>
            <h6 class="global-dashboard-title mb-0 font-weight-bold border-bottom text-break text-capitalize">
              {{ matter.matter }}
            </h6>
          </template>
          <b-card-body>
            <image-item class="mt-2"
              :item="{image: matter.full_path}"
              type="generic"
            />
          </b-card-body>
          <template #footer>
            <h2 class="global-dashboard-title text-break">
              {{ `${matter.total}%` }}
            </h2>
          </template>
        </b-card>
        <!-- Card matters -->
      </div>
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <!-- Graph pie -->
        <b-card class="flex-fill text-center m-1 w-25">
          <h5 class="global-dashboard-title">
            Cumplimiento EHS - {{ year }}
          </h5>
          <current-year :series="compliance.series" :labels="compliance.labels" :colors="compliance.colors" />
        </b-card>
        <!-- Graph pie -->
        <!-- Graph bars -->
        <b-card class="flex-fill text-center m-1">
          <h5 class="global-dashboard-title">
            Cumplimiento EHS - plantas {{ year }}
          </h5>
          <industrial-plant-grouped ref="chartGroupPlant" :series="process.series" :colors="process.colors" :text="'Requerimientos'" />
          <!-- percentages -->
          <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
            <b-card class="flex-fill text-center m-1 clic-handler" v-for="item in process.main"
              :style="{'min-width': `${75 / process.main.length}%`}"
              :key="item.id_audit_processes" v-b-tooltip.hover="`${item.corp_tradename} - ${item.audit_processes}`"
              @click="goToCorporate(item)">
              <h5 class="global-dashboard-title text-break">
                {{ item.corp_tradename }}
              </h5>
              <h6 class="global-dashboard-title text-break">
                {{ `${item.total}%` }}
              </h6>
            </b-card>
          </div>
          <!-- percentages -->
        </b-card>
        <!-- Graph bars -->
      </div>
    </filter-area>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import CurrentYear from '../graphics/pie-chart/CurrentYear'
import IndustrialPlantGrouped from '../graphics/bar-chart/IndustrialPlantGrouped'
import GlobalComplianceReportButton from '../reports/GlobalComplianceReportButton'
import ImageItem from '../../components/customers/ImageItem'
import { getCustomerGlobalCompliance } from '../../../../services/dashboardService'

export default {
  components: {
    FilterArea,
    CurrentYear,
    IndustrialPlantGrouped,
    GlobalComplianceReportButton,
    ImageItem,
  },
  props: {
    idCustomer: {
      type: Number,
      required: true,
    }
  },
  mounted() {
    this.getCustomerGlobalCompliance()
  },
  data() {
    return {
      with_data: false,
      year: '',
      customer: {
        address: '',
        cust_tradename: '',
        full_path: '',
        id_audit_processes: '',
      },
      process: {
        main: [],
        colors: [],
        series: [],
      },
      matters: [],
      compliance: {
        series: [],
        labels: [],
        colors: [],
      },
      historicalMonth: {
        data: [],
        colors: []
      }
    }
  },
  computed: {
    mattersColumns() {
      const columns = 12 / this.matters.length
      return columns
    }
  },
  methods: {
    async getCustomerGlobalCompliance() {
      try {
        this.showLoadingMixin()
        const { data } = await getCustomerGlobalCompliance(this.idCustomer)
        const { year, customer, global_matters, global_projects, 
          global_compliance, with_data} = data.data
        this.year = year
        this.with_data = with_data
        if (!with_data) {
          this.showLoadingMixin()
          return 
        }
        this.setCustomer(customer)
        this.matters = global_matters
        this.setAllProcess(global_projects)
        this.setCompliance(global_compliance)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setCustomer({address, cust_tradename, cust_full_path, id_audit_processes}) {
      this.customer.address = address
      this.customer.cust_tradename = cust_tradename
      this.customer.full_path = cust_full_path
      this.customer.id_audit_processes = id_audit_processes
    },
    uniqueMatters(global_projects) {
      const uniqueId = new Set()
      const matters = global_projects.map(item => {
        return item.matters.map(item => ({
            id_matter: item.id_matter,
            matter: item.matter,
            full_path: item.full_path,
            color: item.color,
          })
        )
      }).flat().filter(item => {
        if (!uniqueId.has(item.id_matter)) {
          uniqueId.add(item.id_matter)
          return true
        }
        return false
      })
      return matters
    },
    setAllProcess(global_projects) {
      const global_matters = this.uniqueMatters(global_projects)
      this.process.main = global_projects.sort((a, b) => a.id_audit_register - b.id_audit_register)
      this.process.colors = global_matters.map(item => item.color)
      this.process.series = global_matters.map(item => {
        return {
          id: item.id_audit_register,
          name: item.matter,
          data: global_projects.map(process => {
            const matterCurrent = process.matters.find(matt => matt.id_matter == item.id_matter)
            return {
              x: process.corp_tradename,
              y: matterCurrent == undefined ? 0 : matterCurrent.total,
              fillColor: item.color
            }
          })
        }
      }).sort((a, b) => a.id - b.id)
    },
    setCompliance(global_compliance) {
      this.compliance.series = global_compliance.map(item => item.total)
      this.compliance.labels = global_compliance.map(item => item.label)
      this.compliance.colors = global_compliance.map(item => item.color)
    },
    setUniqueSerie(serieName) {
      this.$refs.chartGroupPlant.showSeries(serieName)
    },
    goToCorporate({id_audit_processes, id_aplicability_register, id_audit_register}) {
      const host = window.location.origin
      const url = `${host}/v2/dashboard/project/${id_audit_processes}/applicability/${id_aplicability_register}/compliance/${id_audit_register}/view`
      window.open(url, '_blank')
    }
  }
}
</script>

<style scoped>
  .img-fluid {
    max-width: 80px !important;
    width: 100% !important;
    height: auto;
  }
  .img-fluid.customer {
    max-width: 95% !important;
    height: auto;
  }
  .clic-handler:hover {
    cursor: pointer;
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