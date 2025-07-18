<template>
  <fragment>
    <b-button class="btn-link button-help"
      variant="info"
      v-b-tooltip.hover.left
      title="Valoración para Probabilidad" 
      @click="showModal"
    >
      <b-icon icon="question-circle-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <loading :show="loadingMixin" />
      <b-container fluid>
        <b-row class="mt-2">
          <b-table-simple hover small caption-top responsive>
            <b-thead>
              <b-tr class="text-center font-weight-bold text-uppercase">
                <b-th variant="info" colspan="3">
                  Valoración de {{ attributeName }}
                </b-th>
              </b-tr>
              <b-tr>
                <b-th class="text-center font-weight-bold">Nombre</b-th>
                <b-th class="text-center font-weight-bold">Criterio</b-th>
                <b-th class="text-center font-weight-bold">Valor</b-th>
              </b-tr>
            </b-thead>
            <b-tbody v-if="hasHelps">
              <b-tr v-for="attribute in record.helps" :key="attribute.id">
                <b-td class="text-center">
                  {{ attribute.risk_help }}
                </b-td>
                <b-td class="text-justify">
                  {{ attribute.standard }}
                </b-td>
                <b-td class="text-center">
                  {{ attribute.value }}
                </b-td>
              </b-tr>
            </b-tbody>
            <b-tbody v-else>
              <b-tr colspan="3">
                <b-td class="text-center font-weight-bold">
                  No hay registros para mostrar
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
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
export default {
  props: {
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      const name = this.record.risk_attribute
      return `Significado de valores de ${name}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      const name = this.record.risk_attribute
      return `Valoración para ${name}`
    },
    attributeName() {
      if (this.record == null) return ''
      const name = this.record.risk_attribute 
      const initial = name.charAt(0).toUpperCase()
      return `${name} (${initial})`
    },
    hasHelps() {
      return this.record != null && this.record.helps.length > 0
    }
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()    
      this.showLoadingMixin()
      this.dialog = true
    },
  }
}
</script>

<style>
  .button-help {
    margin-bottom: 0%;
    padding-top: 0;
  }
</style>