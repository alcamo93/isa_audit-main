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
      title="Editar Cliente"
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
                    Nombre Comercial <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:100"
                    name="Nombre Comercial"
                  >
                    <b-form-input
                      v-model="form.cust_tradename"
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
                    Razón Social <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:100"
                    name="Razón Social"
                  >
                    <b-form-input
                      v-model="form.cust_trademark"
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
            @click="setCustomer"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateCustomer"
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
import { required, max } from '../../validations'
import { getCustomer, storeCustomer, updateCustomer } from '../../../services/customerService'

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
      max,
      dialog: false,
      form: {
        cust_tradename: null,
        cust_trademark: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Cliente ' : `Editar: ${this.register.cust_tradename}`
    }
  },
  methods: {
    async showModal() {
      this.reset()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getCustomer(this.register.id_customer)
        const { cust_tradename, cust_trademark } = data.data
        this.form.cust_tradename = cust_tradename
        this.form.cust_trademark = cust_trademark
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setCustomer() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeCustomer(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateCustomer() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateCustomer(this.register.id_customer, this.form)
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
      this.form.cust_tradename = null
      this.form.cust_trademark = null
    }
  }
}
</script>

<style>

</style>