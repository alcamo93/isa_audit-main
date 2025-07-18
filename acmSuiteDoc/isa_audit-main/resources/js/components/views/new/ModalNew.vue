<template>
  <fragment>
    <loading :show="loadingMixin"/>
      <b-modal
        v-model="dialog"
        size="xl"
        title="Noticia"
        :no-close-on-backdrop="true"
      >
      <b-container fluid>
        <rich-text-edit 
          :initial-content="newData"
          disabled
          onlyPresentation
        />
      </b-container>
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
import RichTextEdit from '../components/rich_text/RichTextEdit'
  export default {
    components: {
      RichTextEdit
    },
    props: {
      record: {
        type: Object,
        required: false,
      },
    },
    data() {
      return {
        dialog: false,
      }
    },
    computed:{
      newData() {
        return this.record.description_env
      }
    },
    methods: {
      async showModal() {
        this.dialog = true
      },
    }
  }
</script>