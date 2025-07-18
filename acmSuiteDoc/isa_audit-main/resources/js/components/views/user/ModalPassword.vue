<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover
      title="Cambiar Contraseña"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="lock-fill" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form ref="formRegister" autocomplete="off">
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Nueva Contraseña <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|min:8|max:255"
                    name="Nueva Contraseña"
                  >
                    <b-input-group>
                      <template #append>
                        <b-input-group-append>
                          <b-button @click="handlerPassword">
                            <b-icon :icon="iconShow"></b-icon>
                          </b-button>
                        </b-input-group-append>
                      </template>
                      <b-form-input 
                        v-model="form.password"
                        :type="typeInput"
                      ></b-form-input>
                    </b-input-group>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Confirmar Contraseña <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|confirmed:Nueva Contraseña|min:8|max:255"
                    name="Confirmar Contraseña"
                  >
                    <b-input-group>
                      <template #append>
                        <b-input-group-append>
                          <b-button @click="handlerPassword">
                            <b-icon :icon="iconShow"></b-icon>
                          </b-button>
                        </b-input-group-append>
                      </template>
                      <b-form-input
                        v-model="form.password_confirmation"
                        :type="typeInput"
                      ></b-form-input>
                    </b-input-group>
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
          <b-button
            variant="success"
            class="float-right"
            @click="updateUserPassword"
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
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required, min, max, confirmed, password } from "../../validations";
import { updateUserPassword } from "../../../services/UserService";

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
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
      confirmed,
      password,
      dialog: false,
      showPassword: false,
      form: {
        password: null,
        password_confirmation: null,
      },
    };
  },
  computed: {
    titleModal() {
      return `Cambiar Contraseña para: ${this.register.person.full_name}`;
    },
    typeInput() {
      return this.showPassword ? 'text' : 'password'
    },
    iconShow() {
      return this.showPassword ? 'eye-slash-fill' : 'eye-fill'
    }
  },
  methods: {
    async showModal() {
      this.reset();
      this.dialog = true;
    },
    async updateUserPassword() {
      try {
        const isValid = await this.$refs.rulesForm.validate();
        if (!isValid) return;
        this.showLoadingMixin();
        const { data } = await updateUserPassword(this.register.id_user, this.form);
        this.showLoadingMixin();
        this.dialog = false;
        this.$emit("successfully");
        this.responseMixin(data);
      } catch (error) {
        this.showLoadingMixin();
        this.responseMixin(error);
      }
    },
    handlerPassword() {
      this.showPassword = !this.showPassword
    },
    reset() {
      this.form.password = null;
      this.form.password_confirmation = null;
    },
  },
};
</script>
  
  <style>
</style>