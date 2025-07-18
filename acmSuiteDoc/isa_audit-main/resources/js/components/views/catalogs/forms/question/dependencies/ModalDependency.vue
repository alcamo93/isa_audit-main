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
      <b-icon icon="lock-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <filters class="mb-2"
          :id-form="idForm"
          :question="question"
          :init="dialog"
          @fieldSelected="setFilters"
          @successfully="getAnswerQuestionDependency"
        />
        <hr>
        <span class="font-weight-bold">Instrucciones:</span> Selecciona las preguntas que se bloquearan según la respuesta seleccionada por el usuario en Aplicabilidad
        <span id="popover-help" class="click-pointer" >
          <b-icon icon="info-circle" variant="primary"></b-icon>
        </span>
        <b-popover target="popover-help" triggers="hover" placement="top">
          <template #title>Nomenclatura de colores</template>
          Cada respuesta muestra el listado de preguntas que significan lo sigueinte:
          <br>
          <b-badge variant="secondary">Pregunta desbloqueada</b-badge><br>
          <b-badge variant="success">Pregunta bloqueada</b-badge><br>
          <b-badge variant="danger">Pregunta desbloqueada (Estatus Inactivo)</b-badge><br>
          <b-badge variant="warning">Pregunta bloqueada  (Estatus Inactivo)</b-badge><br>
        </b-popover>
        <hr>
        <b-row class="mb-1">
          <b-col>
            <b-card v-for="item in items" :key="item.id_answer_question" no-body class="mb-1">
              <b-card-header header-tag="header" role="tab">
                <b-row v-b-toggle="`panel-${item.id_answer_question}`">
                  <b-col class="d-flex flex-inline">
                    <span class="mr-1">
                      <h5> <span class="font-weight-bold">{{ item.description }}</span> </h5>
                    </span>
                    <span class="mr-1">
                      <b-badge pill :variant="item.value.color">
                        {{ item.value.answer_value }}
                      </b-badge>
                    </span>
                  </b-col>
                </b-row>
              </b-card-header>
              <b-collapse :id="`panel-${item.id_answer_question}`" 
                visible :role="`tab-panel-${item.id_answer_question}`"
                :accordion="`accordion-panel-${item.id_answer_question}`"
              >
                <b-card-body>
                  <b-list-group>
                    <b-list-group-item v-for="question in item.dependency" :key="question.id_question"
                      href="#" :variant="question.evaluates.color"
                      @click="setAnswerQuestionDependency(item.id_answer_question, question.id_question)"
                      v-b-tooltip.hover.top
                      :title="`${question.evaluates.block ? 'Desbloquear' : 'Bloquear'} pregunta`"
                    >
                      <span>{{ question.order }} - {{ question.question }}</span>
                    </b-list-group-item>
                  </b-list-group>
                </b-card-body>
              </b-collapse>
            </b-card>
          </b-col>
        </b-row>
        <!-- Paginator -->
        <app-paginator
          :data-list="paginate"
          @pagination-data="changePaginate"
        />
        <!-- End Paginator -->
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
import AppPaginator from '../../../../components/app-paginator/AppPaginator'
import Filters from './Filters'
import { getAnswerQuestionDependency, setAnswerQuestionDependency } from '../../../../../../services/dependencyService'

export default {
  components: {
    AppPaginator,
    Filters
  },
  props: {
    idForm: {
      type: Number,
      required: true,
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
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        id_answer_value: null,
        description: null,
      }
    }
  },
  computed: {
    titleModal() {
      if (this.question == null) return ''
      return `Bloqueo de Preguntas`
    },
    titleTooltip() {
      if (this.question == null) return ''
      return `Ver Bloqueo de Preguntas`
    },
    idQuestion() {
      return this.question.id_question
    }
  },
  methods: {
    async showModal() {
      await this.getAnswerQuestionDependency()
      this.dialog = true
    },
    async getAnswerQuestionDependency() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getAnswerQuestionDependency(this.idForm, this.idQuestion, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setAnswerQuestionDependency(idAnswerQuestion, idQuestionBlock) {
      try {
        this.showLoadingMixin()
        const { data } = await setAnswerQuestionDependency(this.idForm, this.idQuestion, idAnswerQuestion, idQuestionBlock)
        await this.getAnswerQuestionDependency()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setFilters({ id_answer_value, description }) {
      this.filters.id_answer_value = id_answer_value
      this.filters.description = description
      await this.getAnswerQuestionDependency()
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
  }
}
</script>

<style>

</style>