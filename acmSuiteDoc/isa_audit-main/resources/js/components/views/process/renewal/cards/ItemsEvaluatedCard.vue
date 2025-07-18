<template>
  <fragment>
    <b-card v-for="matter in items" :key="matter.id_matter">
      <div class="font-weight-bold m-1">
        {{ title }} de: {{ matter.matter }}
      </div>
      <div class="d-flex flex-wrap">
        <b-button class="flex-grow-1 text-center px-2 py-2 mx-1 my-1 rounded text-white font-weight-bold"
          v-for="aspect in matter.aspects" :key="aspect.id_aspect" 
          :style="{'background-color': matter.color}"
          :title="handlerSelected(aspect, 'tooltip')"
          v-b-tooltip.hover.leftbottom
          @click="handlerForm(aspect)"
        >
          {{ aspect.aspect }}
          <b-icon :icon="handlerSelected(aspect, 'icon')" aria-hidden="true"></b-icon>
        </b-button>
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
    },
    forms: {
      type: Array,
      required: true,
    }
  },
  methods: {
    handlerSelected(item, action) {
      const isSelected = action == 'icon' ? 'patch-check-fill' : 'Remover este aspecto'
      const isNotSelected = action == 'icon' ? 'patch-plus' : 'Agregar este aspecto'
      return this.forms.includes(item.id_form) ? isSelected : isNotSelected
    },
    handlerForm(item) {
      let newForms = this.forms
      if ( newForms.indexOf(item.id_form) != -1 ) {
        newForms = newForms.filter(idForm => idForm != item.id_form)
      } else {
        newForms.push(item.id_form)
      }
      this.$emit('update:forms', newForms)
    }
  }
}
</script>

<style>

</style>