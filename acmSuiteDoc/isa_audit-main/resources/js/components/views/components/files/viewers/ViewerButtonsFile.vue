<template>
  <fragment>
    <viewer-pdf
      v-if="typeFile == 'pdf'"
      :record="fileRecord"
    ></viewer-pdf>
    <viewer-image
      v-if="typeFile == 'jpg' || typeFile == 'jpeg' || typeFile == 'png'"
      :record="fileRecord"
    ></viewer-image>
    <viewer-doc
      v-if="typeFile == 'docx' || typeFile == 'xlsx' || typeFile == 'pptx'"
      :record="fileRecord"
    ></viewer-doc>
    <download-file 
      :record="fileRecord"
    ></download-file>
  </fragment>
</template>

<script>
import ViewerPdf from './ViewerPdf'
import ViewerImage from './ViewerImage'
import ViewerDoc from './ViewerDoc'
import DownloadFile from './DownloadFile'

export default {
  components: {
    ViewerPdf,
    ViewerImage,
    ViewerDoc,
    DownloadFile,
  },
  props: {
    fileRecord: {
      type: Object,
      required: true,
    },
  },
  computed: {
    typeFile() {
      return this.fileRecord.extension.toLowerCase()
    },
  }
}
</script>

<style>

</style>