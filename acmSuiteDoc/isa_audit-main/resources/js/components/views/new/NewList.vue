<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" :opened="true">
          <filters @filterSelected="setFilters" />
        </filter-area>
      </div>
      <b-card>
        <b-row>
          <b-col>
            <register-modal @successfully="getNews" :is-new="true" />
          </b-col>
        </b-row>
        <div class="row d-flex justify-content-center">
          <template>
            <b-button-group size="sm">
              <b-button v-for="button in specialsFilters" :key="button.id" :variant="button.color"
                @click="currentStatus(button.id)">
                {{ button.name }}
              </b-button>
            </b-button-group>
          </template>
        </div>
        <b-row>
          <b-col>
            <b-table responsive striped hover show-empty empty-text="No hay registros que mostrar" :fields="headerTable"
              :items="items">
              <template #cell(date_in_range)="data">
                <b-badge pill :variant="getVariant(data.item.date_in_range)">
                  {{ data.item.date_in_range }}
                </b-badge>
              </template>
              <template #cell(published)="data">
                <b-form-checkbox v-model="data.item.published" v-b-tooltip.hover.left
                  :title="labelStatus(data.item.published)" :value="1" :unchecked-value="0" switch size="lg"
                  @change="changeCurrent(data.item.published, data.item.id)" />
              </template>
              <template #cell(actions)="data">
                <!-- button update -->
                <register-modal @successfully="getNews" :is-new="false" :register="data.item" />
                <!-- button delete -->
                <b-button v-if="canDeleteNew(data.item)" v-b-tooltip.hover.left title="Eliminar noticia"
                  variant="danger" class="btn-link" @click="alertRemove(data.item)">
                  <b-icon icon="x-lg" aria-hidden="true"></b-icon>
                </b-button>
              </template>
            </b-table>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <app-paginator :data-list="paginate" @pagination-data="changePaginate" />
          </b-col>
        </b-row>
      </b-card>
    </b-container>
  </fragment>
</template>

<script>

import FilterArea from '../components/slots/FilterArea'
import AppPaginator from '../components/app-paginator/AppPaginator'
import Filters from './Filters'
import RegisterModal from './Modal'
import BannerNews from './BannerNews'
import { getNews, deleteNew, updateStatusNew } from '../../../services/newService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Noticias`
    this.getNews()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    BannerNews,
  },
  data() {
    return {
      items: [],
      filters: {
        custom_filter: null,
        headline: null,
        description: null
      },
      specialsFilters: [
        { id: 'PUBLISHED', name: 'Publicadas', color: 'btn btn-success' },
        { id: 'CURRENT', name: 'Fecha vigente', color: 'btn btn-primary' },
        { id: 'HISTORY', name: 'En historial', color: 'btn btn-warning' },
        { id: 'OUT_RANGE', name: 'Fuera de fecha', color: 'btn btn-secondary' },
        { id: 'ALL', name: 'Todas', color: 'btn btn-light' },
      ],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
    }
  },
  watch: {
    'paginate.page': function () {
      this.getCustomers()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'headline',
          label: 'Titular',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'publication_start_date_format',
          label: 'Fecha de inicio',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'publication_closing_date_format',
          label: 'Fecha de cierre',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'date_in_range',
          label: 'Estatus',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'published',
          label: 'Publicada',
          class: 'text-center',
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
    async getNews() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getNews(params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    canDeleteNew({ owner }) {
      return !Boolean(owner)
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ custom_filter, headline, description }) {
      this.filters.custom_filter = custom_filter
      this.filters.headline = headline
      this.filters.description = description
      await this.getNews()
    },
    async alertRemove({ id, headline }) {
      try {
        const question = `¿Estás seguro de eliminar la noticia: '${headline}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteNew(id)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getNews()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    labelStatus(published) {
      return `${(published ? 'Retener' : 'Publicar')} Noticia`
    },
    async changeCurrent(published, idNew) {
      try {
        this.showLoadingMixin()
        const { data } = await updateStatusNew(published, idNew)
        this.responseMixin(data)
        await this.getNews()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    currentStatus(id) {
      this.filters.custom_filter = id
      this.getNews()
    },
    getVariant(status) {
      if (status === 'Fecha vigente') return 'primary';
      if (status === 'En historial') return 'warning';
      return 'secondary';
    }
  }
}
</script>