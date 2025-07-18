<template>
  <b-row>
    <b-col sm="12" md="12">
      <div v-show="!addFilesEdit && showButtonInEdit">
        <b-button 
          block variant="success"
          @click="addFilesEdit = true"
        >
          Agregar Documento/Evidencia
          <b-icon icon="plus" aria-hidden="true"></b-icon>
        </b-button>
      </div>
      <div v-if="addFilesEdit">
        <p class="text-light bg-dark text-center">
          {{ title }}
          <span 
            v-show="showRequired"
            class="text-danger"
          >*</span>
        </p>
        <dropzone
          class="mb-2"
          ref="refDropzone"
          id="dropzoneLoadFile"
          :options="dropzoneOptions"
          @vdropzone-file-added="onFileAdd"
          @vdropzone-removed-file="onFileRemove"
        />
      </div>
      <b-button
        v-if="hasNoAcceptedFiles"
        class="mb-2"
        variant="info"
        block
        @click="removeNoAcceptedFiles"
      >
        Remover archivos no permitidos
      </b-button>
      <b-button 
        block variant="danger"
        v-show="addFilesEdit"
        @click="closeLoadFile"
      >
        Cancelar
      </b-button>
    </b-col>
  </b-row>
</template>

<script>
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import Dropzone from 'vue2-dropzone'

export default {
  components: {
    Dropzone,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    showRequired: {
      type: Boolean,
      required: false,
      default: false,
    },
    isNew: {
      type: Boolean,
      required: false,
      default: true,
    }
  },
  data() {
    return {
      addFilesEdit: false,
      filesData: [],
    }
  },
  watch: {
    isNew(value) {
      this.onFileReset()
      if (value) {
        this.$refs.refDropzone.disable()
      } else {
        this.$refs.refDropzone.enable()
      }
    }
  },
  computed: {
    showButtonInEdit() {
      return this.isNew == false
    },
    hasNoAcceptedFiles() {
      const thereAreNoAcceptedFile = this.filesData.some(item => !item.accepted)
      return thereAreNoAcceptedFile
    },
    dropzoneOptions() {
      return {
        url: '/v2/file',
        paramName: 'file',
        maxFilesize: 15,
        acceptedFiles: '.png, .jpg, .jpeg, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .pdf',
        dictDefaultMessage: 'Clic para agregar archivos o arrastre a esta área',
        dictFallbackMessage: 'Su navegador no admite la carga de archivos mediante arrastrar y soltar.',
        dictFallbackText: 'Por favor, utiliza el formulario fallback de abajo para subir tus archivos como en los viejos tiempos.',
        dictFileTooBig: 'El archivo es muy grande ({{filesize}}MiB). Tamaño máximo: {{maxFilesize}}MB.',
        dictInvalidFileType: 'No puedes subir archivos de este tipo.',
        dictResponseError: 'El servidor ha respondido con el código {{statusCode}}.',
        dictCancelUpload: 'Cancelar subida',
        dictRemoveFile: 'Remover archivo',
        autoProcessQueue: false,
        addRemoveLinks: true,
      }
    },
  },
  methods: {
    removeNoAcceptedFiles() {
      const noAccepted = this.$refs.refDropzone.getRejectedFiles()
      noAccepted.forEach(item => {
        this.$refs.refDropzone.removeFile(item)
      });
      this.onFileRemove()
    },
    validateFiles() {
      const data = {
        isValidFiles: true,
        message: 'OK'
      }
      
      if (!this.disabledZone && this.filesData.length == 0 ) {
        data.isValidFiles = false,
        data.message = 'Es necesario agregar archivos'
        return data
      }
      
      if (!this.disabledZone && this.hasNoAcceptedFiles ) {
        data.isValidFiles = false,
        data.message = 'Cuentas con archivos no validos, para continuar remueve de la lista'
        return data
      }

      return data
    },
    onFileRemove() {
      this.filesData = []
      const currentFiles = this.$refs.refDropzone.getAcceptedFiles()
      currentFiles.forEach(file => this.filesData.push(file))
      this.$emit('getFiles', this.filesData)
    },
    onFileAdd(file) {
      this.filesData.push(file)
      this.$emit('getFiles', this.filesData)
    },
    onFileReset() {
      this.filesData = []
      this.$refs.refDropzone.removeAllFiles(true)
    },
    closeLoadFile() {
      this.onFileReset()
      this.addFilesEdit = false
      this.$emit('getFiles', this.filesData)
    }
  }
}
</script>

<style scoped>
  .dropzone .dz-preview .dz-error-message {
    top: 60px !important;
  }
  .dropzone .dz-preview {
    margin: 5px !important;
  }
  .dropzone {
    padding: 5px 5px !important;
  }
  .vue-dropzone>.dz-preview .dz-details {
    background-color: #113c53d1
  }
  .dz-default.dz-message span i {
    font-size: -webkit-xxx-large !important;
  }
</style>