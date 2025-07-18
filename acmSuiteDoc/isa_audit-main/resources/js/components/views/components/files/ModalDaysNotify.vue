<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover
      title="Ver días"
      variant="outline-primary"
      @click="showModal"
    >
      <b-icon icon="calendar" aria-hidden="true"></b-icon>
      {{ titleButton }}
    </b-button>

    <b-modal
      v-model="dialog"
      size="is-small"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <div>
          <span class="font-weight-bold">Evidencia: </span>{{ title }}
          <b-table-simple hover small responsive>
            <b-thead head-variant="dark">
              <b-tr>
                <b-th class="text-center">Fecha de notificación</b-th>
                <b-th class="text-center">Estatus</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr v-for="item in dates" :key="item.id">
                <b-td class="text-center">{{ item.date_format }}</b-td>
                <b-td class="text-center">
                  <b-badge :variant="item.done == 1 ? 'success' : 'warning'">
                    {{ item.done_human }}
                  </b-badge>
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </div>
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
import { truncateText } from '../scripts/texts';

export default {
  props: {
    title: {
      type: String,
      required: true
    },
    dates: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    titleModal() {
      return 'Días para notificar a Responsable'
    },
    titleButton() {
      const datesStr = truncateText( this.dates.map(item => item.date_format).join(', ') , 25)
      const title = `${datesStr} (clic para mostrar todas las fechas y estatus)`
      return title
    }
  },
  methods: {
    async showModal() {
      this.dialog = true;
    },
  },
}
</script>

<style>
  
</style>