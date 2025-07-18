<template>
  <apexchart
    type="bar" 
    height="250"
    :options="chartOptions" 
    :series="series"
  />
</template>
  
<script>
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    apexchart: VueApexCharts
  },
  props: {
    idMatter: {
      type: Number,
      required: true
    },
    matter: {
      type: String,
      required: true
    },
    series: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      search: {
        index: null,
        status_name: '',
        id_matter: null,
        matter_name: '',
        id_status: null
      }
    }
  },
  computed: {
    chartOptions() {
      return {
        chart: {
          type: 'bar',
          toolbar: {
            show: false
          },
          events: {
            click: (chart, w, e) => {
              this.search.index = e.dataPointIndex
              this.execAction()
            }
          }
        },
        plotOptions: {
          bar: {
            barHeight: '70%',
            horizontal: true,
            distributed: true,
          }
        },
        dataLabels: {
          enabled: true,
          style: {
            fontSize: '10px',
            colors: ['#fff']
          }
        },
        fill: {
          opacity: 1,
        },
        legend: {
          show: false
        },
        yaxis: {
          forceNiceScale: true,
          decimalsInFloat: 0,
        },
        xaxis: {
          labels: {
            show: false,
            trim: true,
          },
          tooltip: {
            enabled: true,
          }
        },
      }
    }
  },
  methods: {
    execAction() {
      const index = this.search.index
      this.search.id_matter = this.idMatter
      this.search.matter_name = this.matter
      this.search.status_name = this.series[0].data[index].x
      this.search.id_status = this.series[0].data[index].id_status
      this.$emit('clickedIndex', this.search)
    }
  }
}
</script>