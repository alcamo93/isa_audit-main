<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-if="isNew"
      class="float-right"
      variant="success"
      @click="showModal"
    >
      Agregar 
      <b-icon icon="plus" aria-hidden="true"></b-icon>
    </b-button>

    <b-button v-else
      v-b-tooltip.hover.left 
      title="Editar Requerimiento"
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
      :no-close-on-backdrop="true"
      :no-enforce-focus="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesFormProcess">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Orden <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|double"
                    name="Orden"
                  >
                    <b-form-input
                      v-model="form.order"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Evidencia
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Evidencia"
                  >
                    <v-select 
                      v-model="form.id_evidence"
                      :options="evidences"
                      :reduce="e => e.id_evidence"
                      label="evidence"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Tipo de condición
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Tipo de condición"
                  >
                    <v-select 
                      v-model="form.id_condition"
                      :options="conditionTypes"
                      :reduce="e => e.id_condition"
                      label="condition"
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
            <b-row v-if="specifyDocument">
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Documento <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:400"
                    name="Documento"
                    placeholder="Nombre del Documento"
                  >
                    <b-form-input
                      v-model="form.document"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Tipo de Requerimiento
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Tipo de Requerimiento"
                  >
                    <v-select 
                      v-model="form.id_requirement_type"
                      :options="requirementTypes"
                      :reduce="e => e.id_requirement_type"
                      label="requirement_type"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Competencia
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Competencia"
                  >
                    <v-select 
                      v-model="form.id_application_type"
                      :options="applicationTypes"
                      :reduce="e => e.id_application_type"
                      label="application_type"
                      :disabled="canSelectApplicationType"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Periodicidad
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules=""
                    name="Periodicidad"
                  >
                    <v-select 
                      v-model="form.id_periodicity"
                      :options="periodicities"
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
              <b-col sm="12" :md="columnsFormLocation" v-if="this.useStateOptions">
                <b-form-group>
                 <label>
                    Estado <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Estado"
                  >
                    <v-select 
                      v-model="form.id_state"
                      :options="states"
                      :reduce="e => e.id_state"
                      label="state"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" :md="columnsFormLocation" v-if="useCityOptions">
                <b-form-group>
                 <label>
                    Ciudad <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Ciudad"
                  >
                    <v-select 
                      v-model="form.id_city"
                      :options="cities"
                      :reduce="e => e.id_city"
                      label="city"
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
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    No. Requerimiento
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="No. Requerimiento"
                  >
                    <b-form-input
                      v-model="form.no_requirement"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Requerimiento
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Requerimiento"
                  >
                    <b-form-textarea
                      max-rows="9"
                      v-model="form.requirement"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Descripción
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    name="Descripción"
                  >
                    <b-form-textarea
                      max-rows="9"
                      v-model="form.description"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Ayuda del requerimento 
                  </label>
                  <rich-text-edit 
                    field="help_requirement"
                    placeholder="Especifica el formato del texto del Ayuda del requerimento..."
                    :initial-content="form.help_requirement"
                    @input="getContent"
                  />
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Criterio de aceptación 
                  </label>
                  <rich-text-edit 
                    field="acceptance"
                    placeholder="Especifica el formato del texto del Criterio de aceptación..."
                    :initial-content="form.acceptance"
                    @input="getContent"
                  />
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
            class="float-right"
            variant="success"
            @click="setRequirement"
          >
            Registrar
          </b-button>
          <b-button v-else
            class="float-right"
            variant="success"
            @click="updateRequirement"
          >
            Actualizar
          </b-button>
          <b-button 
            class="float-right mr-2"
            variant="danger"
            @click="dialog = false"
          >
            Cancelar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>


