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
                @successfully="getRequirements"
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
            <template #cell(actions)="data">
              <!-- button relations -->
              <modal-recomendation 
                :name-parent="data.item.no_requirement"
                :id-requirement="data.item.id_requirement"
              />
              <!-- button relation -->
              <modal-relation 
                :record="data.item"
              />
              <!-- button update -->
              <register-modal 
                @successfully="getRequirements"
                :is-new="false"
                :register="data.item"
              />
              <!-- button delete -->
              <b-button 
                v-b-tooltip.hover.left 
                title="Eliminar Requerimiento de Condicionantes, actas u otros"
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
import FilterArea from '../components/slots/FilterArea.vue'
import Filters from './Filters.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import RegisterModal from './Modal.vue'
import ModalRecomendation from './ModalRecomendation.vue'
import ModalRelation from './relationLegal/ModalRelation.vue'
import { getSpecificRequirements, deleteSpecificRequirement } from '../../../services/specifcRequirementService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Condicionantes, actas u otros`
    this.getRequirements()
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
        id_customer: null,
        id_corporate: null,
        id_matter: null,
        id_aspect: null,
        no_requirement: null,
        requirement: null,
        id_application_type: null,
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
        const { data } = await getSpecificRequirements(params, this.filters)
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
          const { data } = await deleteSpecificRequirement(id_requirement)
          this.responseMixin(data)
          await this.getRequirements()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setFilters({ id_customer, id_corporate, id_matter, id_aspect, no_requirement, requirement, id_application_type }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
      this.filters.id_matter = id_matter
      this.filters.id_aspect = id_aspect
      this.filters.no_requirement = no_requirement
      this.filters.requirement = requirement
      this.filters.id_application_type = id_application_type
      await this.getRequirements()
    },
  }
}
</script>

<style>

</style>