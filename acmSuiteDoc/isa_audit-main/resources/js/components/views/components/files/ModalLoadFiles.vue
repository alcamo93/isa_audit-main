<template>
  <fragment v-if="permissions.can_upload">
    <loading :show="loadingMixin" />
    <b-button
      v-if="isNew" 
      variant="success"
      v-b-tooltip.hover.left
      title="Agregar Evidencia/documento"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="cloud-arrow-up-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-button 
      v-else
      variant="primary"
      @click="showModal"
    >
      <b-icon icon="file-earmark-pdf" aria-hidden="true"></b-icon>
      Editar
    </b-button>
    <b-modal
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
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
              <b-col sm="12" md="6">
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
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Clasificación <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Clasificación"
                  >
                    <v-select 
                      v-model="form.id_category"
                      :options="categories"
                      :reduce="e => e.id_category"
                      label="category"
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
              <b-col sm="12" md="6">
                <b-form-group>
                  <b-form-checkbox 
                    class="mt-4"
                    v-model="form.has_end_date" 
                    :value="1"
                    :unchecked-value="0"
                    switch size="lg"
                  >
                  {{ labelCheckDates }}
                  </b-form-checkbox>
                </b-form-group>
              </b-col>
              <b-col v-show="showHasFile" sm="12" md="6">
                <no-file-register 
                  :parent-record="parentRecord"
                  :evaluateable-id="evaluateableId"
                  @successfully="successfullyNoFile"
                />
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
                      @change="resetDates"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Fecha de Vigencia
                    <span 
                      v-show="requiredDates"
                      class="text-danger"
                    >*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    :rules="requiredDates ? 'required' : ''"
                    name="Fecha de Vigencia"
                  >
                    <vue-date-picker 
                      :disabled="disabledEndDate"
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      :disabled-date="disabledDateMin"
                      :disabled-calendar-changer="disabledDateMin"
                      v-model="form.end_date"
                      @change="resetDates"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group>
                  <label>
                    Días para notificar a Responsable
                    <span
                      v-show="requiredDates"
                      class="text-danger"
                    >*</span>
                  </label>
                  <validation-provider
                    ref="validationProvider"
                    #default="{ errors }"
                    :rules="requiredDates ? `required|integer|min_value:1|max_value:${calculateDays}` : ''"
                    name="Días para notificar"
                  >
                    <b-form-input
                      v-model="form.days"
                      type="number"
                      min="1"
                      :max="calculateDays"
                      :disabled="disabledDays"
                    ></b-form-input>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col v-if="form.days" sm="12" md="12">
                <b-form-group>
                  <label>
                    Fechas para recordatorio
                    <span
                      v-show="requiredDates"
                      class="text-danger"
                    >*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    :rules="requiredDates ? 'required' : ''"
                    name="Fechas para recordatorio"
                  >
                    <vue-date-picker 
                      :disabled="!form.days || !form.enableDates"
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      :disabled-date="disabledDates"
                      v-model="form.notify_days"
                      multiple
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <upload-files
              :is-new="isNew"
              ref="refUploadFiles"
              title="Documento/Evidencia"
              :show-required="true"
              @getFiles="reloadUploadFiles"
            />
            <list-files
              :is-new="isNew"
              ref="refListFiles"
              title="Documento/Evidencia Cargados"
              :files="filesServer"
              @getRemoveFiles="reloadListRemoveFiles"
              @getFiles="reloadListFiles"
            />
            <b-row v-if="shareRecord.length">
              <b-col sm="12" md="12">
                <p class="text-light bg-dark text-center">
                  La evidencia documental se establecerá como copia en las siguientes secciones:
                </p>
              </b-col>
              <b-col v-for="record in shareRecord" :key="record.librariable_id"
                sm="12" 
                md="12"
              >
                <b-form-group>
                  <h4>{{ record.section }}</h4>
                  <span class="font-weight-bold">{{ requirementName }}</span>
                  <br>
                  Seguimiento de
                  <span class="font-weight-bold">{{ record.init_date_format }}</span>
                  hasta
                  <span class="font-weight-bold">{{ record.end_date_format }}</span>
                  <br>
                  <b-badge v-if="record.current" variant="primary">
                    Estás viendo evidencias desde aquí
                  </b-badge>
                  <hr>
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
            v-if="isNew"
            variant="success"
            class="float-right"
            @click="setFile"
          >
            Registrar
          </b-button>
          <b-button
            v-else
            variant="success"
            class="float-right"
            @click="updateFile"
          >
            Editar
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
import UploadFiles from './managers/UploadFiles.vue'
import ListFiles from './managers/ListFiles.vue'
import NoFileRegister from './NoFileRegister.vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, min_value, max_value, integer } from '../../../validations'
import { getCategories } from '../../../../services/catalogService.js'
import { getShareByAplicabilityRegister } from '../../../../services/AplicabilityRegisterService.js'
import { storeLibrary, updateLibrary, getLibrary } from '../../../../services/libraryService.js'
import { diffDays, subtractDays } from '../../components/scripts/dates.js'
import { getNoRequirementText, getRequirementText, getFieldRequirement } from '../../components/scripts/texts.js'

