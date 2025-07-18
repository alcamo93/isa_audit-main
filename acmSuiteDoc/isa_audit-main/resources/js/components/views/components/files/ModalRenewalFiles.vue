<template>
  <fragment v-if="allowRenewal">
    <b-button 
      variant="info"
      @click="showModal"
    >
      <b-icon icon="calendar2-check-fill" aria-hidden="true"></b-icon>
      {{ titleBtnModal }}
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
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
              <b-col sm="12" md="12">
                <b-form-group>
                  <span class="font-weight-bold">Requerimiento: </span>
                  {{ requirementName }}
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group>
                  <span class="font-weight-bold">Materia: </span>
                  {{ matterName }}
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <span class="font-weight-bold">Aspecto: </span>
                  {{ aspectName }}
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <span class="font-weight-bold">Evidencia: </span>
                  {{ evidenceName }}
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group>
                  <span class="font-weight-bold">Nombre: </span>
                  {{ name }}
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <span class="font-weight-bold">Clasificación: </span>
                  {{ category }}
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Fecha de Expedición
                    <span 
                      class="text-danger"
                    >*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Fecha de Expedición"
                  >
                    <vue-date-picker 
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      v-model="form.init_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Fecha de Vigencia
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Fecha de Vigencia"
                  >
                    <vue-date-picker 
                      :disabled="form.init_date == null"
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      :disabled-date="disabledDateMin"
                      :disabled-calendar-changer="disabledDateMin"
                      v-model="form.end_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Días para notificar a Responsable
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    :rules="`required|integer|min_value:1|max_value:${calculateDays}`"
                    name="Días para notificar"
                  >
                    <b-form-input
                      v-model="form.days"
                      type="number"
                      min="1"
                      :max="calculateDays"
                      :disabled="form.end_date == null"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <upload-files
              :is-new="true"
              ref="refUploadFiles"
              title="Renovación de Documento/Evidencia"
              :show-required="true"
              @getFiles="reloadUploadFiles"
            />
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button
            variant="success"
            class="float-right"
            @click="renewalFile"
          >
            Renovar
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
import { required, min_value, max_value, integer } from '../../../validations'
import UploadFiles from './managers/UploadFiles'
import ToolbarFile from './managers/ToolbarFile'
import { getLibrary, renewalLibrary } from '../../../../services/libraryService'
import { diffDays } from '../../components/scripts/dates'
import { getNoRequirementText, getRequirementText, getFieldRequirement } from '../../components/scripts/texts'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    UploadFiles,
    ToolbarFile,
  },
  props: {
    showLibrary: {
      type: Boolean,
      required: false,
      default: true,
    },
    origin: {
      type: String,
      required: false,
      validator: value => {
        const types = ['Obligation', 'Task', 'Library']
        return types.indexOf(value) !== -1
      }
    },
    parentRecord: {
      type: Object,
      required: true,
    },
    permissions: {
      type: Object,
      required: false,
      default: function () {
        return {
          can_approve: true,
          can_upload: true
        }
      }
    }
  },
  data() {
    return {
      dialog: false,
      required,
      min_value,
      max_value,
      integer,
      name: '',
      category: '',
      filesData: [],
      form: {
        library_id: null,
        init_date: null, 
        end_date: null, 
        days: null,
        file: [],
      }
    }
  },
  computed: {
    titleModal() {
      return 'Renovación de Evidencia/Documentos'
    },
    titleBtnModal() {
      return 'Renovar'
    },
    requirement() {
      return this.parentRecord
    },
    requirementName() {
      if (this.requirement == null) return ''
      return `${getNoRequirementText(this.requirement)}. ${getRequirementText(this.requirement)}`
    },
    matterName() {
      if (this.requirement == null) return ''
      return getFieldRequirement(this.requirement, 'matter')
    },
    aspectName() {
      if (this.requirement == null) return ''
      return getFieldRequirement(this.requirement, 'aspect')
    },
    evidenceName() {
      if (this.requirement == null) return ''
      return getFieldRequirement(this.requirement, 'document')
    },
    calculateDays() {
      const { init_date, end_date } = this.form
      return diffDays(init_date, end_date)
    },
    allowRenewal() {
      const { for_review, need_renewal } = this.parentRecord.library
      const allow = !for_review && need_renewal
      return allow
    }
  },
  watch: {
    'form.init_date': function(value) {
      if (value != null) return
      this.form.end_date = null
      this.form.days = null
    },
    'form.end_date': function(value) {
      if (value != null) return
      this.form.days = null
    },
  },
  methods: {
    async showModal() {
      await this.loadDataFile()
      this.dialog = true
    },
    reloadUploadFiles(files) {
      this.filesData = files
    },
    disabledDateMin(date) {
      return date < new Date(this.form.init_date)
    },
    async loadDataFile() {
      try {
        this.showLoadingMixin()
        const { id } = this.parentRecord.library
        const { data } = await getLibrary(id)
        const { name,  category } = data.data
        this.form.library_id = id
        this.name = name
        this.category = category.category
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async renewalFile() {
      try {
        // miss data form
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        // miss files upload
        const { isValidFiles, message } = this.$refs.refUploadFiles.validateFiles()
        if (!isValidFiles) {
          this.alertMessageOk(message, 'error')
          return
        }
        this.showLoadingMixin()
        const formData = new FormData()
        formData.append('init_date', this.form.init_date)
        formData.append('end_date', this.form.end_date)
        formData.append('days', this.form.days)
        this.filesData.map((file, index) => {
          formData.append(`file[${index}]`, file)
        })
        const { data } = await renewalLibrary(this.form.library_id, formData)
        this.$emit('successfully')
        this.showLoadingMixin()
        this.dialog = false
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    successfullyReload() {
      this.loadDataFile()
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>