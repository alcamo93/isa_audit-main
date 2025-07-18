<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button v-if="hasFile"
      variant="danger"
      v-b-tooltip.hover.left
      :title="titleBtnModal"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="file-earmark-pdf" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal v-if="hasFile"
      v-model="dialog"
      size="xl"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <toolbar-file
          :has-file="hasFile"
          :show-library="showLibrary"
          :origin="origin"
          :parent-record="parentRecord"      
          :permissions="permissions"
          :for_review="for_review"
          @successfully="successfullyReload"
        />
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
              <span class="font-weight-bold">Fecha de Expedición: </span>
              {{ init_date }}
            </b-form-group>
          </b-col>
          <b-col sm="12" md="4">
            <b-form-group>
              <span class="font-weight-bold">Fecha de Vigencia: </span>
              {{ end_date }}
            </b-form-group>
          </b-col>
          <b-col sm="12" md="4">
            <b-form-group>
              <span class="font-weight-bold">Días para notificar a Responsable: </span>
              {{ days }}
            </b-form-group>
          </b-col>
          <b-col v-if="days != 'NA'" sm="12" md="12" >
            <b-form-group>
              <span class="font-weight-bold">Fechas para recordatorio: </span>
              <modal-days-notify
                :title="name"
                :dates = "dates"
              />
            </b-form-group>
          </b-col>
        </b-row>
        <list-files
          :is-new="false"
          :allow-delete="false"
          title="Documento/Evidencia Cargados"
          :files="filesServer"
        />
      </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import ListFiles from './managers/ListFiles.vue'
import ToolbarFile from './managers/ToolbarFile.vue'
import ModalDaysNotify from './ModalDaysNotify.vue'
import { getLibrary } from '../../../../services/libraryService'
import { getNoRequirementText, getRequirementText, getFieldRequirement } from '../../components/scripts/texts'

export default {
  components: {
    ListFiles,
    ToolbarFile,
    ModalDaysNotify,
  },
  props: {
    hasFile: {
      type: Boolean,
      required: true,
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
    }
  },
  data() {
    return {
      dialog: false,
      parent_status: null,
      for_review: false,
      name: '',
      has_end_date: '',
      init_date: '',
      end_date: '',
      days: '',
      dates: [],
      has_file: '',
      category: '',
      filesServer: [],
      dialogViewDays: false,
    }
  },
  computed: {
    titleModal() {
      return 'Evidencia/Documentos Cargados'
    },
    titleBtnModal() {
      return 'Mostrar Evidencia/Documentos Cargados'
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
  },
  methods: {
    async showModal() {
      await this.loadDataFile()
      this.dialog = true
    },
    async loadDataFile() {
      try {
        this.showLoadingMixin()
        const { id } = this.parentRecord.library
        const { data } = await getLibrary(id)
        const { name, init_date_format, end_date_format, days, category, files, for_review, has_end_date, files_notifications } = data.data
        this.for_review = Boolean(for_review)
        this.name = name
        this.init_date = init_date_format
        this.end_date = Boolean(has_end_date) ? end_date_format : 'NA'
        this.days = Boolean(has_end_date) ? days : 'NA'
        this.dates = files_notifications
        this.category = category.category
        this.filesServer = files
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    successfullyReload() {
      this.loadDataFile()
      this.$emit('successfully')

    },
  }
}
</script>

<style>

</style>