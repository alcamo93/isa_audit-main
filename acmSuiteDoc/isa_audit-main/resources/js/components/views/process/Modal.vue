<template>
  <span>
    <loading :show="loadingMixin" />
    <b-button v-if="isNew"
      variant="success"
      class="float-right"
      @click="showModal"
    >
      Agregar 
      <b-icon icon="plus" aria-hidden="true"></b-icon> 
    </b-button>

    <b-button v-else
      v-b-tooltip.hover.left
      title="Editar evaluación"
      variant="warning"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      no-close-on-backdrop
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <form-customers 
              use-in-modal
              ref="formCustomersRef"
              @fieldSelected="getCustomers"
            ></form-customers>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Nombre de evaluación <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:100"
                    name="Nombre de evaluación"
                  >
                    <b-form-input
                      v-model="form.audit_processes"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Opciones de aplicabilidad <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Opciones de aplicabilidad"
                  >
                    <v-select 
                      v-model="form.evaluation_type_id"
                      :options="evaluations"
                      :reduce="e => e.id"
                      label="name"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" :md="!customScope ? 12 : 6">
                <b-form-group>
                  <label>
                    Alcance <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Alcance"
                  >
                    <v-select 
                      v-model="form.id_scope"
                      :options="scopes"
                      :reduce="e => e.id_scope"
                      label="scope"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col v-if="customScope" sm="12" md="6">
                <b-form-group>
                  <label>
                    Especificar departamento <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    :rules="customScope ? 'required' : ''"
                    name="Especificar departamento"
                  >
                    <b-form-input
                      v-model="form.specification_scope"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Periodo de registro
                    <span 
                      v-if="endDateFutureComputed"
                      class="font-weight-bold"
                    > 
                      ({{ formatDateMixin(form.date) }} - {{ formatDateMixin(endDateFutureComputed) }})
                    </span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Especificar periodo"
                  >
                    <vue-date-picker 
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      v-model="form.date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    No. de Auditorías Anuales<span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Especificar No. de Auditorías Anuales"
                  >
                    <b-form-input
                      v-model="form.per_year"
                      type="number"
                      min="1"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <select-users 
              :id-corporate="idCorporate"
              :auditors.sync="form.auditors"
            />
            <b-row>
              <b-col>
                <b-form-group>
                  <label>
                    Aspecto - Materia <span class="text-danger">*</span>
                  </label>
                  <b-button variant="info" @click="selectAll" size="sm">
                    <b-icon icon="list-check" aria-hidden="true"></b-icon>
                    Evaluar todos
                  </b-button>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Materia y Aspecto"
                  >
                    <multiselect 
                      v-model="selected.forms" 
                      :options="forms" 
                      multiple
                      placeholder=""
                      label="aspect_name" 
                      track-by="aspect_id"
                      selectLabel="Clic para seleccionar"
                      selectedLabel="Seleccionado"
                      deselectLabel="Clic para remover"
                    >
                      <!-- folded multiselect display -->
                      <template slot="singleLabel" slot-scope="{ option }">
                        <strong>{{ option.aspect_name }}</strong>
                        - <em>{{ option.matter_name }}</em>
                      </template>
                      <!-- options display (multiselect unfolded) -->
                      <template slot="option" slot-scope="{ option }">
                        <div class="option__desc">
                          <span class="option__title">
                            <strong>{{ option.aspect_name }}</strong>  
                          </span>
                          - <em>{{ option.matter_name }}</em>
                        </div>
                      </template>
                      <template slot="noOptions">
                        No se encontraron registros
                      </template>
                    </multiselect>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <b-form-checkbox
                    :disabled="disabledRiskEvaluation"
                    v-model="form.evaluate_risk"
                  >
                    Evaluar nivel de riesgo (Recomendado)
                  </b-form-checkbox>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <b-form-checkbox
                    v-model="form.evaluate_especific"
                  >
                    Evaluar requerimientos especificos de condicionantes actas u otros
                  </b-form-checkbox>
                </b-form-group>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button v-if="isNew"
            variant="success"
            class="float-right"
            @click="setProcess"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateProcess"
          >
            Actualizar
          </b-button>
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cancelar
          </b-button>
        </div>
      </template>
    </b-modal>
  </span>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, max } from '../../validations'
import FormCustomers from '../components/customers/FormCustomers.vue'
import SelectUsers from './handlers/SelectUsers.vue'
import { getFormsSource, getEvaluationTypes, getScopes } from '../../../services/catalogService'
import { storeProcess, getProcess, updateProcess } from '../../../services/processService'
import { endDateFuture } from '../components/scripts/dates'

