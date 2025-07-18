<template>
  <fragment>
    <modal-load-files v-if="!hasFile"
      :is-new="true"
      :show-library="showLibrary"
      :origin="origin"
      :parent-record="parentRecord"
      :permissions="permissions"
      @successfully="successfully"
      :evaluateable-id="evaluateableId"
    />
    <modal-view-files
      :has-file="hasFile"
      :show-library="showLibrary"
      :origin="origin"
      :parent-record="parentRecord"
      :permissions="permissions"
      @successfully="successfully"
    />
  </fragment>
</template>

<script>
import ModalLoadFiles from './ModalLoadFiles.vue'
import ModalViewFiles from './ModalViewFiles.vue'

export default {
  components: {
    ModalLoadFiles,
    ModalViewFiles
  },
  props: {
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
  computed: {
    hasFile() {
      const { library } = this.parentRecord
      return library !== null
    },
  },
  methods: {
    successfully() {
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>