<template>
  <apexchart
    type="area" 
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
    colors: {
      type: Array,
      required: true,
    },
  },
  computed: {
    chartOptions() {
      return {
        chart: {
          toolbar: {
            show: false
          }
        },
        colors: this.colors,
        dataLabels: {
          enabled: false
        },
        fill: {
          opacity: 1,
          type: 'solid'
        },
        stroke: {
          curve: 'straight'
        },
        xaxis: {
          type: 'category',
          labels: {
            trim: true
          },
          tooltip: {
            enabled: true,
          }
        },
        yaxis: {
          labels: {
            formatter: (val)  => `${val}%`,
          },
          max: 100,
          forceNiceScale: true,
          decimalsInFloat: 0,
        }
      }
    }
  }
}
</script>