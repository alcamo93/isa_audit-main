<template>
  <fragment>
    <loading :show="loadingMixin" />
    <records-aspects
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :section-name="sectionName"
      :id-section-register="idSectionRegister"
      :id-action-register="idActionRegister"
      :filters="filters_modal"
      ref="modalRecordsAspects"
    />
    <b-modal
      v-model="dialog"
      size="md"
      title="Hallazgos" 
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <donut
          :series="series"
          :labels="labels"
          :colors="colors"
          @clickedIndex="showAspects"
        />
      </b-container>
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
import Donut from '../graphics/pie-chart/Donut'
import RecordsAspects from './RecordsAspects.vue'

export default {
  components: {
    Donut,
    RecordsAspects
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    idAplicabilityRegister: {
      type: Number,
      required: true,
    },
    sectionName: {
      type: String,
      required: true,
    },
    idSectionRegister: {
      type: Number,
      required: true,
    },
    idActionRegister: {
      type: Number,
      required: true,
    },
    data: {
      type: Object,
      required: true
    },
    aspects: {
      type: Array,
      required: true
    }
  },
  computed: {
    series() {
      const { series } = this.data
      return series
    },
    labels() {
      const { labels } = this.data
      return labels
    },
    colors() {
      const { colors } = this.data
      return colors
    },
  },
  data() {
    return {
      dialog: false,
      filters_modal: {
        title: '',
        condition_name: '',
        id_condition: null,
        condition_key: '',
        aspects: []
      }
    }
  },
  methods: {
    async showModal() {
      this.dialog = true
    },
    showAspects({name}) {
      const critical = 1
      const operative = 2
      const conditionName = name.toLowerCase().trim()
      const condition = conditionName == 'critica' ? critical : operative
      this.filters_modal.title = name
      this.filters_modal.condition_name = name
      this.filters_modal.id_condition = condition
      this.filters_modal.condition_key = conditionName
      this.filters_modal.aspects = this.aspects
      this.$refs.modalRecordsAspects.showModal()
    }
  }
}
</script>

<style>

</style>