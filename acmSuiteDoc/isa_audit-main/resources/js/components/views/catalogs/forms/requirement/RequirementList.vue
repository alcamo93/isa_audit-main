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
                @successfully="getRequirements"
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
            <template #cell(no_requirement)="data">
              {{ data.item.no_requirement }}
            </template>
            <template #cell(requirement)="data">
              {{ data.item.requirement }}
            </template>
            <template #cell(has_subrequirement)="data">
              <b-button v-if="Boolean(data.item.has_subrequirement)"
                v-b-tooltip.hover.left
                title="Ir a Subrequirementos"
                class="btn btn-link go-to-process"
                @click="goToSubrequirement(data.item)">
                <b-icon icon="card-list" aria-hidden="true"></b-icon>
                Subrequirementos
              </b-button>
              <b-badge v-else variant="secondary">N/A</b-badge>
            </template>
            <template #cell(actions)="data">
              <!-- button relations -->
              <modal-recomendation 
                :name-parent="data.item.no_requirement"
                :id-form="idForm"
                :id-requirement="data.item.id_requirement"
              />
              <!-- button relation -->
              <modal-relation 
                :id-form="idForm"
                :record="data.item"
              />
              <!-- button update -->
              <register-modal 
                @successfully="getRequirements"
                :id-form="idForm"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left 
                title="Eliminar Requerimiento"
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
import FilterArea from '../../../components/slots/FilterArea.vue'
import Filters from './Filters.vue'
import AppPaginator from '../../../components/app-paginator/AppPaginator.vue'
import RegisterModal from './Modal.vue'
import ModalRecomendation from './ModalRecomendation.vue'
import ModalRelation from './relationLegal/ModalRelation.vue'
import { getRequirements, deleteRequirement } from '../../../../../services/requirementsService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Requerimientos de auditoría`
    this.getRequirements()
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
    ModalRecomendation,
    ModalRelation
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
        no_requirement: null,
        requirement: null,
        id_application_type: null,
        id_requirement_type: null,
        id_state: null,
        id_city: null,
        id_evidence: null,
        id_condition: null,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getRequirements()
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
          label: 'Subrequerimiento',
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
    async getRequirements() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getRequirements(this.idForm, params, this.filters)
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
    async alertRemove({ id_requirement, no_requirement }) {
      try {
        const question = `¿Estás seguro de eliminar: "${no_requirement}"?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteRequirement(this.idForm, id_requirement)
          this.responseMixin(data)
          await this.getRequirements()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setFilters({ no_requirement, requirement, id_application_type, id_requirement_type, id_evidence, id_state, id_city, id_condition }) {
      this.filters.no_requirement = no_requirement
      this.filters.requirement = requirement
      this.filters.id_application_type = id_application_type
      this.filters.id_requirement_type = id_requirement_type
      this.filters.id_state = id_state
      this.filters.id_city = id_city
      this.filters.id_evidence = id_evidence
      this.filters.id_condition = id_condition
      await this.getRequirements()
    },
    goToForms() {
      window.location.href = `/v2/catalogs/forms/view`
    },
    goToSubrequirement({id_requirement}) {
      window.location.href = `/v2/catalogs/form/${this.idForm}/requirement/${id_requirement}/subrequirement/view`
    },
  }
}
</script>

<style>

</style>