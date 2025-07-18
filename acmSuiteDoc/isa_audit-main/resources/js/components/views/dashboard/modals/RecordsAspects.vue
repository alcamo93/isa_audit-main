<template>
  <fragment>
    <loading :show="loadingMixin" />
    <records-action-plan
      ref="modalRecordsActionPlan"
      title-type="findings"
      :id-audit-process="idAuditProcess"
      :id-aplicability-register="idAplicabilityRegister"
      :section-name="sectionName"
      :id-section-register="idSectionRegister"
      :id-action-register="idActionRegister"
      :filters="modal"
    />
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <b-row>
          <b-col v-for="aspect in aspects" :key="aspect.id_aspect" 
            cols="12" sm="6" md="6"
          >
            <b-card
              class="clic-handler"
              align="center"
              header-tag="header"
              footer-tag="footer"
              @click="showRecordsModal(aspect.id_aspect)"
            >
              <template #header>
                <h6 class="mb-0 font-weight-bold border-bottom text-break text-capitalize">
                  {{ aspect.aspect }}
                </h6>
              </template>
              <b-row class="d-flex align-items-center">
                <b-col cols="12">
                  <b-img 
                    fluid 
                    :src="aspect.full_path" 
                    :alt="aspect.aspect"
                    img-width="50px"
                  ></b-img>
                </b-col>
                <b-col cols="12">
                  <h2 class="text-break">
                    {{ `${aspect.total}%` }}
                  </h2>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </b-container>
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            class="btn btn-danger float-right mr-2"
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
import RecordsActionPlan  from './RecordsActionPlan'

export default {
  components: {
    RecordsActionPlan,
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
    filters: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
      modal: {
        condition_name: '',
        aspect_name: '',
        id_action_register: null,
        id_condition: null, 
        id_aspect: null,
      }
    }
  },
  computed: {
    titleModal() {
      const { title } = this.filters
      return `Hallazgos de condición: ${title}`
    },
    aspects() {
      return this.filters.aspects
    }
  },
  methods: {
    async showModal() {
      this.dialog = true
    },
    showRecordsModal(idAspect) {
      const { id_condition, condition_name, aspects } = this.filters
      const aspect = aspects.find(item => item.id_aspect == idAspect)
      this.modal.condition_name = condition_name
      this.modal.aspect_name = aspect.aspect
      this.modal.id_condition = id_condition
      this.modal.id_aspect = idAspect
      this.$refs.modalRecordsActionPlan.showModal()
    }
  }
}
</script>

<style scoped>
  .clic-handler:hover {
    cursor: pointer;
  }
</style>