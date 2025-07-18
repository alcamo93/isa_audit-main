<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-b-tooltip.hover.leftbottom title="Mostrar evaluación" variant="info" class="btn-link"
      @click="showModal">
      <b-icon icon="eye-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal v-model="dialog" size="xl" :title="titleModal" :no-close-on-backdrop="true" hide-footer>
      <b-container fluid>
        <b-card>
          <b-row cols-sm="2" cols-md="3" cols-lg="3" cols-xl="4">
            <b-col class="my-2">
              <label>
                Cliente
              </label>
              <div class="font-weight-bold">
                {{ customerName }}
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Planta
              </label>
              <div class="font-weight-bold">
                {{ corporateName }}
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Nombre de evaluación
              </label>
              <div class="font-weight-bold">
                {{ processName }}
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Periodo de registro
              </label>
              <div class="font-weight-bold">
                <div class="py-1 my-1 rounded text-white text-center" :class="`bg-${dateColor}`">
                  {{ dateFormat }}
                </div>
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Evaluación
              </label>
              <div class="font-weight-bold">
                {{ evaluationType }}
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Alcance
              </label>
              <div class="font-weight-bold">
                {{ scopeName }}
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Evaluar Nivel de riesgo
              </label>
              <div class="font-weight-bold">
                <div class="py-1 my-1 rounded text-white text-center" :class="`bg-${defineColor(evaluateRisk)}`">
                  {{ evaluateRisk }}
                </div>
              </div>
            </b-col>
            <b-col class="my-2">
              <label>
                Evaluar Requerimientos Especificos
              </label>
              <div class="font-weight-bold">
                <div class="py-1 my-1 rounded text-white text-center" :class="`bg-${defineColor(evaluateSpecific)}`">
                  {{ evaluateSpecific }}
                </div>
              </div>
            </b-col>
          </b-row>
        </b-card>
        <b-card>
          <div class="font-weight-bold m-1">
            Auditores
          </div>
          <div class="d-flex flex-wrap">
            <div class="px-2 py-2 mx-1 my-1 rounded text-white" :class="`bg-${auditor.color}`"
              v-for="auditor in auditors" :key="auditor.id">
              <span class="font-weight-bold">
                {{ auditor.name }}
              </span>
              <span class="font-italic"> ({{ auditor.type }}) </span>
            </div>
          </div>
        </b-card>
        <b-card v-for="matter in matters" :key="matter.id_matter">
          <div class="font-weight-bold m-1">
            Apectos evaluados de: {{ matter.matter }}
          </div>
          <div class="d-flex flex-wrap">
            <div class="px-2 py-2 mx-1 my-1 rounded text-white font-weight-bold" v-for="aspect in matter.aspects"
              :key="aspect.id_aspect" :style="{ 'background-color': matter.color }">
              {{ aspect.aspect }}
            </div>
          </div>
        </b-card>
      </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import OnlyViewCustomers from '../components/customers/OnlyViewCustomers.vue'
import { getProcess } from '../../../services/processService'

export default {
  components: {
    OnlyViewCustomers
  },
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      dialog: false,
      loading: false,
      record: null,
      auditors: [],
      matters: [],
    }
  },
  computed: {
    titleModal() {
      return `Detalles de evaluación: ${this.record?.audit_processes ?? ''}`
    },
    customerName() {
      return this.record?.customer.cust_tradename ?? '----'
    },
    corporateName() {
      return this.record?.corporate.corp_tradename ?? '----'
    },
    processName() {
      return this.record?.audit_processes ?? '----'
    },
    evaluationType() {
      return this.record?.evaluation_type.name ?? '----'
    },
    scopeName() {
      const { scope, specification_scope } = this.record ?? {}
      return scope?.id_scope === 2 ? `${scope.scope}: ${specification_scope}` : scope?.scope ?? '----'
    },
    evaluateRisk() {
      if (!this.record) return '----'
      return Boolean(this.record.evaluate_risk) ? 'Si' : 'No'
    },
    dateFormat() {
      return this.record?.dates_format ?? '--/--/--'
    },
    dateColor() {
      if (!this.record) return 'secondary'
      return this.record.is_in_current_year ? 'success' : 'danger'
    },
    evaluateSpecific() {
      if (!this.record) return '----'
      return Boolean(this.record.evaluate_especific) ? 'Si' : 'No'
    },
  },
  methods: {
    async showModal() {
      try {
        this.showLoadingMixin()
        const { data } = await getProcess(this.id)
        this.record = data.data
        const { auditors, aplicability_register } = data.data
        this.auditors = auditors.map(item => this.getAuditors(item))
        this.matters = aplicability_register.contract_matters.map(itemMatter => {
          return {
            id_matter: itemMatter.matter.id_matter,
            matter: itemMatter.matter.matter,
            color: itemMatter.matter.color,
            aspects: itemMatter.contract_aspects.map(itemAspect => {
              return { id_aspect: itemAspect.aspect.id_aspect, aspect: itemAspect.aspect.aspect }
            })
          }
        })
        this.showLoadingMixin()
        this.dialog = true
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getAuditors(item) {
      const { person, pivot } = item
      const type = Boolean(pivot.leader) ? 'Auditor Lider' : 'Auditor'
      const color = Boolean(pivot.leader) ? 'success' : 'primary'
      return { id: item.id_user, name: person.full_name, type, color }
    },
    defineColor(word) {
      if (word != 'Si' && word != 'No') return 'secondary'
      return word == 'Si' ? 'success' : 'danger'
    }
  }
}
</script>