<template>
  <fragment>
    <b-card v-for="matter in items" :key="matter.id_matter">
      <div class="font-weight-bold m-1">
        {{ title }} de: {{ matter.matter }}
      </div>
      <div class="d-flex flex-wrap">
        <div class="flex-grow-1 text-center px-2 py-2 mx-1 my-1 rounded text-white font-weight-bold"
          v-for="aspect in matter.aspects" :key="aspect.id_aspect" 
          :style="{'background-color': matter.color}"
        >
          {{ aspect.aspect }}
        </div>
      </div>
    </b-card>
  </fragment>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: false,
      default: 'Aspectos'
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