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
    series: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      search: {
        index: null,
        point: null,
        aspect_name: '',
        id_matter: null,
        id_aspect: null
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
              if (e.seriesIndex == -1 || e.dataPointIndex == -1) return
              this.search.index = e.seriesIndex
              this.search.point = e.dataPointIndex
              this.execAction()
            },
            xAxisLabelClick: (event, chartContext, config) => {
              this.search.index = 0
              this.search.point = config.labelIndex
              this.execAction()
            },
          }
        },
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
        fill: {
          opacity: 1
        },
        legend: {
          show: false
        },
        labels: {
          trim: true
        },
        yaxis: {
          max: 100,
          forceNiceScale: true,
          decimalsInFloat: 0,
          labels: {
            formatter: (val)  => `${val}%`,
          },
        },
        xaxis: {
          labels: {
            trim: true,
            style: {
              colors: ['#000'],
              fontSize: '12px'
            }
          },
          tooltip: {
            y: {
              formatter: (val) => `${val} %`,
            }
          }
        },
      }
    }
  },
  methods: {
    execAction() {
      const {index, point} = this.search
      this.search.id_matter = this.idMatter
      this.search.aspect_name = this.series[index].data[point].x
      this.search.id_aspect = this.series[index].data[point].id_aspect
      this.$emit('clickedIndex', this.search)
    }
  }
}
</script>