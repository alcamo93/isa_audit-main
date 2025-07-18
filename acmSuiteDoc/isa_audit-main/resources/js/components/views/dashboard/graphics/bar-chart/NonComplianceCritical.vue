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
          plotOptions: {
            bar: {
              barHeight: '70%',
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
              trim: true
            },
            tooltip: {
              enabled: true,
            }
          },
          responsive: [
            {
              breakpoint: 1116,
              options: {
                dataLabels: {
                  enabled: false,
                },
              },
            }
          ]
        }
      }
    }
  }
</script>