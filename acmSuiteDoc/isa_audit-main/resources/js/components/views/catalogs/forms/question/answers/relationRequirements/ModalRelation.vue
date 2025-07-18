<template>
  <fragment>
    <b-button v-if="showSelection"
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="arrow-left-right" aria-hidden="true"></b-icon>
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
          :id-form="idForm"
          :question="question"
          :answer="answer"
          :init="dialog"
          @fieldSelected="setFilters"
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
              <template #cell(order)="data">
                {{ data.item.order }}
              </template>
              <template #cell(no_requirement)="data">
                {{ data.item.no_requirement }}
              </template>
              <template #cell(requirement)="data">
                {{ data.item.requirement }}
              </template>
              <template #cell(has_subrequirement)="data">
                <modal-relation 
                  :id-form="idForm"
                  :question="question"
                  :answer="answer"
                  :requirement="data.item"
                  @successfully="getRequirements"
                />
              </template>
              <template #cell(actions)="data">
                <b-button
                  v-b-tooltip.hover.left
                  :title="hasRelation('message', data.item)"
                  :variant="hasRelation('color', data.item)"
                  class="btn-link"
                  @click="setRequirement(data.item)"
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
import AppPaginator from '../../../../../components/app-paginator/AppPaginator'
import Filters from './Filters'
import ModalRelation from './relationSubrequirements/ModalRelation'
import { getRelationRequirements, setRelationRequirement } from '../../../../../../../services/answerRequirementsService'

export default {
  components: {
    AppPaginator,
    ModalRelation,
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
    answer: {
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
        no_requirement: null,
        requirement: null,
        id_requirement_type: null,
        id_evidence: null,
        id_condition: null,
        has_relation: null
      },
      items: []
    }
  },
  computed: {
    titleModal() {
      if (this.answer == null) return ''
      return `Requerimientos de auditoría`
    },
    titleTooltip() {
      if (this.answer == null) return ''
      return `Selección de requerimientos de auditoría`
    },
    showSelection() {
      return this.answer.id_answer_value == 1
    },
    idQuestion() {
      return this.question.id_question
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
          key: 'no_requirement',
          label: 'No. Requerimiento',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'requirement',
          label: 'Requerimiento',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'has_subrequirement',
          label: 'Subrequerimientos',
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
      this.getRequirements()
    },
  },
  methods: {
    async showModal() {
      await this.getRequirements()
      this.dialog = true
    },
    async getRequirements() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRelationRequirements(this.idForm, this.idQuestion, this.answer.id_answer_question, params, this.filters)
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
    async setFilters({ no_requirement, requirement, id_requirement_type, id_evidence, id_condition, has_relation }) {
      this.filters.no_requirement = no_requirement
      this.filters.requirement = requirement
      this.filters.id_requirement_type = id_requirement_type
      this.filters.id_evidence = id_evidence
      this.filters.id_condition = id_condition
      this.filters.has_relation = has_relation
      await this.getRequirements()
    },
    hasRelation(type, {answers_question}) {
      if (type == 'message') {
        return Boolean(answers_question.length) ? 'Remover relación de subrequerimiento con Respuesta' : 'Relacionar subrequerimiento con Respuesta'
      }
      if (type == 'icon') {
        return Boolean(answers_question.length) ? 'patch-check-fill' : 'patch-minus-fill'
      }
      if (type == 'color') {
        return Boolean(answers_question.length) ? 'success' : 'warning'
      }
    },
    async setRequirement({id_requirement}) {
      try {
        this.showLoadingMixin()
        const { data } = await setRelationRequirement(this.idForm, this.idQuestion, this.answer.id_answer_question, id_requirement)
        await this.getRequirements()
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