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
            <b-row class="mb-2 row d-flex justify-content-center">
              <h5>Clic sobre la imagen para más detalles</h5>
            </b-row>
            <viewer class="viewer" ref="viewer" :images="images">
              <b-img  v-for="src in images" :key="src" :src="src"
                class="pointerClass selectImage"
                center rounded thumbnail fluid
              ></b-img>
            </viewer>
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
import 'viewerjs/dist/viewer.css'
import { component as Viewer } from 'v-viewer'

export default {
  components: {
    Viewer
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
    }
  },
  computed: {
    titleModal() {
      return `Evidencia/documento cargado`
    },
    titleTooltip() {
      return 'Ver Evidencia/documento cargado'
    },
    images() {
      if (this.record == null) return []
      return [this.record.full_path]
    }
  },
  methods: {
    async showModal() { 
      try {
        this.showLoadingMixin()
        this.showLoadingMixin()
        this.dialog = true
      } catch (error) {
        this.showLoadingMixin()
      }
    },
  }
}
</script>

<style scoped>
  .pointerClass {
    cursor: pointer;
  }
  .selectImage:hover {
    border: 2px solid #343a40;
  }
</style>