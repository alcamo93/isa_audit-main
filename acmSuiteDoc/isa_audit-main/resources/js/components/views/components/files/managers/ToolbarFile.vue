<template>
  <fragment>
    <loading :show="loadingMixin" />
    <!-- show toolbar complete -->
    <b-button-toolbar v-if="showApproveButton"
      class="d-flex justify-content-center" 
      key-nav aria-label="Toolbar with button files"
    >
      <b-button-group class="mx-1">
        <modal-load-files
          v-if="userCanUploadFile"
          :is-new="false"
          :show-library="showLibrary"
          :origin="origin"
          :parent-record="parentRecord"
          :permissions="permissions"
          @successfully="successfullyReload"
        />
        <modal-renewal-files 
          :show-library="showLibrary"
          :origin="origin"
          :parent-record="parentRecord"
          :permissions="permissions"
          @successfully="successfullyReload"
        />
        <b-button v-if="userCanApprove"
          variant="success"
          @click="approveFile(true)"
        >
          <b-icon icon="check-lg" aria-hidden="true"></b-icon>
          Aprobar
        </b-button>
        <b-button v-if="userCanApprove"
          variant="danger"
          @click="approveFile(false)"
        >
          <b-icon icon="x-lg" aria-hidden="true"></b-icon>
          Rechazar
        </b-button>
      </b-button-group>
    </b-button-toolbar>
    <div v-if="showApproveNote" class="d-flex justify-content-center">
      <h6 class="badge-primary">
        {{ 'Evidencia/Documento en Revisión de Plan de Acción'  }}
      </h6>
    </div>
  </fragment>
</template>

<script>
import ModalLoadFiles from '../ModalLoadFiles.vue'
import { approveLibrary } from '../../../../../services/libraryService'
import ModalRenewalFiles from '../ModalRenewalFiles.vue'

export default {
  components: {
    ModalLoadFiles,
    ModalRenewalFiles
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
      required: true,
    },
    for_review: {
      type: Boolean,
      required: true,
    }
  },
  computed: {
    showApproveButton() {
      return this.origin == 'Task' || this.origin == 'Obligation' || this.origin == 'Library'
    },
    showApproveNote() {
      return this.origin != 'Task' && this.for_review
    },
    userCanApprove() {
      const { can_approve } = this.permissions
      return this.origin == 'Task' && this.for_review && can_approve
    },
    userCanUploadFile() {
      return this.permissions.can_upload
    }
  },
  methods: {
    async approveFile(value) {
      try {
        this.showLoadingMixin()
        const form = { 
          approve: value,
          id_task: this.parentRecord.id_task
        }
        const { data } = await approveLibrary(this.parentRecord.library.id, form)
        this.showLoadingMixin()
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    successfullyReload() {
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>