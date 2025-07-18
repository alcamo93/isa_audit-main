<template>
  <fragment>
    <loading :show="loadingMixin" />
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
      <b-container fluid>
        <b-row v-if="numPages" class="mb-2 row d-flex justify-content-center">
          <b-col sm="3" md="3">
            <b-input-group 
              :append="`/ ${numPages}`"
            >
              <b-form-input
                v-model.number="page"
                type="number"
                min="1"
                :max="numPages"
              ></b-form-input>
            </b-input-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12" md="12">
            <pdf 
              ref="pdf" 
              style="border: 1px solid black" 
              :src="src" 
              :page="page" 
              @password="checkPassword"
              @error="checkError" 
              @num-pages="numPages = $event" 
              @link-clicked="page = $event"
            ></pdf>
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
import pdf from 'vue-pdf'
import { getContentFileBase } from '../../../../../services/fileService'

export default {
  components: {
    pdf
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
			page: 1,
			numPages: 0,
      src: '',
    }
  },
  computed: {
    titleModal() {
      return `Evidencia/documento cargado`
    },
    titleTooltip() {
      return 'Ver Evidencia/documento cargado'
    },
    fullPathComputed() {
      if (this.record == null) return ''
      return ''
    }
  },
  methods: {
    async showModal() { 
      await this.getFile()
      this.dialog = true
    },
    checkPassword(event) {
      console.log(event)
    },
    checkError(event) {
      console.log(event)
    },
    async getFile() {
      try {
        this.showLoadingMixin()
        const { data } = await getContentFileBase(this.record.id)
        this.src = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>