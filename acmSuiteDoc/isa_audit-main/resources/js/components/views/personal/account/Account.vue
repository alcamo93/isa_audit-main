<template>
  <b-card>
    <loading :show="loadingMixin" />
    <validation-observer ref="rulesForm">
      <b-form
        ref="formRegister"
        autocomplete="off"
      >
        <b-row>
          <b-col>
            <modal-image 
              :record="user"
              @successfully="getUser"
            />
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12" md="4">
            <b-form-group>
              <label>Planta</label>
              <validation-provider
                #default="{ errors }"
                name="Planta"
              >
                <b-form-input
                  disabled
                  v-model="form.corp_tradename"
                ></b-form-input>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col sm="12" md="4">
            <b-form-group>
              <label>
                Correo
                <span class="text-danger">*</span>
              </label>
              <validation-provider
                #default="{ errors }"
                rules="required|max:255|email"
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
              <label>Correo Secundario</label>
              <validation-provider
                #default="{ errors }"
                rules="max:255|email"
                name="Correo Secundario"
              >
                <b-form-input
                  v-model="form.secondary_email"
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
                Nombre(s)
                <span class="text-danger">*</span>
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
                Apellido Paterno
                <span class="text-danger">*</span>
              </label>
              <validation-provider
                #default="{ errors }"
                rules="required|max:255"
                name="Apellido Paterno"
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
              <label>Apellido Materno</label>
              <validation-provider
                #default="{ errors }"
                rules="max:255"
                name="Apellido Materno"
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
          <b-col sm="12" md="6">
            <b-form-group>
              <label>RFC</label>
              <validation-provider
                #default="{ errors }"
                rules="alpha_num|min:13|max:13"
                name="RFC"
              >
                <b-form-input
                  v-model="form.rfc"
                ></b-form-input>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col sm="12" md="6">
            <b-form-group>
              <label>Perfil</label>
              <validation-provider
                #default="{ errors }"
                name="Perfil"
              >
                <b-form-input
                  disabled
                  v-model="form.profile_name"
                ></b-form-input>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12" md="4">
            <b-form-group>
              <label>Género</label>
              <validation-provider
                #default="{ errors }"
                rules="required"
                name="Género"
              >
                <v-select 
                  v-model="form.gender"
                  :options="genders"
                  :reduce="e => e.gender"
                  label="gender_name"
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
              <label>Celular</label>
              <validation-provider
                #default="{ errors }"
                rules="digits:10"
                name="Celular"
              >
                <b-form-input
                  v-model="form.phone"
                ></b-form-input>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col sm="12" md="4">
            <b-form-group>
              <label>Fecha de Nacimiento</label>
              <validation-provider
                #default="{ errors }"
                rules="required"
                name="Fecha de Nacimiento"
              >
                <vue-date-picker 
                  input-class="form-control"
                  format="DD/MM/YYYY"
                  value-type="YYYY-MM-DD"
                  v-model="form.birthdate"
                ></vue-date-picker>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <b-button
              variant="success"
              class="float-right"
              @click="updateUser"
            >
              Actualizar Perfil
            </b-button>
          </b-col>
        </b-row>
      </b-form>
    </validation-observer>
  </b-card>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, digits, alpha_num, min, max, email } from '../../../validations'
import ModalImage from './ModalImage.vue'
import { getAccount, updateAccount } from '../../../../services/accountService'

export default {
  mounted() {
    this.getUser()
  },
  components: {
    ValidationProvider,
    ValidationObserver,
    ModalImage,
  },
  props: {
    idUser: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      required,
      digits,
      alpha_num,
      min,
      max,
      email,
      record: null,
      genders: [
        {gender: 'Masculino', gender_name: 'Masculino'},
        {gender: 'Femenino', gender_name: 'Femenino'}
      ],
      form: {
        corp_tradename: null,
        email: null,
        secondary_email: null,
        first_name: null,
        second_name: null,
        last_name: null,
        rfc: null,
        profile_name: null,
        gender: null,
        phone: null,
        birthdate: null,
      }
    }
  },
  computed: {
    user() {
      const empty = {
        id_user: null,
        image: null
      }
      return this.record != null ? this.record : empty
    }
  },
  methods: {
    async getUser() {
      try {
        this.showLoadingMixin()
        const { data } = await getAccount()
        this.record = data.data
        const { corporate, person, profile, email, secondary_email } = data.data
        const { first_name, second_name, last_name, rfc, gender, phone, birthdate } = person
        this.form.corp_tradename = corporate.corp_tradename_format
        this.form.email = email
        this.form.secondary_email = secondary_email
        this.form.first_name = first_name
        this.form.second_name = second_name
        this.form.last_name = last_name
        this.form.rfc = rfc
        this.form.profile_name = profile.profile_name
        this.form.gender = gender
        this.form.phone = phone
        this.form.birthdate = birthdate
        this.showLoadingMixin()
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
        const { data } = await updateAccount(this.form)
        await this.getUser()
        this.showLoadingMixin()
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style>

</style>