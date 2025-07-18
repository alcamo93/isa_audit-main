<template>
  <apexchart       
    type="donut"
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
    labels: {
      type: Array,
      required: true,
    },
    series: {
      type: Array,
      required: true,
    },
    colors: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      search: {
        index: null,
        name: ''
      }
    }
  },
  computed: {
    chartOptions() {
      return {
        chart: {
          width: "100%",
          events: {
            dataPointSelection: (event, chartContext, config) => {
              this.search.index = config.dataPointIndex
              this.execAction()
            }
          }
        },
        labels: this.labels,
        colors: this.colors,
        fill: {
          opacity: 1,
          colors: this.colors
        },
        stroke: {
          width: 1,
          colors: ["#fff"]
        },
        legend: {
          position: "bottom",
          fontSize: '18px',
        }, 
        dataLabels: {
          enabled: true,
          textAnchor: 'middle',
          style: {
            fontSize: '14px',
          },
          formatter: (val, opts)  => `${val}%`,
        },
        plotOptions: {
          pie: {
            dataLabels: {
              offset: -1,
            }
          }
        },
      }    
    }
  },
  methods: {
    execAction() {
      const index = this.search.index
      this.search.name = this.labels[index]
      this.$emit('clickedIndex', this.search)
    }
  }
}
</script>