<template>
  <fragment>
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
      title="Editar Aspecto"
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
      <loading :show="loadingMixin" />
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Orden <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|integer"
                    name="Orden"
                  >
                    <b-form-input
                      v-model="form.order"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="8">
                <b-form-group>
                  <label>
                    Nombre de Aspecto <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Nombre de Aspecto"
                  >
                    <b-form-input
                      v-model="form.aspect"
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
            @click="setAspect"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateAspect"
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
import { required, integer } from '../../../../validations'
import { getAspect, storeAspect, updateAspect } from '../../../../../services/matterService'

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
    idMatter: {
      type: Number,
      required: true
    },
    register: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      required,
      integer,
      dialog: false,
      form: {
        order: null,
        aspect: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Aspecto ' : `Editar: ${this.register.aspect}`
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
        const { data } = await getAspect(this.idMatter, this.register.id_aspect)
        const { aspect, order } = data.data
        this.form.aspect = aspect
        this.form.order = order
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setAspect() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeAspect(this.idMatter, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateAspect() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateAspect(this.idMatter, this.register.id_aspect, this.form)
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
      this.form.order = null
      this.form.aspect = null
    }
  }
}
</script>

<style>

</style>