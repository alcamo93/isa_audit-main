<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-if="isNew" variant="success" class="float-right" @click="showModal">
      Agregar
      <b-icon icon="plus" aria-hidden="true"></b-icon>
    </b-button>
    <b-button v-else v-b-tooltip.hover.left title="Editar Noticia" variant="warning" class="btn-link"
      @click="showModal">
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal 
      v-model="dialog" 
      size="xl" 
      :title="titleModal" 
      :no-close-on-backdrop="true"
      :no-enforce-focus="true"
      :no-close-on-esc="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesFormNew">
          <b-form ref="formRegister" autocomplete="off">
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Titular <span class="text-danger">*</span>
                  </label>
                  <validation-provider #default="{ errors }" rules="required|max:800" name="Titular">
                    <b-form-input v-model="form.headline"></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <modal-panel-image :record="panelImage" @successfully="setPanelImage" />
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Tema <span class="text-danger">*</span>
                  </label>
                  <b-button variant="info" @click="selectAll" size="sm">
                    <b-icon icon="list-check" aria-hidden="true"></b-icon>
                    Todos los temas
                  </b-button>
                  <validation-provider #default="{ errors }" rules="required" name="Tema">
                    <multiselect v-model="selected.topics" :options="topics" :multiple="true"
                      placeholder="Selecciona temas" label="name" track-by="id" selectLabel="Clic para seleccionar"
                      selectedLabel="Seleccionado" deselectLabel="Clic para remover">
                      <template slot="noOptions">
                        No se encontraron registros
                      </template>
                    </multiselect>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Resumen <span class="text-danger">*</span>
                  </label>
                  <rich-text-edit 
                    field="description" 
                    :enable-image="true"
                    placeholder="Especifica el formato del texto de la noticia..." 
                    :initial-content="form.description"
                    @input="getContent" />
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Fecha de inicio de publicación <span class="text-danger">*</span>
                  </label>
                  <validation-provider #default="{ errors }" rules="required" name="Fecha de inicio de publicación">
                    <vue-date-picker input-class="form-control" value-type="format"
                      v-model="form.publication_start_date"></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Fecha de cierre de publicación <span class="text-danger">*</span>
                  </label>
                  <validation-provider #default="{ errors }" rules="required" name="Fecha de cierre de publicación">
                    <vue-date-picker input-class="form-control" value-type="YYYY-MM-DD"
                      v-model="form.publication_closing_date" :disabled="!form.publication_start_date"
                      :disabled-date="disabledDateMin" :disabled-calendar-changer="disabledDateMin"></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Fecha de inicio de historial <span class="text-danger">*</span>
                  </label>
                  <validation-provider #default="{ errors }" rules="required" name="Fecha de inicio de publicación">
                    <vue-date-picker input-class="form-control" value-type="format" v-model="form.historical_start_date"
                      :disabled="!form.publication_closing_date" :disabled-date="disabledDateSH"
                      :disabled-calendar-changer="disabledDateSH"></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Fecha de cierre de historial <span class="text-danger">*</span>
                  </label>
                  <validation-provider #default="{ errors }" rules="required" name="Fecha de cierre de historial">
                    <vue-date-picker input-class="form-control" value-type="format"
                      v-model="form.historical_closing_date" :disabled="!form.historical_start_date"
                      :disabled-date="disabledDateCH" :disabled-calendar-changer="disabledDateCH"></vue-date-picker>
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
          <b-button v-if="isNew" variant="success" class="float-right" @click="setNew(0)">
            Guardar
          </b-button>
          <b-button v-else variant="success" class="float-right" @click="updateNew(0)">
            Actualizar
          </b-button>
          <b-button v-if="!form.published" variant="success" class="float-right mr-2" @click="publish">
            Publicar
          </b-button>
          <b-button variant="danger" class="float-right mr-2" @click="dialog = false">
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
import RichTextEdit from '../components/rich_text/RichTextEdit'
import { getNew, storeNew, updateNew } from '../../../services/newService'
import { getTopicsSource } from '../../../services/catalogService'
import ModalPanelImage from './ModalPanelImage'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    RichTextEdit,
    ModalPanelImage
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
      topics: [],
      selected: {
        topics: []
      },
      form: {
        headline: null,
        panel_image: null,
        description: '',
        publication_start_date: null,
        publication_closing_date: null,
        historical_start_date: null,
        historical_closing_date: null,
        published: 0,
        topics: []
      },
    }
  },
  watch: {
    'selected.topics': function (topics) {
      this.form.topics = topics.map(topic => topic.id)
    },
    'form.publication_start_date'(newValue, oldValue) {
      if (newValue !== oldValue && oldValue != null) {
        this.form.publication_closing_date = null;
        this.form.historical_start_date = null;
        this.form.historical_closing_date = null;
      }
    },
    'form.publication_closing_date'(newValue, oldValue) {
      if (newValue !== oldValue && oldValue != null) {
        this.form.historical_start_date = null;
        this.form.historical_closing_date = null;
      }
    },
    'form.historical_start_date'(newValue, oldValue) {
      if (newValue !== oldValue && oldValue != null) {
        this.form.historical_closing_date = null;
      }
    },
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Agregar Noticia ' : 'Editar: Noticia'
    },
    panelImage() {
      const empty = {
        image: null
      }
      return this.record != null ? this.record : empty
    },

  },
  methods: {
    disabledDateMin(date) {
      return date < new Date(this.form.publication_start_date)
    },
    disabledDateSH(date) {
      return date < new Date(this.form.publication_closing_date)
    },
    disabledDateCH(date) {
      return date < new Date(this.form.historical_start_date)
    },
    async showModal() {
      await this.getTopics()
      this.reset()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    setPanelImage(image) {
      this.form.panel_image = image
    },
    async getTopics() {
      try {
        const { data } = await getTopicsSource()
        this.topics = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getNew(this.register.id)
        const {
          headline,
          topics,
          description_env,
          publication_start_date,
          publication_closing_date,
          historical_start_date,
          historical_closing_date,
          published
        } = data.data
        this.form.headline = headline
        topics.map((item, index) => {
          this.selected.topics.push(item)
        })
        this.form.description = description_env ?? ''
        this.form.publication_start_date = publication_start_date
        this.form.publication_closing_date = publication_closing_date
        this.form.historical_start_date = historical_start_date
        this.form.historical_closing_date = historical_closing_date
        this.form.published = published
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setNew(publish) {
      try {
        const isValid = await this.$refs.rulesFormNew.validate()
        if (!isValid) return
        this.showLoadingMixin()
        this.form.published = publish
        const formData = new FormData()
        formData.append(`headline`, this.form.headline)
        formData.append(`file`, this.form.panel_image)
        this.form.topics.map((topic, index) => {
          formData.append(`topics[${index}]`, topic)
        })
        formData.append(`description`, this.form.description)
        formData.append(`publication_start_date`, this.form.publication_start_date)
        formData.append(`publication_closing_date`, this.form.publication_closing_date)
        formData.append(`historical_start_date`, this.form.historical_start_date)
        formData.append(`historical_closing_date`, this.form.historical_closing_date)
        formData.append(`published`, this.form.published)
        const { data } = await storeNew(formData)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateNew(publish) {
      try {
        const isValid = await this.$refs.rulesFormNew.validate()
        if (!isValid) return
        this.showLoadingMixin()
        this.form.published = publish ? publish : this.form.published
        const formData = new FormData()
        formData.append('headline', this.form.headline)
        formData.append('file', this.form.panel_image)
        this.form.topics.map((topic, index) => {
          formData.append(`topics[${index}]`, topic)
        })
        formData.append(`description`, this.form.description)
        formData.append(`publication_start_date`, this.form.publication_start_date)
        formData.append(`publication_closing_date`, this.form.publication_closing_date)
        formData.append(`historical_start_date`, this.form.historical_start_date)
        formData.append(`historical_closing_date`, this.form.historical_closing_date)
        formData.append(`published`, this.form.published)
        const { data } = await updateNew(this.register.id, formData)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    publish() {
      this.isNew ? this.setNew(1) : this.updateNew(1)
    },
    selectAll() {
      this.selected.topics = this.topics
    },
    reset() {
      this.form.headline = null
      this.form.panel_image = null,
        this.form.description = '',
        this.form.publication_start_date = null,
        this.form.publication_closing_date = null,
        this.form.historical_start_date = null,
        this.form.historical_closing_date = null,
        this.form.published = 0
      this.form.topics = []
      this.selected.topics = []
    },
    getContent(field, content) {
      this.form.description = content
    },
  }
}
</script>
<style>
.multiselect__content-wrapper {
  z-index: 99999 !important;
  position: relative;
}
</style>