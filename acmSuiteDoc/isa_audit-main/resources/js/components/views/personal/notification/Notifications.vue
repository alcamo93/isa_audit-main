<template>
  <fragment>
    <b-link class="nav-link"
      v-b-tooltip.hover
      :title="titleTooltip"
      @click="showModal"
    >
      <b-icon icon="bell" shift-v="-6" aria-hidden="true"></b-icon>
      <span v-if="showCounter" 
        class="notification" 
      >{{ count }}</span>
    </b-link>

    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <loading :show="loadingMixin" />
      <div v-if="notifications.length == 0" class="text-center border">
        <h5 class="font-weight-bold m-4">No tienes notificaciones</h5>
      </div>
      <b-container v-else fluid>
        <b-list-group>
          <b-list-group-item v-for="notification in notifications" :key="notification.id"
            class="flex-column align-items-start"
          >
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">
                <span v-b-tooltip.hover.right :title="notification.active ? 'Marcar como leida' : ''">
                  <b-icon 
                    @click="markRead(notification)"
                    :variant="notification.active ? 'success': 'secondary'" 
                    :icon="notification.active ? 'circle-fill': 'circle'" 
                    aria-hidden="true">
                  </b-icon>
                </span>
                <b-link v-b-tooltip.hover.right title="Ir a sección" :href="notification.link" target="_blank">
                  {{ notification.title }}
                </b-link>
              </h5>
              <small>{{ notification.date_format }}</small>
            </div>

            <p class="mb-1" v-html="notification.body"></p>
          </b-list-group-item>

        </b-list-group>
        <!-- Paginator -->
        <app-paginator class="mt-2"
          :data-list="paginate"
          @pagination-data="changePaginate"
        />
        <!-- End Paginator -->
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="info"
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
import { getTotalNotifications, getNotifications, updateNotification } from '../../../../services/notificationsService'
import AppPaginator from '../../components/app-paginator/AppPaginator.vue'

export default {
  components: {
    AppPaginator
  },
  mounted() {
    this.getTotalNotifications()
    Echo.private('App.Models.V2.Admin.User.' + this.idUser).notification(() => {
      this.getTotalNotifications()
    })
  },
  props: {
    idUser: {
      type: Number,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      count: 0,
      notifications: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
    }
  },
  computed: {
    titleModal() {
      return 'Notificaciones'
    },
    titleTooltip() {
      return 'Mostrar notificaciones'
    },
    showCounter() {
      return this.count != 0
    }
  },
  methods: {
    async getNotifications() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        const { data } = await getNotifications(params)
        this.notifications = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getTotalNotifications() {
      try {
        const { data } = await getTotalNotifications()
        this.count = data.data.total
        if (this.dialog) {
          await this.getNotifications()
        }
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async markRead({id, active}) {
      if ( !active ) return
      try {
        this.showLoadingMixin()
        const { data } = await updateNotification({id})
        await this.getTotalNotifications()
        this.responseMixin(data)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async showModal() {
      await this.getNotifications()
      this.dialog = true
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