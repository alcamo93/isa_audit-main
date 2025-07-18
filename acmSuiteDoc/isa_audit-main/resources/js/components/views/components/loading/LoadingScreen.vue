<template>
  <div v-show="show" class="loading-container">
    <div class="img-loading loader"></div>
  </div>
</template>

<script>
  export default {
    name: 'Loading',
    props: {
      show: {
        type: Boolean,
        default: false
      }
    }
  }
</script>

<style scoped>
.loading-container {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 99999;
}
</style>