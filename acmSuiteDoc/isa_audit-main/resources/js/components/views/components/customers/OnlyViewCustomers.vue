<template>
  <fragment>
    <loading :show="loading" />
    <!-- Can select Customers and Corporates -->
    <b-row v-if="canSelectCustomerCorporate">
      <b-col sm="12" md="6">
        <label>
          Cliente
        </label>
        <div class="font-weight-bold">
          {{ customerName }}
        </div>
      </b-col>
      <b-col sm="12" md="6">
        <label>
          Planta
        </label>
        <div class="font-weight-bold">
          {{ corporateName }}
        </div>
      </b-col>
    </b-row>
    <!-- Can select only Corporates -->
    <b-row v-if="canSelectOnlyCorporate">
      <b-col>
        <label>
          Planta
        </label>
        <div class="font-weight-bold">
          {{ corporateName }}
        </div>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { whatProfile } from '../../../../services/profileService'

export default {
  async mounted() {
    this.showLoadingMixin()
    await this.whatsProfile()
    this.showLoadingMixin()
  },
  props: {
    customerName: {
      type: String,
      required: true,
    },
    corporateName: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      loading: false,
      owner: 0,
      level: 0,
    }
  },
  methods: {
    async whatsProfile() {
      try {
        const { data } = await whatProfile()
        const { owner, profile_level } = data.data.type
        this.owner = Boolean(owner)
        this.level = profile_level
      } catch (error) {
        this.responseMixin(error)
      }
    },
  },
  computed: {
    canSelectCustomerCorporate() {
      return this.owner && (this.level == 1 || this.level == 2)
    },
    canSelectOnlyCorporate() {
      return !this.owner && this.level == 3
    },
  } 
}
</script>

<style>

</style>