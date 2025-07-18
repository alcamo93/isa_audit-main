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
      title="Editar Requerimiento de Condicionantes, actas u otros"
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
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <form-customers 
              :use-in-modal="true"
              ref="formCustomersRef"
              @fieldSelected="getCustomers"
            />
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Matería <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Matería"
                  >
                    <v-select 
                      v-model="form.id_matter"
                      :options="matters"
                      :reduce="e => e.id_matter"
                      label="matter"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Aspecto <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Aspecto"
                  >
                    <v-select 
                      v-model="form.id_aspect"
                      :options="aspects"
                      :reduce="e => e.id_aspect"
                      label="aspect"
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
import { required, max, double,  } from '../../validations'
import FormCustomers from '../components/customers/FormCustomers.vue'
import { getSpecificRequirement, storeSpecificRequirement, updateSpecificRequirement } from '../../../services/specifcRequirementService'
import { getApplicationTypesSpecific, getMatters, getAspects } from '../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    FormCustomers
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
      double,
      dialog: false,
      matters: [],
      aspects: [],
      applicationTypes: [],
      form: {
        id_customer: null,
        id_corporate: null,
        id_matter: null,
        id_aspect: null,
        order: null,
        id_application_type: null,
        no_requirement: null,
        requirement: null,
        description: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Requerimiento de Condicionantes, actas u otros' : `Editar: ${this.register.no_requirement}`
    },
  },
  watch: {
    'form.id_matter': function(newValue, oldValue) {
      this.aspects = []
      if (oldValue != null && (newValue != oldValue) ) {
        this.form.id_aspect = null
      }
      if (newValue != null) {
        this.getAspects()
      }
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
      await this.getApplicationTypesSpecific()
      await this.getMatters()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getSpecificRequirement(this.register.id_requirement)
        const { id_matter, id_aspect, order, id_application_type, no_requirement, requirement, description } = data.data
        this.form.order = order
        this.form.id_application_type = id_application_type
        this.form.id_matter = id_matter
        this.form.id_aspect = id_aspect
        this.form.no_requirement = no_requirement
        this.form.requirement = requirement
        this.form.description = description
        setTimeout(async () => {
          await this.$refs.formCustomersRef.loadData(data.data, true)
        }, 1000) 
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setRequirement() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeSpecificRequirement(this.form)
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
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateSpecificRequirement(this.register.id_requirement, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getApplicationTypesSpecific() {
      try {
        this.showLoadingMixin()
        const { data } = await getApplicationTypesSpecific()
        this.applicationTypes = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getMatters() {
      try {
        this.showLoadingMixin()
        const { data } = await getMatters()
        this.matters = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getAspects() {
      try {
        this.showLoadingMixin()
        const filters = { id_matter: this.form.id_matter }
        const { data } = await getAspects({}, filters)
        this.aspects = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getCustomers({ id_customer, id_corporate }) {
      this.form.id_customer = id_customer
      this.form.id_corporate = id_corporate
    },
    reset() {
      this.form.id_customer = null
      this.form.id_corporate = null
      this.form.id_matter = null
      this.form.id_aspect = null
      this.form.order = null
      this.form.id_application_type = null
      this.form.no_requirement = null
      this.form.requirement = null
      this.form.description = null
    },
  }
}
</script>

<style>

</style>