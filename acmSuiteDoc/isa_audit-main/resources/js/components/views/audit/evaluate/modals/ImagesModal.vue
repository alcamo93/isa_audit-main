<template>
  <fragment>
    <loading :show="loadingMixin" />
      <b-button 
        class="btn-link"
        v-b-tooltip.hover.left 
        :title="titleTooltip"
        variant="info"
        @click="showModal"
      >
        <b-icon icon="images" aria-hidden="true"></b-icon>
      </b-button>
      <b-modal
        v-model="dialog"
        size="xl"
        :title="titleModal"
        no-close-on-backdrop
      >
      <upload-images
        :is-new="isNew"
        :params="params"
        :record="record"
        ref="refUploadFiles"
        title="Evidencia"
        show-required
        @getRecord="getRecord"
      />
      <list-images
        :is-new="isNew"
        ref="refListFiles"
        title="Evidencias Cargadas"
        :files="filesServer"
        @getRemoveFiles="reloadListRemoveFiles"
        @getFiles="reloadListFiles"
      />
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import UploadImages from './images/UploadImages.vue'
import ListImages from './images/ListImages.vue'
import { getNoRequirementText } from '../../../components/scripts/texts'
import { getAudit } from '../../../../../services/AuditService'

export default {
  components: {
    UploadImages,
    ListImages
  },
  props: {
    params: {
      type: Object,
      required: true
    },
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      isNew: true,
      filesData: [],
      filesServer: [],
      filesRemoveServer: []
    }
  },
  computed: {
    isRequirement() {
      return this.record.subrequirement == null ? true : false
    },
    idRecord() {
      return this.record.subrequirement == null 
        ? this.record.requirement.id_requirement : this.record.subrequirement.id_subrequirement
    },
    getNameRecord() {
      const type = this.isRequirement ? 'requerimiento' : 'subrequerimiento'
      return `${type} ${getNoRequirementText(this.record)}`
    },
    titleModal() {
      if (this.record == null) return ''
      return `Evidencias de ${this.getNameRecord}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Ver evidencias de ${this.getNameRecord}`
    },
  },
  methods: {
    async showModal() {
      await this.getRecord()
    },
    async getRecord() {
      try {
        this.showLoadingMixin()
        const { idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect } = this.params
        const { data } = await getAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, this.record.id)
        const { audit } = data.data
        // default data
        const answer = audit.answer
        if(answer != 0) {
          this.showLoadingMixin()
          this.alertMessageOk('Solo se puede agregar evidencias cuando la respueta es No Cumple', 'error')
          return
        }
        const images = audit.images
        this.filesServer = images
        this.dialog = true
        this.showLoadingMixin()
      } catch (error) {  
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reloadListFiles(files) {
      this.filesServer = files
    },
    reloadListRemoveFiles(files) {
      const arrayIds = files.map(item => item.id)
      this.filesRemoveServer = arrayIds
    },
  }
}
</script>

<style scoped>
.btn-link {
  padding: 3px;
}
</style>