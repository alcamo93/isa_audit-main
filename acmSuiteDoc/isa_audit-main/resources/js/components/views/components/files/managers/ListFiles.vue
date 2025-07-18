<template>
  <b-row v-show="showList">
    <b-col sm="12" md="12">
      <p class="text-light bg-dark text-center mb-2">
        {{ title }}
      </p>
      <b-table-simple striped hover small responsive>
        <b-thead>
          <b-tr>
            <b-th class="text-center">Nombre de documento</b-th>
            <b-th class="text-center">Fecha de vencimiento</b-th>
            <b-th class="text-center">Registro</b-th>
            <b-th class="text-center">Tamaño</b-th>
            <b-th class="text-center">Acciones</b-th>
          </b-tr>
        </b-thead>
        <b-tbody v-if="files.length">
          <b-tr v-for="file in files" :key="file.id">
            <b-td>
              {{ file.original_name }}
            </b-td>
            <b-td class="text-center">
              {{ (file.end_date != null ? file.end_date_format : 'NA') }}
            </b-td>
            <b-td class="text-center">
              <b-badge v-if="Boolean(!file.is_current)" pill variant="warning">
                {{ file.is_current_text }}
              </b-badge>
              <span v-else>{{ file.is_current_text }}</span>
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
                v-show="allowDelete && Boolean(file.is_current)"
                v-b-tooltip.hover.left
                title="Eliminar Evidencia/Documento"
                variant="danger"
                class="btn-link"
                @click="removeFile(file.id)"
              >
                <b-icon icon="trash" aria-hidden="true"></b-icon>
              </b-button>
              <viewer-buttons-file
                :file-record="file"
              />
            </b-td>
          </b-tr>
        </b-tbody>
        <b-tbody v-else>
          <b-tr>
            <b-td colspan="5" class="text-center">
              Sin documentos cargados
            </b-td>
          </b-tr>
        </b-tbody>
      </b-table-simple>
    </b-col>
  </b-row>
</template>

<script>
import ViewerButtonsFile from '../viewers/ViewerButtonsFile'

export default {
  components: {
    ViewerButtonsFile,
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
    removeFile(id) {
      const removeFile = this.files.find(item =>  item.id == id)
      this.remove_files.push(removeFile)
      this.$emit('getRemoveFiles', this.remove_files)
      const files = this.files.filter(item => item.id != id)
      this.$emit('getFiles', files)
    }
  }
}
</script>

<style>

</style>