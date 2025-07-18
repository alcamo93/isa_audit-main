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
      title="Editar Licencia"
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
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Nombre de Licencia <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:50"
                    name="Nombre de Licencia"
                  >
                    <b-form-input
                      v-model="form.name"
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
                    Periodo(s) <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|numeric|min_value:1"
                    name="Periodo(s)"
                  >
                    <b-form-input
                      v-model="form.num_period"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Unidad de Periodo <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Unidad de Periodo"
                  >
                    <v-select 
                      v-model="form.period_id"
                      :options="periods"
                      :reduce="e => e.id"
                      label="name"
                      placeholder="Seleccionar"
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
                    Estatus <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Estatus"
                  >
                    <v-select 
                      v-model="form.status_id"
                      :options="status"
                      :reduce="e => e.id_status"
                      label="status"
                      placeholder="Seleccionar"
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
              <b-col v-for="(item, index) in this.form.quantity" :key="item.id_profile_type" sm="12" md="4">
                <b-form-group>
                  <label>
                    {{ `Cantidad de usuarios tipo ${item.type}` }} 
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|numeric|min_value:1"
                    :name="`Cantidad de usuarios tipo ${item.type}`"
                  >
                    <b-form-input
                      v-model="form.quantity[index].quantity"
                    ></b-form-input>
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
            variant="success"
            class="float-right"
            @click="setLicense"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateLicense"
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
import { required, max, min_value, numeric } from '../../validations'
import FormCustomers from '../components/customers/FormCustomers'
import { getLicense, storeLicense, updateLicense } from '../../../services/licenseService'
import { getStatus, getPeriods, getProfileTypes} from '../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    FormCustomers,
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
      min_value,
      numeric,
      dialog: false,
      status: [],
      periods: [],
      types: [],
      form: {
        name: null,
        num_period: null,
        period_id: null,
        status_id: null,
        quantity: []
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Licencia ' : `Editar: ${this.register.name}`
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.getProfileTypes()
      await this.getPeriods()
      await this.getStatus()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getLicense(this.register.id)
        const { name, num_period, period_id, status_id, quantity } = data.data
        this.form.name = name
        this.form.num_period = num_period
        this.form.period_id = period_id
        this.form.status_id = status_id
        quantity.forEach(item => {
          const index = this.form.quantity.findIndex(obj => obj.id_profile_type == item.id_profile_type)
          if (index != -1) this.form.quantity[index].quantity = item.pivot.quantity
        })
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setLicense() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeLicense(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateLicense() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateLicense(this.register.id, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getStatus() {
      try {
        this.showLoadingMixin()
        const filters = { group: 1 }
        const { data } = await getStatus({}, filters)
        this.status = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getPeriods() {
      try {
        this.showLoadingMixin()
        const { data } = await getPeriods()
        this.periods = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getProfileTypes() {
      try {
        this.showLoadingMixin()
        const filters = { owner: '0' }
        const { data } = await getProfileTypes({}, filters)
        this.types = data.data
        this.form.quantity = this.types.map(item => {
          return { type: item.type, id_profile_type: item.id_profile_type, quantity: null }
        })
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.name = null
      this.form.num_period = null
      this.form.period_id = null
      this.form.status_id = null
      this.form.quantity = []
    }
  }
}
</script>

<style>

</style>