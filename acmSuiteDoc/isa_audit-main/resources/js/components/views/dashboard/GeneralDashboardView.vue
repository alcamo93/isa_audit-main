<template>
  <fragment>
    <loading :show="loadingMixin" />
    <admin-view v-if="canViewGeneralDashboard" />
    <customer-view v-if="canViewCustomerDashboard" />
    <corporate-view v-if="canViewCorporateDashboard" />
  </fragment>
</template>

<script>
  import AdminView from './views/AdminView.vue'
  import CustomerView from './views/CustomerView.vue'
  import CorporateView from './views/CorporateView.vue'
  import { whatProfile } from '../../../services/profileService'

  export default {
    components: {
      AdminView,
      CustomerView,
      CorporateView
    },
    mounted() {
      this.setProfileData()
    },
    data() {
      return {
        owner: false,
        profile_level: null,
        type: null
      }
    },
    computed: {
      canViewGeneralDashboard() {
        return this.owner && (this.profile_level == 1 || this.profile_level == 2)
      },
      canViewCustomerDashboard() {
        return !this.owner && this.profile_level == 3
      },
      canViewCorporateDashboard() {
        return !this.owner && (this.profile_level == 4 || this.profile_level == 5)
      }
    },
    methods: {
      async setProfileData() {
        try {
          this.showLoadingMixin()
          const { data } = await whatProfile()
          const { owner, profile_level, type } = data.data.type
          this.owner = Boolean(owner)
          this.profile_level = profile_level
          this.type = type
          this.showLoadingMixin()
        } catch (error) {
          this.showLoadingMixin()
          this.responseMixin(error)
        }
      },
    }
  }
</script>

<style scoped>
  
</style>