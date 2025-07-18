<template>
    <fragment>
      <viewer-image
        :record="fileRecord"
      ></viewer-image>
      <download-image
        :record="fileRecord"
      ></download-image>
    </fragment>
  </template>
  
  <script>
  import ViewerImage from '../../../../../views/components/files/viewers/ViewerImage'
  import DownloadImage from './DownloadImage'
  
  export default {
    components: {
      ViewerImage,
      DownloadImage,
    },
    props: {
      fileRecord: {
        type: Object,
        required: true,
      },
    }
  }
  </script>
  
  <style>
  
  </style>