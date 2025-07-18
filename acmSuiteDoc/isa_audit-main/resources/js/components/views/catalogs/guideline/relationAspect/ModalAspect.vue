<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="subtract" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <filters 
          :record="record"
          :init="dialog"
          @filterSelected="setFilters"
        />
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
              <template #cell(actions)="data">
                <b-button
                  v-b-tooltip.hover.left
                  :title="hasRelation('message', data.item)"
                  :variant="hasRelation('color', data.item)"
                  class="btn-link"
                  @click="setAspect(data.item)"
                >
                  <b-icon :icon="hasRelation('icon', data.item)" aria-hidden="true"></b-icon>
                </b-button>
              </template>
            </b-table>
             <!-- Paginator -->
            <app-paginator
              :data-list="paginate"
              @pagination-data="changePaginate"
            />
            <!-- End Paginator -->
          </b-col>
        </b-row>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import AppPaginator from '../../../components/app-paginator/AppPaginator'
import Filters from './Filters'
import { getRelationAspects, setRelationAspects } from '../../../../../services/guidelineService'

export default {
  name: 'ModalAspect',
  components: {
    AppPaginator,
    Filters
  },
  props: {
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        id_matter: null,
        aspect_name: null,
        has_relation: null,
      },
      items: []
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      return `Materia y Aspectos`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Selección de materia y aspectos`
    },
    headerTable() {
      return [
        {
          key: 'matter.matter',
          label: 'Materia',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'aspect',
          label: 'Aspecto',
          class: 'text-justify',
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
  watch: {
    'paginate.page': function() {
      this.getAspects()
    },
  },
  methods: {
    async showModal() {
      //await this.getAspects()
      // this.items = []
      this.dialog = true
    },
    async getAspects() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRelationAspects(this.record.id_guideline, params, this.filters)
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
    async setFilters({ id_matter, aspect_name, has_relation }) {
      this.filters.id_matter = id_matter
      this.filters.aspect_name = aspect_name
      this.filters.has_relation = has_relation
      await this.getAspects()
    },
    hasRelation(type, {guidelines}) {
      if (type == 'message') {
        return Boolean(guidelines.length) ? 'Remover relación de marco jurídico con aspecto' : 'Relacionar marco jurídico con aspecto'
      }
      if (type == 'icon') {
        return Boolean(guidelines.length) ? 'patch-check-fill' : 'patch-minus-fill'
      }
      if (type == 'color') {
        return Boolean(guidelines.length) ? 'success' : 'warning'
      }
    },
    async setAspect({id_aspect}) {
      try {
        this.showLoadingMixin()
        const { data } = await setRelationAspects(id_aspect, this.filters.id_matter, this.record.id_guideline)
        await this.getAspects()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style>

</style>