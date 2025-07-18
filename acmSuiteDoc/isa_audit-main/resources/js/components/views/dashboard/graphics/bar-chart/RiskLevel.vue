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
    series: {
      type: Array,
      required: true,
    },
    xAxisTitle: {
      type: String,
      required: false,
    }
  },
  data() {
    return {
      search: {
        index: null,
        id_risk_category: null,
        interpretation: null,
        risk_category: ''
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
            },
            xAxisLabelClick: (event, chartContext, config) => {
              this.search.index = config.labelIndex
              this.execAction()
            },
          }
        },
        plotOptions: {
          bar: {
            barHeight: '70%',
            distributed: true,
            dataLabels: {
              position: 'top', 
            }
          }
        },
        dataLabels: {
          enabled: true,
          offsetY: -20,
          style: {
            fontSize: '10px',
            colors: ['#aaa']
          }
        },
        fill: {
          opacity: 1
        },
        legend: {
          show: false
        },
        yaxis: {
          forceNiceScale: true,
          decimalsInFloat: 0,
        },
        xaxis: {
          title: {
            text: this.xAxisTitle
          },
          labels: {
            trim: true
          },
          tooltip: {
            enabled: true,
          }
        }
      }
    }
  },
  methods: {
    execAction() {
      const index = this.search.index
      const { id_risk_category, interpretation, risk_category} = this.series[0].data[index]
      this.search.id_risk_category = id_risk_category
      this.search.interpretation = interpretation
      this.search.risk_category = risk_category
      this.$emit('clickedIndex', this.search)
    }
  }
}
</script>