<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-container fluid>
      <div class="d-flex">
        <filter-area class="flex-fill" :opened="true">
          <filters
            @filterSelected="setFilters"
          />
        </filter-area>
      </div>
      <b-card>
        <b-row>
          <b-col>
            <register-modal 
              @successfully="getProfiles"
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
              <template #cell(profile_name)="data">
                <span> {{ data.item.profile_name }} </span>
              </template>
              <template #cell(corp_tradename)="data">
                <span> {{ data.item.corporate.corp_tradename }} </span>
              </template>
              <template #cell(type)="data">
                <span> {{ data.item.type.type }} </span>
              </template>
              <template #cell(status)="data">
                <b-badge v-if="data.item.status"
                  pill 
                  :variant="data.item.status.color"
                >
                  {{ data.item.status.status }}
                </b-badge>
              </template>
              <template #cell(permissions)="data">
                <b-button 
                  variant="info"
                  v-b-tooltip.hover.left
                  title="Permisos para "
                  @click="data"
                >
                  <b-icon icon="unlock"/> 
                  Permisos
                </b-button>
              </template>
              <template #cell(actions)="data">
                <!-- button update -->
                <register-modal 
                  v-if="canModifyProfile(data.item)"
                  @successfully="getProfiles"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-if="canModifyProfile(data.item)"
                  v-b-tooltip.hover.left
                  title="Eliminar Perfil"
                  variant="danger"
                  class="btn-link"
                  @click="alertRemove(data.item)"
                >
                  <b-icon icon="x-lg" aria-hidden="true"></b-icon>
                </b-button>
              </template>
            </b-table>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <app-paginator
              :data-list="paginate"
              @pagination-data="changePaginate"
            />
          </b-col>
        </b-row>
      </b-card>
    </b-container>
  </fragment>
</template>

<script>
import FilterArea from '../components/slots/FilterArea'
import AppPaginator from '../components/app-paginator/AppPaginator'
import Filters  from './Filters'
import RegisterModal from './Modal'
import { getProfiles, deleteProfile } from '../../../services/profileService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Perfiles`
    this.getProfiles()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
  },
  data() {
    return {
      items: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        profile_name: null,
        id_profile_type: null,
        id_status: null,
      },
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
    }
  },
  watch: {
    'paginate.page': function() {
      this.getProfiles()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'profile_name',
          label: 'Perfil',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'corp_tradename',
          label: 'Planta',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estatus',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'type',
          label: 'Tipo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'permissions',
          label: 'Permisos',
          class: 'text-center d-none',
          sortable: false,
        },
        {
          key: 'actions',
          label: 'Acciones',
          class: 'text-center td-actions',
          sortable: false,
        }
      ]
    }
  },
  methods: {
    async getProfiles() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getProfiles(params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    canModifyProfile({ id_profile_type }) {
      return id_profile_type != 1
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ id_customer, id_corporate, profile_name, id_profile_type, id_status }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
      this.filters.profile_name = profile_name
      this.filters.id_profile_type = id_profile_type
      this.filters.id_status = id_status
      await this.getProfiles()
    },    
    async alertRemove({ id_profile, profile_name }) {
      try {
        const question = `¿Estás seguro de eliminar el perfil: '${profile_name}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteProfile(id_profile)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getProfiles()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style></style>
