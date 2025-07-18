<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="success"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="list-check" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <filters class="mb-2"
          :question="question"
          :init="dialog"
          @fieldSelected="setFilters"
        />
        <b-row>
          <b-col>
            <register-modal 
              @successfully="getQuestionAnswers"
              :id-form="idForm"
              :id-question="idQuestion"
              :is-new="true"
            />
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
              <template #cell(order)="data">
                {{ data.item.order }}
              </template>
              <template #cell(description)="data">
                {{ data.item.description }}
              </template>
              <template #cell(value)="data">
                <b-badge pill :variant="data.item.value.color"
                >
                  {{ data.item.value.answer_value }}
                </b-badge>
              </template>
              <template #cell(actions)="data">
                <!-- button requirements -->
                <modal-relation
                  :id-form="idForm"
                  :question="question"
                  :answer="data.item"
                />
                <!-- button update -->
                <register-modal 
                  @successfully="getQuestionAnswers"
                  :id-form="idForm"
                  :id-question="idQuestion"
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
import AppPaginator from '../../../../components/app-paginator/AppPaginator.vue'
import Filters from './Filters.vue'
import RegisterModal from './Modal.vue'
import ModalRelation from './relationRequirements/ModalRelation.vue'
import { deleteQuestionAnswer, getQuestionAnswers } from '../../../../../../services/questionAnswerService'

export default {
  components: {
    AppPaginator,
    RegisterModal,
    ModalRelation,
    Filters
  },
  props: {
    idForm: {
      type: Number,
      required: true
    },
    question: {
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
        id_answer_value: null,
        description: null,
      },
      items: []
    }
  },
  computed: {
    idQuestion() {
      return this.question.id_question
    },
    titleModal() {
      if (this.question == null) return ''
      return `Respuestas`
    },
    titleTooltip() {
      if (this.question == null) return ''
      return `Mostrar Respuestas`
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
          key: 'description',
          label: 'Respuesta',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'value',
          label: 'Valor',
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
  watch: {
    'paginate.page': function() {
      this.getQuestionAnswers()
    },
  },
  methods: {
    async showModal() {
      await this.getQuestionAnswers()
      this.dialog = true
    },
    async getQuestionAnswers() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getQuestionAnswers(this.idForm, this.idQuestion, params, this.filters)
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
    async alertRemove({ id_answer_question, description }) {
      try {
        const question = `¿Estás seguro de eliminar: "${description}"?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteQuestionAnswer(this.idForm, this.idQuestion, id_answer_question)
          this.responseMixin(data)
          await this.getQuestionAnswers()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setFilters({ id_answer_value, description }) {
      this.filters.id_answer_value = id_answer_value
      this.filters.description = description
      await this.getQuestionAnswers()
    },
  }
}
</script>

<style>

</style>