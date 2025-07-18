<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area
          class="flex-fill"
          title="Filtros" 
          :opened="true"
        >          
          <label>Planta</label>
          <b-form-input
            v-model="filters.corporate_name"
            placeholder="Búsqueda por nombre"
            debounce="500"
          ></b-form-input>
        </filter-area>
      </div>
      <div class="d-flex flex-wrap justify-content-sm-center justify-content-md-around justify-content-lg-start justify-content-xl-start" 
        v-if="items.length"
      >
        <div class="flex-fill flex-sm-fill flex-md-grow-0 flex-lg-grow-0 m-0 flex-xl-grow-0 m-1"
          v-for="item in items"
          :key="item.id_corporate"
          v-b-tooltip.hover.left
          :title="item.corporate.corp_tradename_format"     
        >
          <b-card class="text-center"
            v-b-tooltip.hover.left
            :title="item.corporate.corp_tradename_format"          
          >
            <template #header>
              <image-item
                :item="item.corporate"
                type="corporate"
              />
            </template>
            <template #footer>
              <b-button
                variant="primary"
                size="sm" 
                @click="goToCorporate(item)"
              >
                Mostrar información
                <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon>
              </b-button>
            </template>
          </b-card>
        </div>
      </div>
      <div class="d-flex" v-else>
        <b-card class="flex-fill text-center">
          <h5>No se cuenta con registros</h5>
        </b-card>
      </div>
    </b-container>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import ImageItem from '../../components/customers/ImageItem'
import { getCorporates } from '../../../../services/dashboardService'

export default {
  components: {
    FilterArea,
    ImageItem,
  },
  mounted() {
    this.getCorporates()
  },
  data() {
    return {
      loading: false,
      items: [],
      filters: {
        corporate_name: null
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getCorporates()
    },
    filters: {
      handler() {
        this.getCorporates()
      },
      deep: true
    }
  },
  methods: {
    async getCorporates() {
      try {
        this.showLoadingMixin()
        const { data } = await getCorporates({}, this.filters)
        this.items = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    goToCorporate({id_audit_processes, aplicability_register}) {
      const idApllicabilityRegister = aplicability_register.id_aplicability_register
      const host = window.location.origin
      const url = `${host}/v2/dashboard/project/${id_audit_processes}/applicability/${idApllicabilityRegister}/all/view`
      window.open(url, '_blank')
    }
  },
}
</script>

<style scoped>

.text-footer {
  font-size: 12px;
  font-weight: 600;
}
.dashboard-card {
  box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px;
  width: 100%;
  height: 280px;
}
.card-title{
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 12px;
}
.custom-text {
  font-size: 12px;
}
.p-card {
  margin: 0;
}
.dashboard-card:hover {
  -webkit-transform: scale(1.05);
  transform: scale(1.05);
  filter: saturate(150%);
  transition-duration: 250ms;
  box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
}
.custom-color {
  color: #4E84F3;
}
.redirect-text{
  font-size: 14px;
}
</style>