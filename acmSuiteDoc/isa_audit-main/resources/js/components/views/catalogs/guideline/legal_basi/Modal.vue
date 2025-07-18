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
      title="Editar Artículo"
      variant="warning"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
      :no-enforce-focus="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="2">
                <b-form-group>
                 <label>
                    Orden <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|double"
                    name="Orden"
                  >
                    <b-form-input
                      v-model="form.order"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                 <label>
                    Artículo <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required|max:100"
                    name="Artículo"
                    placeholder="No. de Artículo"
                  >
                    <b-form-input
                      v-model="form.legal_basis"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                 <label>
                    Publicar Actualización <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Publicar Actualización"
                  >
                    <v-select 
                      v-model="form.publish"
                      :options="publishOptions"
                      :reduce="e => e.id_publish"
                      label="publish"
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
              <b-col sm="12" md="12">
                <b-form-group>
                 <label>
                    Artículo <span class="text-danger">*</span>
                  </label>
                  <rich-text-edit 
                    field="legal_quote"
                    :enable-image="true"
                    placeholder="Especifica el formato del texto del artículo..."
                    :initial-content="form.legal_quote"
                    @input="getContent"
                  />
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
            class="float-right"
            variant="success"
            @click="setLegalBasi"
          >
            Registrar
          </b-button>
          <b-button v-else
            class="float-right"
            variant="success"
            @click="updateLegalBasi"
          >
            Actualizar
          </b-button>
          <b-button 
            class="float-right mr-2"
            variant="danger"
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
import { required, double } from '../../../../validations'
import { getLegalBasi, storeLegalBasi, updateLegalBasi } from '../../../../../services/legalBasiService'
import RichTextEdit from '../../../components/rich_text/RichTextEdit'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    RichTextEdit,
  },
  props: {
    isNew: {
      type: Boolean,
      required: true,
      default: true,
    },
    idGuideline: {
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
      double,
      dialog: false,
      loadDataActive: false,
      publishOptions: [
        { id_publish: 0, publish: 'No, solo guardar' },
        { id_publish: 1, publish: 'Si, publicar' }
      ],
      form: {
        legal_basis: null,
        legal_quote: null,
        publish: null,
        order: null,
      },
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Artículo ' : `Editar: ${this.register.legal_basis}`
    },
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
        this.loadDataActive = true
        const { data } = await getLegalBasi(this.idGuideline, this.register.id_legal_basis)
        const { legal_basis, legal_quote_env, publish, order } = data.data
        this.form.legal_basis = legal_basis
        this.form.publish = publish
        this.form.order = order
        this.form.legal_quote = legal_quote_env ?? ''
        setTimeout(() => {
          this.loadDataActive = false
        }, 500);
        this.showLoadingMixin()
      } catch (error) {
        this.loadDataActive = false
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setLegalBasi() {
      try {
        if (this.form.legal_quote == null || this.form.legal_quote == '') {
          this.alertMessageOk('El campo Artículo es requerido', 'warning')
          return
        }
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeLegalBasi(this.idGuideline, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateLegalBasi() {
      try {
        if (this.form.legal_quote == null || this.form.legal_quote == '') {
          this.alertMessageOk('El campo Artículo es requerido', 'warning')
          return
        }
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return 
        this.showLoadingMixin()
        const { data } = await updateLegalBasi(this.idGuideline, this.register.id_legal_basis, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getContent(field, content) {
      this.form.legal_quote = content
    },
    reset() {
      this.form.legal_basis = null
      this.form.legal_quote = ''
      this.form.publish = null
      this.form.order = null
    },
  }
}
</script>

<style>

</style>