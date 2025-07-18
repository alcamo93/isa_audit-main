<template>
  <fragment>
    <loading :show="loadingMixin" />
    <div class="d-flex" v-if="!with_data">
      <b-card class="flex-fill text-center">
        <h5>No se cuenta con datos históricos para mostrar aún Auditoría</h5>
      </b-card>
    </div>
    <filter-area v-else title="histórico - auditorías" :opened="true" :custom-title="true">
      <div class="d-flex flex-wrap flex-sm-wrap flex-md-nowrap">
        <b-card class="flex-fill text-center m-1 w-25">
          <h5 class="global-dashboard-title">
            cumplimiento global - registro anual
          </h5>
          <last-three-years :customer-id="idCustomer" :chart-id="`last-three-years-customer-id-au-${idCustomer}`"
            :series="historical_years.series" :colors="historical_years.colors" :text="'Requerimientos'">
          </last-three-years>
        </b-card>
        <b-card class="flex-fill text-center m-1">
          <h5 class="global-dashboard-title">
            cumplimiento por planta - registro anual
          </h5>
          <industrial-plant-grouped :customer-id="idCustomer"
            :chart-id="`industrial-plant-grouped-last-three-years-customer-id-au-${idCustomer}`"
            :series="historicals_projects.series" :categories="historicals_projects.categories"
            :colors="historicals_projects.colors" :text="'Requerimientos'">
          </industrial-plant-grouped>
        </b-card>
      </div>
    </filter-area>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import IndustrialPlantGrouped from '../graphics/bar-chart/IndustrialPlantGrouped'
import LastThreeYears from '../graphics/bar-chart/LastThreeYears'
import { getCustomerGlobalAuditHistorical } from '../../../../services/dashboardService'

export default {
  components: {
    FilterArea,
    IndustrialPlantGrouped,
    LastThreeYears,
  },
  props: {
    idCustomer: {
      type: Number,
      required: true,
    }
  },
  mounted() {
    this.getCustomerGlobalAuditHistorical()
  },
  data() {
    return {
      with_data: false,
      historical_years: {
        series: [],
        colors: [],
      },
      historicals_projects: {
        series: [],
        categories: [],
        colors: [],
      }
    }
  },
  methods: {
    async getCustomerGlobalAuditHistorical() {
      try {
        this.showLoadingMixin()
        const { data } = await getCustomerGlobalAuditHistorical(this.idCustomer)
        const { with_data } = data.data
        this.with_data = with_data
        if (!with_data) {
          this.showLoadingMixin()
          return
        }
        const { global_projects, last_years } = data.data
        this.setYears(last_years)
        this.setProjects(global_projects)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setYears(last_years) {
      this.historical_years.colors = last_years.map(item => item.color)
      this.historical_years.series = last_years.map((year, index, arr) => {
        return {
          name: year.year.toString(),
          data: arr.filter(item => item.year == year.year).map(item =>
            ({ x: '3 últimos años', y: item.total, fillColor: item.color })
          )
        }
      })
    },
    setProjects(global_projects) {
      this.historicals_projects.categories = global_projects.map(item => item.corp_tradename)
      const allColors = global_projects.map( item => item.years.map(year => year.color) ).flat()
      this.historicals_projects.colors = [... new Set(allColors)]
      
      const allYears = global_projects.map( item => item.years.map(year => ({year: year.year, color: year.color})) ).flat().reduce((accomulate, current) => {
        const exists = accomulate.find(item => item.year === current.year);
        if (!exists) accomulate.push(current);
        return accomulate;
      }, [])
      
      this.historicals_projects.series = allYears.map(year => {
        return {
          name: year.year,
          data: global_projects.map(process => {
            const yearCurrent = process.years.find(itemYear => itemYear.year == year.year)
            return {
              x: process.corp_tradename,
              y: yearCurrent.total,
              fillColor: yearCurrent.color,
            }
          })
        }
      })
    }
  }
}
</script>

<style scoped>
.global-dashboard-title {
  text-transform: capitalize !important;
  color: #113C53;
  font-family: Verdana;
  font-weight: 600;
  font-size: 18px;
}
</style>