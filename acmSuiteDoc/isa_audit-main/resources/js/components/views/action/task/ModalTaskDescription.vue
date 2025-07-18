<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-link 
      v-b-tooltip.hover.left
      :title="titleTooltip"
      class="btn btn-link go-to-process"
      @click="showModal"
    >
      {{ titleTruncateModal  }}
    </b-link>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <p class="text-justify"> {{ taskComputed }}</p>
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
import { truncateText } from '../../components/scripts/texts'

export default {
  props: {
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    titleModal() {
      if (this.record == null) return ''
      const { title } = this.record
      return `Tarea: ${title}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      const { title } = this.record
      return `Mostrar Tarea: ${title}`
    },
    titleTruncateModal() {
      if (this.record == null) return ''
      const { task } = this.record
      return truncateText(task)
    },
    taskComputed() {
      if (this.record == null) return ''
      const { task } = this.record
      return task
    },
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()      
      this.showLoadingMixin()
      this.dialog = true
    },
  }
}
</script>

<style>

</style>