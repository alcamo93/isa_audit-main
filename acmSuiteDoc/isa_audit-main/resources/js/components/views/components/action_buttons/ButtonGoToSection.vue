<template>
  <b-button 
    variant="success"
    :class="rightClass" 
    @click="goToSection"
  >
    <b-icon icon="box-arrow-up-right" aria-hidden="true"></b-icon> 
    {{ titleButton }}
  </b-button>
</template>

<script>

export default {
  props: {
    url: {
      type: String,
      required: true
    },
    titleButton: {
      type: String,
      required: false,
    },
    right: {
      type: Boolean,
      required: false,
      default: false,
    }
  },
  computed: {
    rightClass() {
      return this.right ? 'float-right' : ''
    },
  },
  methods: {
    goToSection() {
      const host = window.location.origin
      const path = `${host}${this.url}`
      window.open(path, '_blank')
    },
  }
}
</script>

<style>

</style>