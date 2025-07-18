<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      :variant="isNew ? 'info' : 'success'"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="person-square" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      no-close-on-backdrop
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
                    Correo eléctronico <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|email|max:45"
                    name="Correo eléctronico"
                  >
                    <b-form-input
                      v-model="form.ct_email"
                      type="email"
                      placeholder="ejemplo@mail.com"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Nombre(s) <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Nombre"
                  >
                    <b-form-input
                      v-model="form.ct_first_name"
                      placeholder="Nombre"
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
                    Primer Apellido <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:255"
                    name="Primer Apellido"
                  >
                    <b-form-input
                      v-model="form.ct_second_name"
                      placeholder="Primer Apellido"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
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
                      v-model="form.ct_last_name"
                      placeholder="Segundo Apellido"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6" lg="3">
                <b-form-group>
                  <label>
                    Puesto <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:45"
                    name="Puesto"
                  >
                    <b-form-input
                      v-model="form.degree"
                      placeholder="Director General"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6" lg="3">
                <b-form-group>
                  <label>
                    Teléfono Móvil <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|numeric|max:16"
                    name="Teléfono Móvil"
                  >
                    <b-form-input
                      v-model="form.ct_cell"
                      placeholder="00 0000 0000"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6" lg="3">
                <b-form-group>
                  <label>
                    Extensión
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="numeric|max:16"
                    name="Extensión"
                  >
                    <b-form-input
                      v-model="form.ct_ext"
                      placeholder="0000"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6" lg="3">
                <b-form-group>
                  <label>
                    Teléfono Oficina
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="numeric|max:16"
                    name="Teléfono Oficina"
                  >
                    <b-form-input
                      v-model="form.ct_phone_office"
                      placeholder="00 0000 0000"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <div class="my-2 mx-auto text-justify font-weight-bold">
                  <span class="font-italic text-uppercase text-info">* Nota</span><br>
                  Los datos que se muestran en el formulario serán utilizados para notificar cualquier información sobre 
                  el contrato al que esta relacionado la planta
                </div>
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
            @click="setContact"
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
import { required, max, email, numeric } from '../../../validations'
import { getContact, storeContact, updateContact } from '../../../../services/contactService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    title: {
      type: String,
      required: true
    },
    idCustomer: {
      type: Number,
      required: true,
    },
    idCorporate: {
      type: Number,
      required: true,
    },
    idContact: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      required,
      max,
      email,
      numeric,
      dialog: false,
      form: {
        id_contact: null,
        ct_email: null,
        ct_phone_office: null,
        ct_ext: null,
        ct_cell: null,
        ct_first_name: null,
        ct_second_name: null,
        ct_last_name: null,
        degree: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.title
    },
    titleTooltip() {
      const stage = this.isNew ? 'Agregar' : 'Modificar'
      return `${stage} Contacto`
    },
    isNew() {
      return this.idContact == 0
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
        const { data } = await getContact(this.idCustomer, this.idCorporate, this.idContact)
        const { id_contact, ct_email, ct_phone_office, ct_ext, ct_cell, 
          ct_first_name, ct_second_name, ct_last_name, degree } = data.data
        this.form.id_contact = id_contact
        this.form.ct_email = ct_email
        this.form.ct_phone_office = ct_phone_office
        this.form.ct_ext = ct_ext
        this.form.ct_cell = ct_cell
        this.form.ct_first_name = ct_first_name
        this.form.ct_second_name = ct_second_name
        this.form.ct_last_name = ct_last_name
        this.form.degree = degree
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setContact() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeContact(this.idCustomer, this.idCorporate, this.form)
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
        const { data } = await updateContact(this.idCustomer, this.idCorporate, this.form.id_contact, this.form)
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
      this.form.id_contact = null
      this.form.ct_email = null
      this.form.ct_phone_office = null
      this.form.ct_ext = null
      this.form.ct_cell = null
      this.form.ct_first_name = null
      this.form.ct_second_name = null
      this.form.ct_last_name = null
      this.form.degree = null
    }
  }
}
</script>

<style>

</style>