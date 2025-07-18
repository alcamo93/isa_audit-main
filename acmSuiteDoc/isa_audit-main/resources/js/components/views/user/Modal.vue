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
      title="Editar Usuario"
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
                    Nombre <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Nombre"
                  >
                    <b-form-input
                      v-model="form.first_name"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Primer Apellido <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Primer Apellido"
                  >
                    <b-form-input
                      v-model="form.second_name"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Segundo Apellido
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="max:255"
                    name="Segundo Apellido"
                  >
                    <b-form-input
                      v-model="form.last_name"
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
                    Correo <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Correo"
                  >
                    <b-form-input
                      v-model="form.email"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Perfil <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Perfil"
                  >
                    <v-select 
                      v-model="form.id_profile"
                      :options="profiles"
                      :reduce="e => e.id_profile"
                      label="profile_name"
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
            variant="success"
            class="float-right"
            @click="setUser"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateUser"
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
import { getUser, storeUser, updateUser } from '../../../services/UserService'
import { getProfiles, getStatus } from '../../../services/catalogService'

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
      status: [],
      form: {
        id_customer: null,
        id_corporate: null,
        id_status: null,
        id_profile: null,
        first_name: null,
        second_name: null,
        last_name: null,
        email: null,
      },
    }
  },
  watch: {
    'form.id_corporate': function(value) {
      this.profiles = []
      if (value != null) {
        this.getProfiles()
      }

    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Usuario ' : `Editar: ${this.register.person.full_name}`
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.getStatus()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getUser(this.register.id_user)
        const { id_status, id_profile, email,  person } = data.data
        const { first_name, second_name, last_name } = person
        this.form.id_status = id_status
        this.form.id_profile = id_profile
        this.form.email = email
        this.form.first_name = first_name
        this.form.second_name = second_name
        this.form.last_name = last_name
        setTimeout(async () => {
          await this.$refs.formCustomersRef.loadData(data.data, true)
        }, 1000) 
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setUser() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeUser(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateUser() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateUser(this.register.id_user, this.form)
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
    async getProfiles() {
      try {
        const filters = { id_corporate: this.form.id_corporate }
        const params = { scope: 'source' }
        const { data } = await getProfiles(params, filters)
        this.profiles = data.data.map(item => { 
          return { id_profile: item.id_profile, profile_name: `${item.profile_name} (${item.type.type})` } 
        })
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
      this.form.id_profile = null
      this.form.email = null
      this.form.first_name = null
      this.form.second_name = null
      this.form.last_name = null
    }
  }
}
</script>

<style>

</style>