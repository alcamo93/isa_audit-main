<template>
  <fragment>
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="ui-checks" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <loading :show="loadingMixin" />
      <b-container fluid>
        <filters
          @filterSelected="setFilters"
          :id-matter="idMatter"
        />
        <b-row>
          <b-col>
            <register-modal 
              @successfully="getAspects"
              :is-new="true"
              :id-matter="idMatter"
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
              <template #cell(industry)="data">
                <span> {{ data.item.industry }} </span>
              </template>
              <template #cell(actions)="data">
                <!-- button update -->
                <register-modal 
                  @successfully="getAspects"
                  :is-new="false"
                  :register="data.item"
                  :id-matter="idMatter"
                />
                <!-- button delete -->
                <b-button 
                  v-b-tooltip.hover.left
                  title="Eliminar aspecto"
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
      </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import AppPaginator from '../../../components/app-paginator/AppPaginator'
import Filters  from './Filters'
import RegisterModal from './Modal'
import { getAspects, deleteAspect } from '../../../../../services/matterService'

export default {
  components: {
    AppPaginator,
    Filters,
    RegisterModal,
  },
  props: {
    record: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
      items: [],
      filters: {
        aspect: null,
        id_matter: null
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
      this.getAspects()
    }
  },
  computed: {
    idMatter() {
      return this.record.id_matter
    },
    matterName() {
      return this.record.matter
    },  
    titleModal() {
      return `Aspectos de materia ${this.matterName}`
    },
    titleTooltip() {
      return `Mostrar Aspectos de materia ${this.matterName}`
    },
    headerTable() {
      return [
        {
          key: 'order',
          label: 'Orden',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'aspect',
          label: 'Aspecto',
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
    async showModal() {
      await this.getAspects()
      this.dialog = true
    },
    async getAspects() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getAspects(this.idMatter, params, this.filters)
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
    async setFilters({ aspect }) {
      this.filters.aspect = aspect
      await this.getAspects()
    },    
    async alertRemove({ id_aspect, aspect }) {
      try {
        const question = `¿Estás seguro de eliminar el aspecto: '${aspect}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteAspect(this.idMatter, id_aspect)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getAspects()
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
