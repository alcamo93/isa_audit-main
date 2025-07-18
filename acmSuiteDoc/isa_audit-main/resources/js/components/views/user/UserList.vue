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
              @successfully="getUsers"
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
              <template #cell(image)="data">
                <b-avatar :src="getImage(data.item)" variant="secondary" />
              </template>
              <template #cell(full_name)="data">
                <span> {{ data.item.person.full_name }} </span>
              </template>
              <template #cell(email)="data">
                <span> {{ data.item.email }} </span>
              </template>
              <template #cell(phone)="data">
                <span> {{ data.item.person.phone }} </span>
              </template>
              <template #cell(profile)="data">
                <span> {{ data.item.profile.profile_name }} ({{ data.item.profile.type.type }}) </span>
              </template>
              <template #cell(status)="data">
                <b-badge v-if="data.item.status"
                  pill 
                  :variant="data.item.status.color"
                >
                  {{ data.item.status.status }}
                </b-badge>
              </template>
              <template #cell(actions)="data">
                <!-- button password -->
                <modal-password 
                  :register="data.item"
                />
                <!-- button update -->
                <register-modal 
                  @successfully="getUsers"
                  :is-new="false"
                  :register="data.item"
                />
                <!-- button delete -->
                <b-button 
                  v-show="canDeleteUser(data.item)"
                  v-b-tooltip.hover.left
                  title="Eliminar Usuario"
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
import ModalPassword from './ModalPassword'
import { getUsers, deleteUser } from '../../../services/UserService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Usuarios`
    this.getUsers()
  },
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    RegisterModal,
    ModalPassword,
  },
  data() {
    return {
      items: [],
      filters: {
        id_customer: null,
        id_corporate: null,
        name: null,
        email: null,
        id_status: null,
        phone: null,
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
      this.getUsers()
    }
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'image',
          label: 'Foto',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'full_name',
          label: 'Nombre',
          sortable: false,
        },
        {
          key: 'email',
          label: 'Correo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'phone',
          label: 'Teléfono',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'profile',
          label: 'Perfil',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'status',
          label: 'Estatus',
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
    }
  },
  methods: {
    getImage({ image }) {
      return image ? image.full_path : null
    },
    async getUsers() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getUsers(params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    canDeleteUser({ id_user }) {
      return id_user != 1
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ id_customer, id_corporate, name, email, id_status, phone }) {
      this.filters.id_customer = id_customer
      this.filters.id_corporate = id_corporate
      this.filters.name = name
      this.filters.email = email
      this.filters.id_status = id_status
      this.filters.phone = phone
      await this.getUsers()
    },    
    async alertRemove({ id_user, person }) {
      try {
        const question = `¿Estás seguro de eliminar al usuario: '${person.full_name}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteUser(id_user)
          this.showLoadingMixin()
          this.responseMixin(data)
          await this.getUsers()
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