export default {
  components: {
    FormCustomers,
    ValidationProvider,
    ValidationObserver,
    SelectUsers,
  },
  props: {
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    register: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      required,
      max,
      dialog: false,
      loading: false,
      loadDataManual: false,
      scopes: [],
      forms: [],
      evaluations: [],
      selected: {
        forms: []
      },
      form: {
        id_customer: null,
        id_corporate: null,
        audit_processes: '',
        evaluation_type_id: null,
        id_scope: null,
        specification_scope: '',
        evaluate_risk: false,
        evaluate_especific: false,
        date: null, 
        per_year: 1,
        auditors: [],
        forms: [],
      }
    }
  },
  watch: {
    'form.id_corporate': function() {
      if ( !this.loadDataManual ) {
        // this.users = []
        // this.selected.leader = null
        // this.selected.auditors = []
        // this.optionsAuditors.alls = []
        // this.optionsAuditors.leaders = null
        // this.optionsAuditors.auditors = []
      }
    },
    'form.evaluation_type_id': function(value) {
      const bothEvaluations = 3
      if (value == bothEvaluations) {
        this.form.evaluate_risk = false
      }
    },
    'selected.forms': function(forms) {
      this.form.forms = forms.map(form => form.id)
    }
  },
  computed: {
    idCorporate() {
      return this.form.id_corporate ?? 0
    },
    customScope() {
      return this.form.id_scope === 2
    },
    titleModal() {
      return this.isNew ? 'Agregar Evaluación ' : `Editar: ${this.register.audit_processes}`
    },
    endDateFutureComputed() {
      if (this.form.date == null ) return null
      return endDateFuture(this.form.date)
    },
    disabledRiskEvaluation() {
      const bothEvaluations = 3
      return this.form.evaluation_type_id != bothEvaluations
    }
  },
  methods: {
    async showModal() {
      this.reset()
      this.showLoadingMixin()
      await this.loadNewRegister()
      if (!this.isNew) await this.loadUpdateRegister()
      this.showLoadingMixin()
      this.dialog = true
    },
    async loadNewRegister() {
      await this.getEvaluationTypes()
      await this.getScopes()
      await this.getForms()
    },
    async loadUpdateRegister() {
      try {
        this.loadDataManual = true
        this.showLoadingMixin()
        // load server data
        const { data } = await getProcess(this.register.id_audit_processes)
        // set data in view
        const { 
          audit_processes, id_scope, specification_scope, evaluate_risk, 
          evaluation_type_id, evaluate_especific, date, per_year } = data.data
        const { auditors, aplicability_register } = data.data
        this.form.audit_processes = audit_processes
        this.form.evaluation_type_id = evaluation_type_id
        this.form.id_scope = id_scope
        this.form.specification_scope = specification_scope
        this.form.evaluate_risk = Boolean(evaluate_risk)
        this.form.evaluate_especific = Boolean(evaluate_especific)
        this.form.date = date
        this.form.per_year = per_year
        setTimeout(async () => { 
          await this.$refs.formCustomersRef.loadData(data.data, true) 
          this.form.auditors = auditors.map(item => ({ id_user: item.id_user, name: item.person.full_name, leader: item.pivot.leader }) )
          const selectedsForms = aplicability_register.contract_matters.map(matter => matter.contract_aspects.map(aspect => aspect.form_id)).flat()
          this.selected.forms = this.forms.filter(form => selectedsForms.some(selected => form.id == selected))
          this.showLoadingMixin()
          this.loadDataManual = false
        }, 1000) 
      } catch (error) {
        console.log(error)
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getEvaluationTypes() {
      try {
        const { data } = await getEvaluationTypes()
        this.evaluations = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getScopes() {
      try {
        const { data } = await getScopes()
        this.scopes = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getForms() {
      try {
        const { data } = await getFormsSource()
        this.forms = data.data.map(({ id, name, aspect_id, matter, aspect }) => {
          return { id, name, aspect_id, matter_name: matter.matter, aspect_name: aspect.aspect }
        })
      } catch (error) {
        this.responseMixin(error)
      }
    },
    getCustomers({ id_customer, id_corporate }) {
      this.form.id_customer = id_customer
      this.form.id_corporate = id_corporate
    },
    async setProcess() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeProcess(this.form)
        this.dialog = false
        this.showLoadingMixin()
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateProcess() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await updateProcess(this.register.id_audit_processes, this.form)
        this.dialog = false
        this.showLoadingMixin()
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    selectAll() {
      this.selected.forms = this.forms
    },
    reset() {
      this.form.id_customer = null
      this.form.id_corporate = null
      this.form.audit_processes = ''
      this.form.evaluation_type_id = null
      this.form.id_scope = null
      this.form.specification_scope = ''
      this.form.evaluate_risk = false
      this.form.evaluate_especific = false
      this.form.date = null
      this.form.per_year = 1
      this.form.auditors = []
      this.form.forms = []
      this.selected.forms = []
    }
  }
}
</script>

<style>
  
</style>