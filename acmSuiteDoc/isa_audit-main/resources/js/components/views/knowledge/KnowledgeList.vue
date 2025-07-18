<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" :opened="true">
          <filters @fieldSelected="setFilters" />
        </filter-area>
      </div>
      <b-card>
        <div class="button-container-wrapper">
            <div class="button-container" ref="container">
              <b-button v-for="(item, index) in topics" :key="index" class="m-1"
                @click="goToGuidelinesByTopic(item.id)"
              >
                {{ item.name }}
              </b-button>
            </div>
        </div>
        <div class="d-flex flex-wrap bd-highlight">
          <div class="p-2 flex-grow-1 col-12 col-md-9 bd-highlight">
            <b-row>
              <b-col>
                <b-table responsive striped hover show-empty empty-text="No hay registros que mostrar"
                  :fields="headerTable" :items="items">
                  <template #cell(guideline)="data">
                    <b-col cols="12" class="d-flex align-items-center">
                      <b-icon class="float-left mt-2 mr-2 text-legal-basis" icon="file-earmark-text-fill" aria-hidden="true"></b-icon>
                      <b-button
                        v-b-toggle="'collapse-' + data.index"
                        variant="link"
                        :class="['col-9 text-left main-guideline', { 'no-link-guideline': !data.item.guidelines.length }]"
                      >
                        {{ data.item.guideline }} ({{ data.item.last_date_format_text }}).
                      </b-button>
                      <div class="col-3 d-flex justify-content-end mt-2">
                        <b-button v-b-tooltip.hover.left title="Ir a Artículos" class="btn btn-link go-to-process"
                          @click="goToArticles(data.item)">
                          <b-icon icon="eye-fill" aria-hidden="true"></b-icon>
                          Ver
                        </b-button>
                      </div>
                    </b-col>
                    <b-collapse v-if="data.item.guidelines.length" :id="'collapse-' + data.index" :visible="isOpen">
                      <b-card>
                        <div v-for="child in data.item.guidelines" :key="child.id_guideline"
                          class="d-flex align-items-center">
                          <div class="col-9 ml-3 text-left">{{ child.guideline }} ({{ child.last_date_format_text }})</div>
                          <div class="col-3 d-flex justify-content-end mt-2">
                            <b-button v-b-tooltip.hover.left title="Ir a Artículos" class="btn btn-link go-to-process"
                              @click="goToArticles(child)">
                              <b-icon icon="eye-fill" aria-hidden="true"></b-icon>
                              Ver
                            </b-button>
                          </div>
                        </div>
                      </b-card>
                    </b-collapse>
                  </template>
                </b-table>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <app-paginator :data-list="paginate" @pagination-data="changePaginate" />
              </b-col>
            </b-row>
          </div>
          <div class="p-2 col-12 col-md-3 bd-highlight">
            <banner-news />
          </div>
        </div>
      </b-card>
    </b-container>
  </fragment>
</template>

<script>

import FilterArea from '../components/slots/FilterArea'
import AppPaginator from '../components/app-paginator/AppPaginator'
import Filters from './Filters'
import BannerNews from './BannerNews'
import { getGuidelines } from '../../../services/knowledgeService'
import { getTopicsSource } from '../../../services/catalogService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Biblioteca jurídica`
    this.getGuidelines()
    this.getTopics()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    BannerNews,
  },
  data() {
    return {
      items: [],
      filters: {
        guideline: null,
        initials_guideline: null,
        id_legal_classification: null,
        id_application_type: null,
        id_state: null,
        id_city: null
      },
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      topics: [],
      isOpen: true
    }
  },
  watch: {
    'paginate.page': function () {
      this.getGuidelines()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'guideline',
          label: 'MARCO JURÍDICO',
          class: 'text-center',
          sortable: false,
        },
      ]
    }
  },
  methods: {
    async getGuidelines() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getGuidelines(params, this.filters)
        this.items = data.data

        this.items.forEach(item => {
          this.$set(item, 'isCollapsed', true)
        })

        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getTopics() {
      try {
        this.showLoadingMixin()
        const params = {}
        const { data } = await getTopicsSource(params, '')
        this.topics = data.data
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
    async setFilters({ guideline, initials_guideline, id_legal_classification, id_application_type, id_state, id_city }) {
      this.filters.guideline = guideline
      this.filters.initials_guideline = initials_guideline
      this.filters.id_legal_classification = id_legal_classification
      this.filters.id_application_type = id_application_type
      this.filters.id_state = id_state
      this.filters.id_city = id_city
      await this.getGuidelines()
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
    goToArticles({ id_guideline }) {
      window.location.href = `/v2/catalogs/guideline/${id_guideline}/article/view`
    },
    goToGuidelinesByTopic(id_topic){
      window.location.href = `/v2/catalogs/knowledge/${id_topic}/guideline_topic/view`
    },
    currentStatus(id) {
      this.filters.custom_filter = id
      this.getNews()
    },
    scrollLeft() {
      this.$refs.container.scrollLeft -= 100
    },
    scrollRight() {
      this.$refs.container.scrollLeft += 100
    },
    toggleCollapse(item) {
      item.isCollapsed = !item.isCollapsed
    }
  }
}

</script>

<style scoped>
.button-container-wrapper {
  margin: 8px;
  display: flex;
  align-items: center;
}

.button-container {
  display: flex;
  overflow-x: auto;
  white-space: nowrap;
  padding-bottom: 10px;
  flex-grow: 1;
}

.b-button {
  margin-right: 10px;
}

.scroll-button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  border-radius: 10%;
}

.scroll-button:hover {
  background-color: #0056b3;
}

.button-container::-webkit-scrollbar {
  height: 6px;
}

.main-guideline{
  color: #010666;
  font-weight: bolder;
}

.no-link-guideline{
  cursor:text !important; 
}

.no-link-guideline:hover{
  color: #010666 !important;
}
</style>