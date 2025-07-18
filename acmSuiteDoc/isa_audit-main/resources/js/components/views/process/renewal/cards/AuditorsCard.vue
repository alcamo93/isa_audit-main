<template>
  <b-card>
    <div v-if="title" class="font-weight-bold m-1">
      {{ title }}
    </div>
    <select-users
      :id-corporate="idCorporate"
      :auditors.sync="auditors"
      allow-flex
    />
  </b-card>
</template>

<script>
import SelectUsers from '../../handlers/SelectUsers.vue'

export default {
  components: { 
    SelectUsers,
  },
  props: {
    title: {
      type: String,
      required: false
    },
    idCorporate: {
      type: Number,
      required: true
    },
    auditors: {
      type: Array,
      required: true,
    }
  },
}
</script>

<style>

</style>