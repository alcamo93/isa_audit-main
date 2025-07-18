<template>
  <div :class="{ 'flashing': isFlashing }">
    <apexchart
      ref="chartGroup"
      type="bar"
      height="250"
      :options="chartOptions" 
      :series="series"
    />
  </div>
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
      required: false,
    },
    text: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      isFlashing: false,
      search: {
        dataPointIndex: null,
        seriesIndex: null,
      }
    }
  },
  computed: {
    chartOptions() {
      return {
        chart: {
          toolbar: {
            show: false
          },
          events: {
            click: (chart, w, e) => {
              this.search.dataPointIndex = e.dataPointIndex
              this.search.seriesIndex = e.seriesIndex
              this.execAction()
            }
          }
        },
        colors: this.colors,
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded',
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
        xaxis: {
          labels: {
            trim: true,
            maxHeight: 120,
            formatter: function(value, timestamp, opts) {
              return value
            }
          },
          tooltip: {
            enabled: true,
          }
        },
        fill: {
          opacity: 1,
        },
        tooltip: {
          y: {
            formatter: (val)  => `${val}%`,
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
                  colors: ['#aaa']
                },
                formatter: (val, opt) => `${val}%`,
              },
            },
          }
        ]
      }
    }
  },
  methods: {
    execAction() {
      const pointIndex = this.search.dataPointIndex
      const firstIndex = this.search.seriesIndex
      const data = {
        serie: this.series[firstIndex],
        point: this.series[firstIndex].data[pointIndex]
      }
      this.$emit('clickedIndex', data)
    },
    showSeries(serieName) {
      const chart = this.$refs.chartGroup
      chart.showSeries(serieName)
      const hides = this.series.filter(item => item.name != serieName)
      hides.forEach(item => chart.hideSeries(item.name));
      this.startFlashing()
    },

    startFlashing() {
      this.isFlashing = true;
      const duration = 2000; 
      const interval = 200; 

      const flashingInterval = setInterval(() => {
        this.isFlashing = !this.isFlashing;
      }, interval);

      setTimeout(() => {
        clearInterval(flashingInterval);
        this.stopFlashing();
      }, duration);
    },
    stopFlashing() {
      this.isFlashing = false;
    },
  }
}
</script>

<style scoped>
.flashing {
  border-radius: 20px;
  background-color: #00929933;
  transition: opacity 0.2s ease-in-out;
}
</style>