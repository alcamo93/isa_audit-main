<template>
  <fragment>
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
      title="Editar Planta"
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
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Nombre Comercial <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:100"
                    name="Nombre Comercial"
                  >
                    <b-form-input
                      v-model="form.corp_tradename"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Razón Social <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:100"
                    name="Razón Social"
                  >
                    <b-form-input
                      v-model="form.corp_trademark"
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
                    RFC <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|min:12|max:13"
                    name="RFC"
                  >
                    <b-form-input
                      v-model="form.rfc"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Estatus <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Estatus"
                  >
                    <v-select 
                      v-model="form.id_status"
                      :options="status"
                      :reduce="e => e.id_status"
                      label="status"
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
                    Tipo de Planta <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Tipo de Planta"
                  >
                    <v-select 
                      v-model="form.type"
                      :options="types"
                      :reduce="e => e.id_type"
                      label="type"
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
              <b-col v-if="!form.add_new" sm="12" md="8">
                <b-form-group>
                  <label>
                    Giro <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Giro"
                  >
                    <v-select 
                      v-model="form.id_industry"
                      :options="industries"
                      :reduce="e => e.id_industry"
                      label="industry"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col v-else sm="12" md="8">
                <b-form-group>
                  <label>
                    Nuevo Giro <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Nuevo Giro"
                  >
                    <b-form-input
                      v-model="form.new_industry"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <b-form-checkbox 
                    class="mt-4"
                    v-model="form.add_new" 
                    :value="true"
                    :unchecked-value="false"
                    switch size="lg"
                  >
                  Especificar Giro
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
            @click="setCorporate"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateCorporate"
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
  </fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, max } from '../../../validations'
import { getCorporate, storeCorporate, updateCorporate } from '../../../../services/corporateService'
import { getStatus, getIndustries } from '../../../../services/catalogService'

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
    idCustomer: {
      type: Number,
      required: true,
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
      status: [],
      industries: [],
      types: [
        { id_type: 0, type: 'Operativa' },
        { id_type: 1, type: 'Nueva' },
      ],
      form: {
        corp_tradename: null,
        corp_trademark: null,
        rfc: null,
        id_industry: null,
        id_status: null,
        type: null,
        new_industry: null,
        add_new: false,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Planta ' : `Editar: ${this.register.corp_tradename}`
    }
  },
  watch: {
    'form.add_new': function(value) {
      if (!value) this.form.new_industry = null
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadCatalogs()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadCatalogs() {
      await this.getStatus()
      await this.getIndustries()
    },
    async getStatus() {
      try {
        this.showLoadingMixin()
        const { data } = await getStatus({}, { group: 1 })
        this.status = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getIndustries() {
      try {
        this.showLoadingMixin()
        const { data } = await getIndustries()
        this.industries = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getCorporate(this.idCustomer, this.register.id_corporate)
        const { corp_tradename, corp_trademark, rfc, id_industry, id_status, type } = data.data
        this.form.corp_tradename = corp_tradename
        this.form.corp_trademark = corp_trademark
        this.form.rfc = rfc
        this.form.id_industry = id_industry
        this.form.id_status = id_status
        this.form.type = type
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setCorporate() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeCorporate(this.idCustomer, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateCorporate() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateCorporate(this.idCustomer, this.register.id_corporate, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.corp_tradename = null
      this.form.corp_trademark = null
      this.form.rfc = null
      this.form.id_industry = null
      this.form.id_status = null
      this.form.type = null
      this.form.new_industry = null
      this.form.add_new = false
    }
  }
}
</script>

<style>

</style>