<template>
    <!-- <b-row v-show="showList"> -->
    <b-row>
      <b-col sm="12" md="12">
        <p class="text-light bg-dark text-center mb-2">
          {{ title }}
        </p>
        <b-table-simple striped hover small responsive>
          <b-thead>
            <b-tr>
              <b-th class="text-center">Nombre de documento</b-th>
              <b-th class="text-center">Tamaño</b-th>
              <b-th class="text-center">Acciones</b-th>
            </b-tr>
          </b-thead>
          <b-tbody v-if="files.length">
            <b-tr v-for="file in files" :key="file.id">
              <b-td>
                {{ file.original_name }}
              </b-td>
              <b-td 
                v-b-tooltip.hover.left
                :title="file.hash_name"
                class="text-center"
              >
                {{ file.file_size_human }}
              </b-td>
              <b-td class="text-center td-actions">
                <b-button 
                  v-show="allowDelete"
                  v-b-tooltip.hover.left
                  title="Eliminar Evidencia"
                  variant="danger"
                  class="btn-link"
                  @click="alertRemove(file)"
                >
                  <b-icon icon="trash" aria-hidden="true"></b-icon>
                </b-button>
                <viewer-buttons-image
                  :file-record="file"
                />
              </b-td>
            </b-tr>
          </b-tbody>
          <b-tbody v-else>
            <b-tr>
              <b-td colspan="5" class="text-center">
                Sin evidencias cargadas
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </b-col>
    </b-row>
  </template>
  
  <script>
  import ViewerButtonsImage from './ViewerButtonsImage'
  import { deleteImage } from '../../../../../../services/imageService'
  
  export default {
    components: {
      ViewerButtonsImage,
    },
    props: {
      isNew: {
        type: Boolean,
        required: true,
      },
      title: {
        type: String,
        required: true
      },
      files: {
        type: Array,
        required: true,
        defalut: []
      },
      allowDelete: {
        type: Boolean,
        required: false,
        default: true
      }
    },
    data() {
      return {
        remove_files: []
      }
    },
    computed: {
      showList() {
        return !this.isNew
      },
    },
    methods: {
      validateFiles() {
        const data = {
          isValidFiles: true,
          message: 'OK'
        }
        const hasFiles = this.files.length
        if (!hasFiles) {
          data.isValidFiles = false,
          data.message = 'El requerimiento siempre debe tener Evidencia/Documentos'
        }
        return data
      },
      async alertRemove({ id, original_name }) {
      try {
          const question = `¿Estás seguro de eliminar la evidencia: '${original_name}'?`
          const { value } = await this.alertDeleteMixin(question)
          if (value) {
            this.showLoadingMixin()
            await this.removeFile(id)
            this.showLoadingMixin()
          }
        } catch (error) {
          this.showLoadingMixin()
          this.responseMixin(error)
        }
      },
      async removeFile(id) {
        try {
          this.showLoadingMixin()
          const files = this.files.filter(item => item.id != id)
          const removeFile = this.files.find(item =>  item.id == id)
          this.remove_files.push(removeFile)
          this.$emit('getRemoveFiles', this.remove_files)
          const { data } = await deleteImage(id)
          this.$emit('getFiles', files)
          this.showLoadingMixin()
          this.responseMixin(data)
        } catch (error) {
          this.showLoadingMixin()
          this.responseMixin(error)
        }
      }
    }
  }
  </script>
  
  <style>
  
  </style>