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
      title="Editar Perfil"
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
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Perfil <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Perfil"
                  >
                    <b-form-input
                      v-model="form.profile_name"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Tipo de Perfil <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Tipo de Perfil"
                  >
                    <v-select 
                      v-model="form.id_profile_type"
                      :options="types"
                      :reduce="e => e.id_profile_type"
                      label="type"
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
                      v-model="form.id_status"
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
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button v-if="isNew"
            class="btn btn-success float-right"
            @click="setProfile"
          >
            Registrar
          </b-button>
          <b-button v-else
            class="btn btn-success float-right"
            @click="updateProfile"
          >
            Actualizar
          </b-button>
          <b-button 
            class="btn btn-danger float-right mr-2"
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
import { required, min, max } from '../../validations'
import FormCustomers from '../components/customers/FormCustomers'
import { getProfile, storeProfile, updateProfile } from '../../../services/profileService'
import { getProfileTypes, getStatus } from '../../../services/catalogService'

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
      dialog: false,
      types: [],
      status: [],
      form: {
        id_customer: null,
        id_corporate: null,
        id_status: null,
        id_profile_type: null,
        profile_name: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Perfil' : `Editar: ${this.register.profile_name}`
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.getStatus()
      await this.getProfileTypes()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getProfile(this.register.id_profile)
        const { id_status, id_profile_type, profile_name } = data.data
        this.form.id_status = id_status
        this.form.id_profile_type = id_profile_type
        this.form.profile_name = profile_name
        setTimeout(async () => {
          await this.$refs.formCustomersRef.loadData(data.data, true)
        }, 1000) 
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setProfile() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeProfile(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateProfile() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateProfile(this.register.id_profile, this.form)
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
    async getProfileTypes() {
      try {
        const { data } = await getProfileTypes()
        this.types = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getStatus() {
      try {
        const filters = { group: 1 }
        const { data } = await getStatus({}, filters)
        this.status = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.id_customer = null
      this.form.id_corporate = null
      this.form.id_status = null
      this.form.id_profile_type = null
      this.form.profile_name = null
    }
  }
}
</script>

<style>

</style>