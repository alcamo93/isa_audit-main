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
      title="Editar Marco jurídico"
      variant="warning"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesFormProcess">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Marco jurídico (Ley, Reglamento, Norma, etc) <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Marco jurídico"
                  >
                    <b-form-input
                      v-model="form.guideline"
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
                    Siglas <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Siglas"
                  >
                    <b-form-input
                      v-model="form.initials_guideline"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Última reforma
                    <span 
                      class="text-danger"
                    >*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="última reforma"
                  >
                    <vue-date-picker 
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      v-model="form.last_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Clasificación <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Clasificación"
                  >
                    <v-select 
                      v-model="form.id_legal_c"
                      @input="setFilters"
                      :options="classifications"
                      :reduce="e => e.id_legal_c"
                      label="legal_classification"
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
                    Competencia <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Competencia"
                  >
                    <v-select 
                      v-model="form.id_application_type"
                      @input="setFilters"
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
                      @input="setFilters"
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
                      @input="setFilters"
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
              <b-col sm="12" v-if="useGuidelineOptions">
                <b-form-group>
                    <label>
                      Marco Jurídico
                    </label>
                      <multiselect 
                        v-model="guidelines_ext" 
                        :options="guidelines" 
                        :multiple="true" 
                        placeholder="Selecciona marco jurídico"
                        label="guideline" 
                        track-by="id_guideline"
                        selectLabel="Clic para seleccionar"
                        selectedLabel="Seleccionado"
                        deselectLabel="Clic para remover"
                      >
                        <template slot="noOptions">
                          No se encontraron registros
                        </template>
                      </multiselect>
                  </b-form-group>

              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Objetivo
                  </label>
                  <b-form-textarea
                    v-model="form.objective"
                    rows="3"
                    max-rows="6"
                  ></b-form-textarea>
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
            @click="setGuideline"
          >
            Registrar
          </b-button>
          <b-button v-else
            class="float-right"
            variant="success"
            @click="updateGuideline"
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
import { required } from '../../../validations'
import { getGuideline, storeGuideline, updateGuideline } from '../../../../services/guidelineService'
import { getLegalClassifications, getApplicationTypes, getStates, getCities, getGuidelineSource } from '../../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
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
      dialog: false,
      loadDataActive: false,
      classifications: [],
      applicationTypes: [],
      states: [],
      cities: [],
      showGuidelinesInput: false,
      guidelines: [],
      guidelines_ext: [],
      form: {
        guideline: null,
        initials_guideline: null,
        last_date: null,
        id_legal_c: null,
        id_application_type: null,
        id_state: null,
        id_city: null,
        guidelines_ext: [],
        objective: null
      },
      filters: {
        id_legal_classification: null,
        id_application_type: null,
        id_state: null,
        id_city: null
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Marco Júridico' : `Editar: ${this.register.guideline}`
    },
    useStateOptions() {
      return this.form.id_application_type == 2 || this.form.id_application_type == 4
    },
    useCityOptions() {
      return this.form.id_application_type == 4
    },
    useGuidelineOptions() {
      const allowedGuidelineIds = [4, 6, 7, 9, 14]
      return allowedGuidelineIds.includes(this.form.id_legal_c)
    },
    columnsFormLocation() {
      const columns = this.useStateOptions & this.useCityOptions ? 2 : 1
      return (12 / columns)
    },
    guidelinesExtIds() {
      return this.guidelines_ext.map(guideline => guideline.id_guideline);
    }
  },
  watch: {
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
      await this.loadNewRegister()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadNewRegister() {
      this.showLoadingMixin()
      await this.getStates()
      await this.getLegalClassifications()
      await this.getApplicationTypes()
      this.showLoadingMixin()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        this.loadDataActive = true
        const { data } = await getGuideline(this.register.id_guideline)
        const { guideline, initials_guideline, last_date, id_legal_c, id_application_type, id_state, id_city, guidelines, objective } = data.data
        this.form.guideline = guideline
        this.form.initials_guideline = initials_guideline
        this.form.last_date = last_date
        this.form.id_legal_c = id_legal_c
        this.form.id_application_type = id_application_type
        this.form.id_state = id_state
        this.form.id_city = id_city
        this.guidelines_ext = guidelines
        this.form.objective = objective
        setTimeout(() => {
          this.loadDataActive = false
          this.setFilters()
        }, 500);
        this.showLoadingMixin()
      } catch (error) {
        this.loadDataActive = false
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
    async getLegalClassifications() {
      try {
        this.showLoadingMixin()
        const { data } = await getLegalClassifications()
        this.classifications = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getStates() {
      try {
        this.showLoadingMixin()
        const filters = { id_country: 1 }
        const { data } = await getStates({}, filters)
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
    async setGuideline() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        this.form.guidelines_ext = this.guidelinesExtIds
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeGuideline(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateGuideline() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        this.form.guidelines_ext = this.guidelinesExtIds
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateGuideline(this.register.id_guideline, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setFilters() {
      this.form.guidelines_ext = []
      this.guidelines = []
      const idApplicationType = this.form.id_application_type
      this.filters.id_application_type = idApplicationType
      if (idApplicationType == 1) { // FEDERAL
        this.filters.id_state = null
        this.filters.id_city = null
        this.getGuideline()
        return  
      }
      if (idApplicationType == 2 && this.form.id_state != null) { // STATE
        this.filters.id_state = this.form.id_state
        this.filters.id_city = null
        this.getGuideline()
        return
      }
      if (idApplicationType == 4 && this.form.id_state != null && this.form.id_city != null) { // LOCAL
        this.filters.id_state = this.form.id_state
        this.filters.id_city = this.form.id_city
        this.getGuideline()
        return
      }
    },
    async getGuideline() {
      try {
        this.showLoadingMixin()
        const params = {}
        const { data } = await getGuidelineSource(params, this.filters)
        this.guidelines = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.guideline = null
      this.form.initials_guideline = null
      this.form.last_date = null
      this.form.id_legal_c = null
      this.form.id_application_type = null
      this.form.id_state = null
      this.form.id_city = null
      this.form.guidelines_ext = []
      this.form.objective = null
    }
  }
}
</script>

<style>

</style>