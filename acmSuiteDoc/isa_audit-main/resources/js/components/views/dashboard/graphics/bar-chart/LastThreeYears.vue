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
    colors: {
      type: Array,
      required: true,
    },
    text: {
      type: String,
      required: true,
    },
  },
  computed: {
    chartOptions() {
      return {
        chart: {
          type: 'bar',
          toolbar: {
            show: false
          }
        },
        colors: this.colors,
        plotOptions: {
          bar: {
            columnWidth: '45%',
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
          },
          formatter: (val, opt) => `${val}%`,
        },
        legend: {
          position: 'top'
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          labels: {
            trim: true
          },
          tooltip: {
            enabled: true,
          }
        },
        yaxis: {
          title: {
            text: this.text
          },
          max: 100,
          forceNiceScale: true,
          decimalsInFloat: 0,
          labels: {
            formatter: (val)  => `${val}%`,
          },
        },
        fill: {
          opacity: 1,
          colors: this.colors,
        },
        tooltip: {
          y: {
            formatter: (val) => `${val}%`,
          }
        },
        responsive: [
          {
            breakpoint: 1116,
            options: {
              dataLabels: {
                enabled: false,
                offsetY: -20,
                style: {
                  fontSize: '5px',
                  colors: ['#fff']
                },
                formatter: (val, opt) => `${val}%`,
              },
            },
          }
        ]
      }
    }
  }
}
</script>