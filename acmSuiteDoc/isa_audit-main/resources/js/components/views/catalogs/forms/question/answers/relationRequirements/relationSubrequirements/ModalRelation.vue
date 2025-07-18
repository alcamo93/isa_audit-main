<template>
  <fragment>
    <b-button v-if="hasSubrequirement"
      v-b-tooltip.hover.left
      title="Seleccionar Subrequirementos"
      variant="link"
      class="go-to-process"
      @click="showModal"
    >
      <b-icon icon="card-list" aria-hidden="true"></b-icon>
      Subrequirementos
    </b-button>
    <b-badge v-else variant="secondary">N/A</b-badge>

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
          :requirement="requirement"
          :all-selected="allSelected"
          :init="dialog"
          @fieldSelected="setFilters"
          @successfully="renderParents"
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
              <template #cell(no_subrequirement)="data">
                {{ data.item.no_subrequirement }}
              </template>
              <template #cell(subrequirement)="data">
                {{ data.item.subrequirement }}
              </template>
              <template #cell(actions)="data">
                <b-button
                  v-b-tooltip.hover.left
                  :title="hasRelation('message', data.item)"
                  :variant="hasRelation('color', data.item)"
                  class="btn-link"
                  @click="setSubrequirement(data.item)"
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
import AppPaginator from '../../../../../../components/app-paginator/AppPaginator'
import Filters from './Filters'
import { getRelationSubrequirements, setRelationSubrequirement } from '../../../../../../../../services/answerRequirementsService'

export default {
  components: {
    AppPaginator,
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
    answer: {
      type: Object,
      required: true,
      default: null
    },
    requirement: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      allSelected: false,
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        no_subrequirement: null,
        subrequirement: null,
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
      return `Subrequerimientos de auditoría`
    },
    titleTooltip() {
      if (this.answer == null) return ''
      return `Selección de subrequerimientos de auditoría`
    },
    hasSubrequirement() {
      return Boolean(this.requirement.has_subrequirement)
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
          key: 'no_subrequirement',
          label: 'No. Subrequerimiento',
          class: 'text-justify',
          sortable: false,
        },
        {
          key: 'subrequirement',
          label: 'Subrequerimiento',
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
      this.getSubrequirements()
    },
  },
  methods: {
    async showModal() {
      await this.getSubrequirements()
      this.dialog = true
    },
    async getSubrequirements() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRelationSubrequirements(this.idForm, this.idQuestion, this.answer.id_answer_question, this.requirement.id_requirement, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.allSelected = data.all_selected
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
    async setFilters({ no_subrequirement, subrequirement, id_evidence, id_condition, has_relation }) {
      this.filters.no_subrequirement = no_subrequirement
      this.filters.subrequirement = subrequirement
      this.filters.id_evidence = id_evidence
      this.filters.id_condition = id_condition
      this.filters.has_relation = has_relation
      await this.getSubrequirements()
    },
    hasRelation(type, {answers_question}) {
      if (type == 'message') {
        return Boolean(answers_question.length) ? 'Remover relación de requerimiento con Respuesta' : 'Relacionar requerimiento con Respuesta'
      }
      if (type == 'icon') {
        return Boolean(answers_question.length) ? 'patch-check-fill' : 'patch-minus-fill'
      }
      if (type == 'color') {
        return Boolean(answers_question.length) ? 'success' : 'warning'
      }
    },
    async setSubrequirement({id_requirement, id_subrequirement}) {
      try {
        this.showLoadingMixin()
        const { data } = await setRelationSubrequirement(this.idForm, this.idQuestion, this.answer.id_answer_question, id_requirement, id_subrequirement)
        await this.getSubrequirements()
        this.$emit('successfully')
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    renderParents() {
      this.getSubrequirements()
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>