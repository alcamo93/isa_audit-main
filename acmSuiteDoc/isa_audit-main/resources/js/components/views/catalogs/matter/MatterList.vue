<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" :opened="true">
          <filters
            @filterSelected="setFilters"
          />
        </filter-area>
      </div>
      <b-card>
        <b-row>
            <b-col>
              <register-modal 
                @successfully="getMatters"
                :is-new="true"
              ></register-modal>
            </b-col>
          </b-row>
        <b-row>
          <b-col>
            <b-table 
              responsive 
              striped 
              hover 
              show-empty
              empty-text="No hay registros que mostrar"
              :fields="headerTable" 
              :items="items"
            >
              <!-- <template #cell(image)="data">
                <modal-image 
                  :record="data.item"
                  @successfully="getMatters"
                />
              </template> -->
              <template #cell(order)="data">
                <span> {{ data.item.order }} </span>
              </template>
              <template #cell(matter)="data">
                <span> {{ data.item.matter }} </span>
              </template>
              <template #cell(actions)="data">
                <!-- button aspects -->
                <aspect-modal 
                  :record="data.item"
                 />
                <!-- button update -->
                <register-modal 
                  @successfully="getMatters"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-b-tooltip.hover.left
                  title="Eliminar materia"
                  variant="danger"
                  class="btn-link"
                  @click="alertRemove(data.item)"
                >
                  <b-icon icon="x-lg" aria-hidden="true"></b-icon>
                </b-button>
              </template>
            </b-table>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <app-paginator
              :data-list="paginate"
              @pagination-data="changePaginate"
            />
          </b-col>
        </b-row>
      </b-card>
    </b-container>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import AppPaginator from '../../components/app-paginator/AppPaginator'
import Filters  from './Filters'
import RegisterModal from './Modal'
import ModalImage from './ModalImage'
import AspectModal from './aspect/AspectModal'
import { getMatters, deleteMatter } from '../../../../services/matterService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Materias`
    this.getMatters()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    ModalImage,
    AspectModal,
  },
  data() {
    return {
      items: [],
      filters: {
        matter: null,
      },
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getMatters()
    }
  },
  computed: {
    headerTable() {
      return [
        // {
        //   key: 'image',
        //   label: 'Imagen',
        //   class: 'text-center',
        //   sortable: false,
        // },
        {
          key: 'order',
          label: 'Orden',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'matter',
          label: 'Materia',
          sortable: false,
        },
        {
          key: 'actions',
          label: 'Acciones',
          class: 'text-center td-actions',
          sortable: false,
        }
      ]
    }
  },
  methods: {
    async getMatters() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getMatters(params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ matter }) {
      this.filters.matter = matter
      await this.getMatters()
    },    
    async alertRemove({ id_matter, matter }) {
      try {
        const question = `¿Estás seguro de eliminar la materia: '${matter}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteMatter(id_matter)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getMatters()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style></style>
