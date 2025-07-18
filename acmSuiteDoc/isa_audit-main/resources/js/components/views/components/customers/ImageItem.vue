<template>
  <div class="d-flex justify-content-center">
    <div :style="containerStyle">
      <b-img v-if="showImage"
        fluid
        :src="path"
        @error="handleImageError"
        class="img-form"
      ></b-img>
      <div v-else 
        class="img-form error d-flex justify-content-center align-items-center"
      >
        <h6 class="font-weight-bold font-italic">
          {{ messageImage }}
        </h6>
      </div>
    </div>
  </div>
</template>

<script>
import { getImageItemPath } from '../scripts/images'

export default {
  props: {
    item: {
      type: Object,
      required: true
    },
    type: {
      type: String,
      required: true
    },
    usage: {
      type: String,
      required: false,
      default: 'dashboard'
    },
    size: {
      type: Number,
      required: false,
      default: 15
    }
  },
  data() {
    return {
      errorImage: false
    }
  },
  computed: {
    path() {
      return getImageItemPath(this.item, this.type, this.usage)
    },
    messageImage() {
      return this.path != null ? 'No se encontro imagen' : 'Sin imagen cargada'
    },
    showImage() {
      return !this.errorImage && this.path
    },
    containerStyle() {
      return { 
        width: `${this.size}em`,
        aspectRatio: '1/0.9'
      }
    }
  },
  watch: {
    path() {
      this.errorImage = false;
    }
  },
  methods: {
    handleImageError(params) {
      this.errorImage = true;
    }
  }
}
</script>

<style scoped>
.img-form {
  width: 100%;
  height: 100%;
  /* object-fit: cover; */
  background: #ffffff00;
}

.img-form.error {
  width: 100%;
  height: 100%;
  background: #DDD;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>