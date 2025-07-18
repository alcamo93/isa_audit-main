<template>
  <b-card>
    <div v-if="title" class="font-weight-bold m-1">
      {{ title }}
    </div>
    <div class="d-flex flex-wrap">
      <div class="flex-grow-1 text-center px-2 py-2 mx-1 my-1 rounded text-white" :class="`bg-${auditor.color}`"
        v-for="auditor in items" :key="auditor.id"
      >
        <span class="font-weight-bold">
          {{ auditor.name }} 
        </span>
        <span class="font-italic"> ({{ auditor.type }}) </span>
      </div>
    </div>
  </b-card>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: false
    },
    items: {
      type: Array,
      required: true,
    }
  },
}
</script>

<style>

</style>