export default {
  components: {
    UploadFiles,
    ListFiles,
    NoFileRegister,
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    isNew: {
      type: Boolean,
      required: false,
      default: true
    },
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
    },
    evaluateableId: {
      type: Number,
      required: false,
      default: 0
    },
  },
  data() {
    return {
      required,
      min_value,
      max_value,
      integer,
      dialog: false,
      categories: [],
      shareRecord: [],
      filesData: [],
      filesServer: [],
      filesRemoveServer: [],
      library_id: null,
      form: {
        name: '',
        has_end_date: 1,
        init_date: null, 
        end_date: null, 
        days: null,
        has_file: 1,
        id_category: null,
        file: [],
        notify_days: [],
        date_before_limit: null,
        enableDates: false
      }
    }
  },
  computed: {
    titleModal() {
      return this.isNew ? 'Cargar Evidencia/documento' : 'Editar Evidencia/documento'
    },
    calculateDays() {
      const { init_date, end_date, days } = this.form
      this.form.date_before_limit = subtractDays(days, end_date)
      return diffDays(init_date, end_date)
    },
    labelCheckDates() {
      return `${(this.form.has_end_date ? 'Si,' : 'No, no')} necesito agregar fechas de vigencia`
    },
    labelCheckFile() {
      return `${(this.form.has_file ? 'Si,' : 'No, no')} cuento con el documento para agregarlo`
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
    requiredDates() {
      if (this.requirement == null) return false
      const required = this.form.has_end_date == 1
      return required
    },
    disabledEndDate() {
      if (this.requiredDates) {
        return this.form.init_date == null
      }
      else {
        this.form.end_date = null
        return true
      }
    },
    disabledDays() {
      if (this.requiredDates) {
        return this.form.end_date == null
      } else {
        this.form.days = null
        return true
      }
    },
    showHasFile() {
      return this.origin == 'Obligation' ? true : false
    },
    evaluatable() {
      const type = {
        'Task': this.parentRecord.id_task,
        'Obligation': this.parentRecord.id_obligation,
        'default': null,
      }
      return {
        evaluatable_id: (type[this.origin] || type['default']),
        evaluatable_type: this.origin
      }
    }
  },
  watch: {
    'form.has_date': function(val) {
      if (val != 0) return
      this.form.init_date = null
      this.form.end_date = null
    },
    'form.has_file': function(val) {
      if (val != 0) return
      this.form.file = []
    },
    'form.days': function(newValue) {
      this.form.enableDates = this.form.days != null ? true : false 
      this.$nextTick(() => {
        if (this.$refs.validationProvider) {
          this.$refs.validationProvider.validate().then(({ valid }) => {
            this.onDaysChange(valid);
          }).catch((error) => {
            console.error(error);
          });
        }
      });
    }
  },
  methods: {
    async showModal() {
      this.resetForm()
      if ( !this.isNew ) {
        await this.getRecord()
      }
      if (this.showLibrary) {
        await this.getShareByAplicabilityRegister()
      }
      await this.getCategories()
      this.dialog = true
    },
    disabledDateMin(date) {
      return date < new Date(this.form.init_date)
    },
    disabledDates(date) {
      return date < new Date(this.form.date_before_limit) || date > new Date(this.form.end_date)
    },
    async getRecord() {
      try {
        this.showLoadingMixin()
        const { id } = this.parentRecord.library
        const { data } = await getLibrary(id)
        const { name, init_date, end_date, days, has_end_date, id_category, files, for_review, files_notifications } = data.data
        this.for_review = Boolean(for_review)
        this.library_id = id
        this.form.name = name
        this.form.has_end_date = has_end_date
        this.form.init_date = init_date
        this.form.end_date = end_date
        this.form.days = days
        this.form.notify_days = this.getNotifyDates(files_notifications)
        this.form.id_category = id_category
        this.filesServer = files
        this.showLoadingMixin()
      } catch (error) {
        console.log(error)
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getShareByAplicabilityRegister() {
      try {
        this.showLoadingMixin()
        const { id_requirement, id_subrequirement, id_audit_process, id_aplicability_register } = this.requirement
        const filters = { id_requirement, id_subrequirement }
        const { data } = await getShareByAplicabilityRegister(id_audit_process, id_aplicability_register, {}, filters)
        this.shareRecord = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getCategories() {
      try {
        this.showLoadingMixin()
        const { data } = await getCategories()
        this.categories = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reloadUploadFiles(files) {
      this.filesData = files
    },
    reloadListFiles(files) {
      this.filesServer = files
    },
    reloadListRemoveFiles(files) {
      const arrayIds = files.map(item => item.id)
      this.filesRemoveServer = arrayIds
    },
    async setFile() {
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
        formData.append('id_aplicability_register', this.parentRecord.id_aplicability_register)
        formData.append('id_requirement', this.parentRecord.id_requirement)
        formData.append('id_subrequirement', this.parentRecord.id_subrequirement) 
        formData.append('show_library', this.showLibrary)
        formData.append('name', this.form.name)
        formData.append('has_end_date', this.form.has_end_date)
        formData.append('init_date', this.form.init_date)
        if ( this.form.has_end_date == 1 ) {
          formData.append('end_date', this.form.end_date)
          formData.append('days', this.form.days)
          this.form.notify_days.forEach(function (item, index) {
            formData.append(`notify_days[${index}]`, item)
          })
        }
        formData.append('id_category', this.form.id_category)
        formData.append('evaluateable_id', this.evaluateableId)
        formData.append('evaluateable_type', this.origin)
        formData.append('id_task', this.parentRecord.id_task)
        this.filesData.map((file, index) => {
          formData.append(`file[${index}]`, file)
        })
        const { data } = await storeLibrary(formData)
        this.$emit('successfully')
        this.showLoadingMixin()
        this.dialog = false
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async updateFile() {
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
        formData.append('show_library', this.showLibrary)
        formData.append('name', this.form.name)
        formData.append('has_end_date', this.form.has_end_date)
        formData.append('init_date', this.form.init_date)
        if ( this.form.has_end_date == 1 ) {
          formData.append('end_date', this.form.end_date)
          formData.append('days', this.form.days)
          this.form.notify_days.forEach(function (item, index) {
            formData.append(`notify_days[${index}]`, item)
          })
        }
        formData.append('id_category', this.form.id_category)
        formData.append('remove_files', this.filesRemoveServer)
        formData.append('id_task', this.parentRecord.id_task)
        this.filesData.map((file, index) => {
          formData.append(`file[${index}]`, file)
        })
        const { data } = await updateLibrary(this.library_id, formData)
        this.$emit('successfully')
        this.showLoadingMixin()
        this.dialog = false
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    successfullyNoFile() {
      this.dialog = false
      this.$emit('successfully')
    },
    resetForm() {
      this.shareRecord = []
      this.filesData = []
      this.form.name = ''
      this.form.has_end_date = 1
      this.form.init_date = null 
      this.form.end_date = null 
      this.form.days = null
      this.form.has_file = 1
      this.form.id_category = null
      this.form.file = []
      this.filesServer = []
      this.filesRemoveServer = []
    },
    getNotifyDates(arrayDates) {
      const dates = arrayDates.map(item => item.date)
      return dates
    },
    onDaysChange(valid) {
      if (!valid) {
        this.form.enableDates = false
        this.form.notify_days = []
      } else {
        this.form.notify_days = []
        this.form.enableDates = true
      }
    },
    resetDates(){
      this.form.days = null
      this.form.notify_days = []
    }
  }
}
</script>

<style>
  
</style>