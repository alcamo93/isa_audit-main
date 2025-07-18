<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <filters
          @fieldSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <b-row>
            <b-col>
              <register-modal 
                @successfully="getGuidelines"
                :is-new="true"
              />
            </b-col>
          </b-row>
        </b-card-text>
        <b-card-text>
          <b-table 
            responsive 
            striped 
            hover 
            show-empty
            empty-text="No hay registros que mostrar"
            :fields="headerTable" 
            :items="items"
          >
            <template #cell(guideline)="data">
              {{ data.item.guideline }}
            </template>
            <template #cell(initials_guideline)="data">
              {{ data.item.initials_guideline }}
            </template>
            <template #cell(id_legal_classification)="data">
              {{ data.item.legal_classification.legal_classification }}
            </template>
            <template #cell(id_application_type)="data">
              {{ data.item.application_type.application_type }}
            </template>
            <template #cell(links)="data">
              <b-row>
                <b-col>    
                  <b-button 
                    v-b-tooltip.hover.left
                    title="Ir a Artículos"
                    class="btn btn-link go-to-process"
                    @click="goToArticles(data.item)">
                    <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon>
                    Artículos
                  </b-button>
                </b-col>
              </b-row>
            </template>
            <template #cell(actions)="data">
              <b-row>
                <!--button topics-->
                <modal-relation 
                  :record="data.item"
                />
                <!--button aspects-->
                <modal-aspect 
                  :record="data.item"
                />
              </b-row>
              <b-row>
                <!-- button update -->
                <register-modal 
                  @successfully="getGuidelines"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-b-tooltip.hover.left 
                  title="Eliminar Marco jurídico"
                  variant="danger"
                  class="btn-link"
                  @click="alertRemove(data.item)"
                >
                  <b-icon icon="x-lg" aria-hidden="true"></b-icon>
                </b-button>
              </b-row>
            </template>
          </b-table>
          <!-- Paginator -->
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
          <!-- End Paginator -->
        </b-card-text>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea.vue'
import Filters from './Filters.vue'
import AppPaginator from '../../components/app-paginator/AppPaginator.vue'
import RegisterModal from './Modal.vue'
import ModalRelation from './relationTopic/ModalTopic'
import ModalAspect from './relationAspect/ModalAspect'
import { getGuidelines, deleteGuideline } from '../../../../services/guidelineService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Marco Jurídico`
    this.getGuidelines()
  },
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    RegisterModal,
    ModalRelation,
    ModalAspect
  },
  data() {
    return {
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        guideline: null,
        initials_guideline: null,
        id_legal_classification: null,
        id_application_type: null,
        id_state: null,
        id_city: null
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getGuidelines()
    },
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'guideline',
          label: 'Marco jurídico',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'initials_guideline',
          label: 'Siglas',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'id_legal_classification',
          label: 'Clasificación',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'id_application_type',
          label: 'Competencia',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'links',
          label: 'Secciones',
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
    },
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
    async alertRemove({ id_guideline, guideline }) {
      try {
        const question = `¿Estás seguro de eliminar: "${guideline}"?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteGuideline(id_guideline)
          this.responseMixin(data)
          await this.getGuidelines()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setFilters({guideline, initials_guideline, id_legal_classification, id_application_type, id_state, id_city}) {
      this.filters.guideline = guideline
      this.filters.initials_guideline = initials_guideline
      this.filters.id_legal_classification = id_legal_classification
      this.filters.id_application_type = id_application_type
      this.filters.id_state = id_state
      this.filters.id_city = id_city
      await this.getGuidelines()
    },
    goToArticles({id_guideline}) {
      window.location.href = `/v2/catalogs/guideline/${id_guideline}/legal_basi/view`
    },
  }
}
</script>

<style>

</style>