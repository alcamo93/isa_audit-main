<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <template v-slot:action>
          <b-button
            class="float-right mt-2 mr-2"
            variant="success"
            @click="goToRequirements"
          >
            Regresar
          </b-button>
        </template>
        <filters
          :id-form="idForm"
          :id-requirement="idRequirement"
          @fieldSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <b-row>
            <b-col>
              <register-modal 
                @successfully="getSubrequirements"
                :id-form="idForm"
                :id-requirement="idRequirement"
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
            <template #cell(no_subrequirement)="data">
              {{ data.item.no_subrequirement }}
            </template>
            <template #cell(subrequirement)="data">
              {{ data.item.subrequirement }}
            </template>
            <template #cell(actions)="data">
              <!-- button relations -->
              <modal-recomendation 
                :name-parent="data.item.no_subrequirement"
                :id-form="idForm"
                :id-requirement="idRequirement"
                :id-subrequirement="data.item.id_subrequirement"
              />
              <!-- button relation -->
              <modal-relation 
                :id-form="idForm"
                :id-requirement="idRequirement"
                :record="data.item"
              />
              <!-- button update -->
              <register-modal 
                @successfully="getSubrequirements"
                :id-form="idForm"
                :id-requirement="idRequirement"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left 
                title="Eliminar Subrequerimiento"
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
import FilterArea from '../../../../components/slots/FilterArea.vue'
import Filters from './Filters.vue'
import AppPaginator from '../../../../components/app-paginator/AppPaginator.vue'
import RegisterModal from './Modal.vue'
import ModalRecomendation from './ModalRecomendation.vue'
import ModalRelation from './relationLegal/ModalRelation.vue'
import { getSubrequirements, deleteSubrequirement } from '../../../../../../services/subrequirementsService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Subrequerimientos de auditoría`
    this.getSubrequirements()
  },
  props: {
    idForm: {
      type: Number,
      required: true
    },
    idRequirement: {
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
        no_subrequirement: null,
        subrequirement: null,
        id_requirement_type: null,
        id_condition: null,
        id_evidence: null
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getSubrequirements()
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
  methods: {
    async getSubrequirements() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getSubrequirements(this.idForm, this.idRequirement, params, this.filters)
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
    async alertRemove({ id_subrequirement, no_subrequirement }) {
      try {
        const question = `¿Estás seguro de eliminar: "${no_subrequirement}"?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          const { data } = await deleteSubrequirement(this.idForm, this.idRequirement, id_subrequirement)
          this.responseMixin(data)
          await this.getSubrequirements()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setFilters({ no_subrequirement, subrequirement, id_requirement_type, id_evidence, id_condition }) {
      this.filters.no_subrequirement = no_subrequirement
      this.filters.subrequirement = subrequirement
      this.filters.id_requirement_type = id_requirement_type
      this.filters.id_condition = id_condition
      this.filters.id_evidence = id_evidence
      await this.getSubrequirements()
    },
    goToRequirements() {
      window.location.href = `/v2/catalogs/form/${this.idForm}/requirement/view`
    },
  }
}
</script>

<style>

</style>