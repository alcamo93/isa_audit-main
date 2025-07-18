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
      title="Modificar/Extender/Renovar Contrato"
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
            <form-customers 
              :use-in-modal="true"
              ref="formCustomersRef"
              @fieldSelected="getCustomers"
            />
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Contrato <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:50"
                    name="Contrato"
                  >
                    <b-form-input
                      v-model="form.contract"
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
                    Licencia <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Licencia"
                  >
                    <v-select 
                      v-model="form.id_license"
                      :options="licenses"
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
                    Fecha de inicio <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Fecha de inicio"
                  >
                    <vue-date-picker 
                      :disabled="disabledStartDate"
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      v-model="form.start_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Fecha de termino <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Fecha de termino"
                  >
                    <vue-date-picker 
                      :disabled="true"
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      v-model="form.end_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row v-if="form.id_license">
              <b-col>
                <h5 class="font-weight-bold">Resumen</h5>
                <p class="text-justify">
                  Este contrato tendrá la licencia <span class="font-weight-bold">{{ licenseSelected.name }}</span> que consta de una duración de 
                  <span class="font-weight-bold">{{ licenseSelected.num_period }} {{ licenseSelected.period.name }}</span> comenzando en la fecha 
                  <span class="font-weight-bold">{{ formatDateMixin(this.form.start_date) }}</span> y terminando en 
                  <span class="font-weight-bold">{{ formatDateMixin(this.form.end_date) }}</span> con la cantidad de usuarios siguiente:
                </p>
                <ul>
                  <li v-for="item in licenseSelected.quantity" :key="item.id_profile_type">
                    <span class="font-weight-bold">{{ item.pivot.quantity }}</span> 
                    usuario(s) tipo <span class="font-weight-bold">{{ item.type }}</span>
                  </li>
                </ul>
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
            @click="setContract"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateContract"
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
import { required, min, max, email } from '../../validations'
import FormCustomers from '../components/customers/FormCustomers'
import { getContract, storeContract, updateContract } from '../../../services/contractService'
import { getLicenses } from '../../../services/catalogService'
import { dateFuture } from '../components/scripts/dates'

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
      min,
      max,
      email,
      dialog: false,
      profiles: [],
      licenses: [],
      form: {
        id_customer: null,
        id_corporate: null,
        contract: null,
        id_license: null,
        start_date: null,
        end_date: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Contrato ' : `Editar: ${this.register.contract}`
    },
    disabledStartDate() {
      return this.form.id_license == null
    },
    licenseSelected() {
      if (this.form.id_license == null) return null
      return this.licenses.find(item => item.id == this.form.id_license)
    }
  },
  watch: {
    'form.id_license': function() {
      this.form.start_date = null
    },
    'form.start_date': function(startDate) {
      if (startDate == null) {
        this.form.end_date = null
        return
      }
      const { num_period, period } = this.licenseSelected
      this.form.end_date = startDate == null ? null : dateFuture(startDate, num_period, period.key)
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.getLicenses()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getContract(this.register.id_contract)
        const { contract, id_license, start_date, end_date } = data.data
        this.form.contract = contract
        this.form.id_license = id_license
        this.form.start_date = start_date
        this.form.end_date = end_date
        setTimeout(async () => {
          await this.$refs.formCustomersRef.loadData(data.data, true)
          this.form.start_date = start_date
          this.form.end_date = end_date
        }, 1000) 
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setContract() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeContract(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateContract() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateContract(this.register.id_contract, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getCustomers({ id_customer, id_corporate }) {
      this.form.id_customer = id_customer
      this.form.id_corporate = id_corporate
    },
    async getLicenses() {
      try {
        const params = { scope: 'source,quantity' }
        const { data } = await getLicenses(params)
        this.licenses = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.id_customer = null
      this.form.id_corporate = null
      this.form.contract = null
      this.form.id_license = null
      this.form.start_date = null
      this.form.end_date = null
    }
  }
}
</script>

<style>

</style>