import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, max, double,  } from '../../../../validations'
import { getRequirement, storeRequirement, updateRequirement } from '../../../../../services/requirementsService'
import { getStates, getCities, getEvidences, getApplicationTypes, getRequirementTypes, getConditions, getPeriodicities } from '../../../../../services/catalogService'
import RichTextEdit from '../../../components/rich_text/RichTextEdit.vue'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    RichTextEdit,
  },
  props: {
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    idForm: {
      type: Number,
      required: true
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
      double,
      dialog: false,
      loadDataActive: false,
      evidences: [],
      conditionTypes: [],
      requirementTypes: [],
      applicationTypes: [],
      state: [],
      cities: [],
      periodicities: [],
      form: {
        order: null,
        id_evidence: null,
        id_condition: null,
        document: null,
        id_requirement_type: null,
        id_application_type: null,
        id_state: null,
        id_city: null,
        no_requirement: null,
        requirement: null,
        description: null,
        help_requirement: '',
        acceptance: '',
        id_periodicity: null
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Requerimiento ' : `Editar: ${this.register.no_requirement}`
    },
    useStateOptions() {
      return this.form.id_application_type == 2 || this.form.id_application_type == 4
    },
    useCityOptions() {
      return this.form.id_application_type == 4
    },
    columnsFormLocation() {
      const columns = this.useStateOptions & this.useCityOptions ? 2 : 1
      return (12 / columns)
    },
    specifyDocument() {
      return this.form.id_evidence == 4
    },
    canSelectApplicationType() {
      return !(this.form.id_requirement_type == 5 || this.form.id_requirement_type == 17)
    }
  },
  watch: {
    'form.id_evidence': function(value) {
      if (this.loadDataActive) return
      this.form.document = null
    },
    'form.id_requirement_type': function(value) {
      if (this.loadDataActive) return
      const isFederal = value == 1
      const isState = value == 2 || value == 4
      const isLocal = value == 12 || value == 13
      const isCompose = value == 5 || value == 17
      const noSelected = value == null
      if (isFederal) {
        this.form.id_application_type = 1
      }
      if (isState) {
        this.form.id_application_type = 2
      }
      if (isLocal) {
        this.form.id_application_type = 4
      }
      if (isCompose || noSelected) {
        this.form.id_application_type = null
      }
    },
    'form.id_application_type': function(value) {
      if (this.loadDataActive) return
      this.form.id_state = null
      this.form.id_city = null
      this.cities = []
    },
    'form.id_state': function(value) {
      if (!this.loadDataActive) {
        this.id_city = null
        this.cities = []
      }
      // if (this.loadDataActive) return
      // this.id_city = null
      if (value != null & this.useCityOptions) {
        this.getCities()
      } else {
        this.cities = []
      }
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadLists()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadLists() {
      await this.getStates()
      await this.getEvidences()
      await this.getConditions()
      await this.getApplicationTypes()
      await this.getRequirementTypes()
      await this.getPeriodicities()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        this.loadDataActive = true
        const { data } = await getRequirement(this.idForm, this.register.id_requirement)
        const { order, id_evidence, id_condition, document, id_requirement_type, id_application_type, id_state, id_city, 
          no_requirement, requirement, description, help_requirement, acceptance, id_periodicity } = data.data
        this.form.order = order
        this.form.id_evidence = id_evidence
        this.form.id_condition = id_condition
        this.form.document = document
        this.form.id_requirement_type = id_requirement_type
        this.form.id_application_type = id_application_type
        this.form.id_state = id_state
        this.form.id_city = id_city
        this.form.no_requirement = no_requirement
        this.form.requirement = requirement
        this.form.description = description
        this.form.help_requirement = help_requirement ?? ''
        this.form.acceptance = acceptance ?? ''
        this.form.id_periodicity = id_periodicity
        setTimeout(() => {
          this.loadDataActive = false
        }, 500);
        this.showLoadingMixin()
      } catch (error) {
        this.loadDataActive = false
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setRequirement() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeRequirement(this.idForm, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateRequirement() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateRequirement(this.idForm, this.register.id_requirement, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getApplicationTypes() {
      try {
        this.showLoadingMixin()
        const { data } = await getApplicationTypes()
        this.applicationTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getRequirementTypes() {
      try {
        this.showLoadingMixin()
        const params = { option: 'main' }
        const { data } = await getRequirementTypes(params)
        this.requirementTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getConditions() {
      try {
        this.showLoadingMixin()
        const { data } = await getConditions()
        this.conditionTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getEvidences() {
      try {
        this.showLoadingMixin()
        const { data } = await getEvidences()
        this.evidences = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getStates() {
      try {
        this.showLoadingMixin()
        const { data } = await getStates()
        this.states = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getCities() {
      try {
        this.showLoadingMixin()
        const filters = { id_state: this.form.id_state }
        const { data } = await getCities({}, filters)
        this.cities = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getPeriodicities() {
      try {
        this.showLoadingMixin()
        const { data } = await getPeriodicities()
        this.periodicities = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getContent(field, content) {
      if (field == 'help_requirement') {
        this.form.help_requirement = content 
      }
      if (field == 'acceptance') {
        this.form.acceptance = content 
      }
    },
    reset() {
      this.form.order = null
      this.form.id_evidence = null
      this.form.id_condition = null
      this.form.document = null
      this.form.id_requirement_type = null
      this.form.id_application_type = null
      this.form.id_state = null
      this.form.id_city = null
      this.form.no_requirement = null
      this.form.requirement = null
      this.form.description = null
      this.form.help_requirement = ''
      this.form.acceptance = ''
      this.form.id_periodicity = null
    },
  }
}
</script>

<style>

</style>