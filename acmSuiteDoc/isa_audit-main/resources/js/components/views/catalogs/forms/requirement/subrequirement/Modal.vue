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
      title="Editar Subrequerimiento"
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
              <b-col sm="12" md="6">
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
              <b-col sm="12" md="6">
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
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Tipo de Subrequerimiento
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Tipo de Subrequerimiento"
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
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    No. Subrequerimiento
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="No. Subrequerimiento"
                  >
                    <b-form-input
                      v-model="form.no_subrequirement"
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
                    Subrequerimiento
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Subrequerimiento"
                  >
                    <b-form-textarea
                      max-rows="9"
                      v-model="form.subrequirement"
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
                    Ayuda del subrequerimento 
                  </label>
                  <rich-text-edit 
                    field="help_subrequirement"
                    placeholder="Especifica el formato del texto del Ayuda del subrequerimento..."
                    :initial-content="form.help_subrequirement"
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
            @click="setSubrequirement"
          >
            Registrar
          </b-button>
          <b-button v-else
            class="float-right"
            variant="success"
            @click="updateSubrequirement"
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
import { required, max, double,  } from '../../../../../validations'
import { getSubrequirement, storeSubrequirement, updateSubrequirement } from '../../../../../../services/subrequirementsService'
import { getEvidences, getRequirementTypes, getConditions, getPeriodicities } from '../../../../../../services/catalogService'
import RichTextEdit from '../../../../components/rich_text/RichTextEdit'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    RichTextEdit,
  },
  props: {
    idForm: {
      type: Number,
      required: true,
    },
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    idRequirement: {
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
      periodicities: [],
      form: {
        order: null,
        id_evidence: null,
        id_condition: null,
        document: null,
        id_requirement_type: null,
        no_subrequirement: null,
        subrequirement: null,
        description: null,
        help_subrequirement: '',
        acceptance: '',
        id_periodicity: null
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Subrequerimiento ' : `Editar: ${this.register.no_subrequirement}`
    },
    specifyDocument() {
      return this.form.id_evidence == 4
    },
  },
  watch: {
    'form.id_evidence': function(value) {
      if (this.loadDataActive) return
      this.form.document = null
    },
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadLists()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadLists() {
      await this.getEvidences()
      await this.getConditions()
      await this.getRequirementTypes()
      await this.getPeriodicities()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        this.loadDataActive = true
        const { data } = await getSubrequirement(this.idForm, this.idRequirement, this.register.id_subrequirement)
        const { order, id_evidence, id_condition, document, id_requirement_type, no_subrequirement, 
          subrequirement, description, help_subrequirement, acceptance, id_periodicity } = data.data
        this.form.order = order
        this.form.id_evidence = id_evidence
        this.form.id_condition = id_condition
        this.form.document = document
        this.form.id_requirement_type = id_requirement_type
        this.form.no_subrequirement = no_subrequirement
        this.form.subrequirement = subrequirement
        this.form.description = description
        this.form.help_subrequirement = help_subrequirement ?? ''
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
    async setSubrequirement() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeSubrequirement(this.idForm, this.idRequirement, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateSubrequirement() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateSubrequirement(this.idForm, this.idRequirement, this.register.id_subrequirement, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getRequirementTypes() {
      try {
        this.showLoadingMixin()
        const params = { option: 'sub', idRequirement: this.idRequirement }
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
      if (field == 'help_subrequirement') {
        this.form.help_subrequirement = content 
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
      this.form.no_subrequirement = null
      this.form.subrequirement = null
      this.form.description = null
      this.form.help_subrequirement = ''
      this.form.acceptance = ''
      this.form.id_periodicity = null
    },
  }
}
</script>

<style>

</style>