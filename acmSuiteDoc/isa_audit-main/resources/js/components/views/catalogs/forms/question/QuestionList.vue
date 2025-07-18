<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <template v-slot:action>
          <b-button
            class="float-right mt-2 mr-2"
            variant="success"
            @click="goToForms"
          >
            Regresar
          </b-button>
        </template>
        <filters
          :id-form="idForm"
          @fieldSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <b-row>
            <b-col>
              <register-modal 
                @successfully="getQuestions"
                :id-form="idForm"
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
            <template #cell(order)="data">
              {{ data.item.order }}
            </template>
            <template #cell(question)="data">
              {{ data.item.question }}
            </template>
            <template #cell(status)="data">
              <b-form-checkbox 
                v-model="data.item.id_status" 
                v-b-tooltip.hover.left
                :title="labelStatus(data.item.id_status)"
                :value="1"
                :unchecked-value="0"
                switch size="lg"
                @change="changeCurrent(data.item)"
              />
            </template>
            <template #cell(actions)="data">
              <!-- button dependencies -->
              <modal-dependency 
                :id-form="idForm"
                :question="data.item"
              />
              <!-- button relations -->
              <modal-answers 
                :id-form="idForm"
                :question="data.item"
              />
              <!-- button relation -->
              <modal-relation 
                :id-form="idForm"
                :record="data.item"
              />
              <!-- button update -->
              <register-modal 
                @successfully="getQuestions"
                :id-form="idForm"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left 
                title="Eliminar Pregunta"
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
import FilterArea from '../../../components/slots/FilterArea'
import Filters from './Filters'
import AppPaginator from '../../../components/app-paginator/AppPaginator'
import RegisterModal from './Modal'
import ModalAnswers from './answers/ModalAnswers'
import ModalRelation from './relationLegal/ModalRelation'
import ModalDependency from './dependencies/ModalDependency'
import { getQuestions, deleteQuestion, updateStatusQuestion } from '../../../../../services/questionsService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Preguntas de auditoría`
    this.getQuestions()
  },
  props: {
    idForm: {
      type: Number,
      required: true
    }
  },
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    RegisterModal,
    ModalAnswers,
    ModalRelation,
    ModalDependency
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
        question: null,
        id_question_type: null,
        id_state: null,
        id_city: null,
        id_status: null,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getQuestions()
    },
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'order',
          label: 'Orden',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'question',
          label: 'Pregunta',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estado',
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
    async getQuestions() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getQuestions(this.idForm, params, this.filters)
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
    async alertRemove({ id_question, question }) {
      try {
        const questionString = `¿Estás seguro de eliminar: "${question}"?`
        const { value } = await this.alertDeleteMixin(questionString)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteQuestion(this.idForm, id_question)
          this.responseMixin(data)
          await this.getQuestions()
          this.showLoadingMixin()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setFilters({ question, id_question_type, id_status, id_state, id_city }) {
      this.filters.question = question
      this.filters.id_question_type = id_question_type
      this.filters.id_state = id_state
      this.filters.id_city = id_city
      this.filters.id_status = id_status
      await this.getQuestions()
    },
    labelStatus(status) {
      return `${(status ? 'Desactivar' : 'Activar')} Pregunta`
    },
    async changeCurrent({id_question}) {
      try {
        this.showLoadingMixin()
        const { data } = await updateStatusQuestion(this.idForm, id_question)
        this.responseMixin(data)
        await this.getQuestions()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    goToForms() {
      window.location.href = `/v2/catalogs/forms/view`
    },
  }
}
</script>

<style>

</style>