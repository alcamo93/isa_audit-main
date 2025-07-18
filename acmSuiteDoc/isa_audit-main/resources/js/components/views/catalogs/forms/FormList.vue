<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area opened>
        <filters
          @fieldSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <b-row>
            <b-col>
              <register-modal 
                @successfully="getForms"
                is-new
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
            <template #cell(name)="data">
              {{ data.item.name }}
            </template>
            <template #cell(matter)="data">
              {{ data.item.matter.matter }}
            </template>
            <template #cell(aspect)="data">
              {{ data.item.aspect.aspect }}
            </template>
            <template #cell(is_current)="data">
              <b-form-checkbox 
                v-model="data.item.is_current" 
                v-b-tooltip.hover.left
                :title="labelStatus(data.item)"
                :value="1"
                :unchecked-value="0"
                switch size="lg"
                @change="changeCurrent(data.item)"
              />
            </template>
            <template #cell(links)="data">
              <b-row>
                <b-col>
                  <b-button 
                    v-b-tooltip.hover.left
                    title="Ir a Cuestionario de Aplicabilidad"
                    variant="link"
                    class="go-to-process"
                    @click="goToQuestions(data.item)">
                    <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
                    Cuestionario de Aplicabilidad
                  </b-button>
                </b-col>
              </b-row>
              <b-row>
                <b-col>    
                  <b-button 
                    v-b-tooltip.hover.left
                    title="Ir a Requerimientos de Auditoría"
                    variant="link"
                    class="go-to-process"
                    @click="goToRequirements(data.item)">
                    <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
                    Requerimientos de Auditoría
                  </b-button>
                </b-col>
              </b-row>
            </template>
            <template #cell(actions)="data">
              <!-- btn duplicate -->
              <b-button 
                v-b-tooltip.hover.left 
                title="Duplicar Formulario"
                variant="info"
                class="btn-link"
                @click="duplicateForm(data.item)"
              >
                <b-icon icon="files" aria-hidden="true"></b-icon>
              </b-button>
              <!-- button update -->
              <register-modal 
                @successfully="getForms"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left 
                title="Eliminar Formulario"
                variant="danger"
                class="btn-link"
                @click="alertRemove(data.item)"
              >
                <b-icon icon="x-lg" aria-hidden="true"></b-icon>
              </b-button>
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
import { getForms, deleteForm, changeCurrent, duplicateForm } from '../../../../services/FormService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Formularios`
    this.getForms()
  },
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    RegisterModal,
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
        name: '',
        id_matter: null,
        id_aspect: null,
        is_current: null,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getForms()
    },
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'name',
          label: 'Nombre',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'matter',
          label: 'Materia',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'aspect',
          label: 'Aspecto',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'is_current',
          label: 'Vigente',
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
    async getForms() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getForms(params, this.filters)
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
    async changeCurrent({ id, name, is_current }) {
      try {
        const titleDisable = 'Establecer formulario como Principal'
        const questionDisable = `¿Estás seguro de cambiar el formulario: '${name}' como el principal?`

        const titleEnable = 'Desactivar formulario'
        const questionEnable = `¿Estás seguro de desactivar el formulario: '${name}'?`

        const title = is_current ? titleDisable : titleEnable
        const question = is_current ? questionDisable : questionEnable

        const { value } = await this.alertQuestionMixin(title, question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await changeCurrent(id)
          this.showLoadingMixin()
          this.responseMixin(data)
        }
        await this.getForms()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async alertRemove({ id, name }) {
      try {
        const question = `¿Estás seguro de eliminar el formulario: '${name}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteForm(id)
          this.responseMixin(data)
          await this.getForms()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    labelStatus({is_current}) {
      return `${(is_current ? 'Desactivar' : 'Activar')} Formulario`
    },
    async setFilters({ name, id_matter, id_aspect, is_current }) {
      this.filters.name = name
      this.filters.id_matter = id_matter
      this.filters.id_aspect = id_aspect
      this.filters.is_current = is_current
      await this.getForms()
    },
    async duplicateForm({ id, name }) {
      try {
        const title = 'Duplicar Formulario'
        const question = `¿Estás seguro que deseas duplicar el formulario: '${name}'?`
        const { value } = await this.alertQuestionMixin(title, question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await duplicateForm(id)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getForms()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    goToQuestions({id}) {
      window.location.href = `/v2/catalogs/form/${id}/question/view`
    },
    goToRequirements({id}) {
      window.location.href = `/v2/catalogs/form/${id}/requirement/view`
    },
  }
}
</script>

<style>

</style>