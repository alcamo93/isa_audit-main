<template>
  <fragment>
    <b-button
      v-b-tooltip.hover.left 
      :title="titleTooltip"
      variant="primary"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="eye-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <loading :show="loadingMixin" />
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="12">
            <vue-doc-preview 
              :value="value" 
              type="office" 
            />
          </b-col>
        </b-row>
      </b-container>
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
import VueDocPreview from 'vue-doc-preview'
import { getContentFile } from '../../../../../services/fileService'

export default {
  components: {
    VueDocPreview
  },
  props: {
    record: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialog: false,
			value: null
    }
  },
  computed: {
    titleModal() {
      return `Evidencia/documento cargado`
    },
    titleTooltip() {
      return 'Ver Evidencia/documento cargado'
    },
  },
  methods: {
    showModal() { 
      this.getFullPath()   
      this.dialog = true
    },
    async getContentFile() {
      try {
        this.showLoadingMixin()
        const { data } = await getContentFile(this.record.id_file)
        var blob = new Blob([data], { type: 'text/plain' });
        this.value = URL.createObjectURL(blob)
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getFullPath() {
      this.value = this.record.full_path
    }
  }
}
</script>

<style>

</style>