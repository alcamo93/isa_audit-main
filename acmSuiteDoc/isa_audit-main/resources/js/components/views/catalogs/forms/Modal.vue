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
      title="Editar Formulario"
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
                    Nombre <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Nombre"
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
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Matería <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Matería"
                  >
                    <v-select 
                      v-model="form.matter_id"
                      :options="matters"
                      :reduce="e => e.id_matter"
                      label="matter"
                      :loading="progress.matters"
                    >
                      <div slot="no-options">
                        No se encontraron registros
                      </div>
                    </v-select>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Aspecto <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Aspecto"
                  >
                    <v-select 
                      v-model="form.aspect_id"
                      :options="aspects"
                      :reduce="e => e.id_aspect"
                      label="aspect"
                      :loading="progress.aspects"
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
            @click="setForm"
          >
            Registrar
          </b-button>
          <b-button v-else
            variant="success"
            class="float-right"
            @click="updateForm"
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
import { required } from '../../../validations'
import { getForm, storeForm, updateForm } from '../../../../services/FormService'
import { getMatters, getAspects } from '../../../../services/catalogService'

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
      dialog: false,
      formatDatePicker: { 
        year: 'numeric', 
        month: 'numeric', 
        day: 'numeric' 
      },
      matters: [],
      aspects: [],
      form: {
        name: null,
        matter_id: null,
        aspect_id: null,
      },
      progress: {
        matters: false,
        aspects: false,
      }
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Formulario ' : `Editar: ${this.register.name}`
    }
  },
  watch: {
    'form.matter_id': function(value) {
      if (value != null) {
        this.getAspects()
      }
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadNewRegister()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadNewRegister() {
      this.showLoadingMixin()
      await this.getMatters()
      this.showLoadingMixin()
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getForm(this.register.id)
        const { name, matter_id, aspect_id } = data.data
        this.form.name = name
        this.form.matter_id = matter_id
        this.form.aspect_id = aspect_id
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getMatters() {
      try {
        this.progress.matters = true
        const { data } = await getMatters()
        this.matters = data.data
        this.progress.matters = false
      } catch (error) {
        this.progress.matters = false
        this.responseMixin(error)
      }
    },
    async getAspects() {
      try {
        this.progress.aspects = true
        const filters = { id_matter: this.form.matter_id }
        const { data } = await getAspects({}, filters)
        this.aspects = data.data
        this.progress.aspects = false
      } catch (error) {
        this.progress.aspects = false
        this.responseMixin(error)
      }
    },
    async setForm() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeForm(this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateForm() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateForm(this.register.id, this.form)
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
      this.form.name = null
      this.form.matter_id = null
      this.form.aspect_id = null
    }
  }
}
</script>

<style>

</style>