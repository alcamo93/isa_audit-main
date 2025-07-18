<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover.left
      :title="titleTooltip"
      class="btn-link go-to-process"
      @click="showModal"
    >
      {{ titleLinkModal  }}
    </b-button>
    <b-modal
      v-model="dialog"
      size="md"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <b-row>
          <b-col class="d-block text-center">
            <h4>{{ titleModal }}</h4>
          </b-col>
        </b-row>
        <b-row>
          <b-col class="d-block text-center">
            <b-form-group v-slot="{ ariaDescribedby }">
              <b-form-radio-group
                v-model="form.id_priority"
                :aria-describedby="ariaDescribedby"
              >
                <b-form-radio v-for="priority in priorities" 
                  @change="setPriority"
                  :key="priority.id_priority" 
                  :value="priority.id_priority"
                >
                  {{ priority.priority }}
                </b-form-radio>
              </b-form-radio-group>
            </b-form-group>
          </b-col>
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
import { updateActionPriority } from '../../../services/actionPlanService'
import { getPriorities } from '../../../services/catalogService'
import { getNoRequirementText } from '../components/scripts/texts'

export default {
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    idAplicabilityRegister: {
      type: Number,
      required: true,
    },
    origin: {
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
    idActionPlan: {
      type: Number,
      required: true,
    },
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      priorities: [],
      form: {
        id_priority: null
      }
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      const name = getNoRequirementText(this.record)
      return `Selecciona la Prioridad para el requerimiento No: ${name}`
    },
    titleLinkModal() {
      if (this.record == null) return ''
      const { priority } = this.record
      return priority.priority
    },
    titleTooltip() {
      if (this.record == null) return ''
      const name = getNoRequirementText(this.record)
      return `Cambiar prioridad para requerimiento No: ${name}`
    },
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()
      await this.getPriorities()
      this.form.id_priority = this.record.id_priority
      this.showLoadingMixin()
      this.dialog = true
    },
    async getPriorities() {
      try {
        const { data } = await getPriorities()
        this.priorities = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async setPriority() {
      try {
        this.showLoadingMixin()
        const { data } = await updateActionPriority(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.form)
        this.dialog = false
        this.showLoadingMixin()
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style>

</style>