<template>
  <fragment>
    <loading :show="loading" />
    <!-- Can select Customers and Corporates -->
    <b-row v-if="canSelectCustomerCorporate">
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Cliente <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Cliente"
          >
            <v-select 
              v-model="form.id_customer"
              :options="customers"
              :reduce="e => e.id_customer"
              label="cust_trademark"
              :append-to-body="!useInModal"
            >
              <div slot="no-options">
                No se encontraron registros
              </div>
            </v-select>
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Planta <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Planta"
          >
            <v-select 
              v-model="form.id_corporate"
              :options="corporates"
              :reduce="e => e.id_corporate"
              label="corp_tradename"
              :append-to-body="!useInModal"
            >
              <div slot="no-options">
                No se encontraron registros
              </div>
            </v-select>
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
    </b-row>
    <!-- Can select only Corporates -->
    <b-row v-if="canSelectOnlyCorporate">
      <b-col>
        <b-form-group>
          <label>
            Planta <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Planta"
          >
            <v-select 
              v-model="form.id_corporate"
              :options="corporates"
              required
              :reduce="e => e.id_corporate"
              label="corp_tradename"
              :append-to-body="!useInModal"
            >
              <div slot="no-options">
                No se encontraron registros
              </div>
            </v-select>
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '../../../validations'
import { getCustomersSource, getCorporatesSource } from "../../../../services/catalogService";
import { whatProfile } from '../../../../services/profileService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  async mounted() {
    this.loading = true
    await this.whatsProfile()
    if (this.canSelectCustomerCorporate) await this.getCustomers()
    this.loading = false
  },
  props: {
    useInModal: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      required,
      loading: false,
      disabledWatch: false,
      owner: 0,
      level: 0,
      customers: [],
      corporates: [],
      form: {
        id_customer: null,
        id_corporate: null
      }
    }
  },
  methods: {
    async whatsProfile() {
      try {
        const { data } = await whatProfile()
        const { owner, profile_level } = data.data.type
        this.owner = Boolean(owner)
        this.level = profile_level
        const { corporate } = data.data
        this.loadData(corporate)
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getCustomers() {
      try {
        const { data } = await getCustomersSource()
        this.customers = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getCorporates(clear = true, isSelected = false) {
      try {
        const filters = { id_customer: this.form.id_customer }
        const { data } = await getCorporatesSource({}, filters)
        // if (clear || isSelected) this.form.id_corporate = null
        this.corporates = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async loadData(data, load = false) {
      this.disabledWatch = true
      const { id_customer, id_corporate } = data
      if (this.canSelectCustomerCorporate) {
        // can select customer and corporate
        if (!load) return
        this.form.id_customer = id_customer
        this.form.id_corporate = id_corporate
      }
      if (this.canSelectOnlyCorporate) {
        this.form.id_customer = id_customer
        if (!load) return
        this.form.id_corporate = id_corporate
      }
      if (!this.canSelectCustomerCorporate && !this.canSelectOnlyCorporate) {
        this.form.id_customer = id_customer
        this.form.id_corporate = id_corporate
      }
      setTimeout(() => this.disabledWatch = false, 1000);
      this.$emit('fieldSelected', this.form)
    }
  },
  watch: {
    'form.id_customer': function(value) {
      this.$emit('fieldSelected', this.form)
      if (value != null) this.getCorporates(false, true)
      if (this.disabledWatch) return 
      this.form.id_corporate = null 
    },
    'form.id_corporate': function(value) {
      this.$emit('fieldSelected', this.form)
      if (this.disabledWatch) return
      if (value == null) return
    },
    clearCorporateInList(value) {
      if (value) this.form.id_corporate = null 
    }
  },
  computed: {
    canSelectCustomerCorporate() {
      return this.owner && (this.level == 1 || this.level == 2)
    },
    canSelectOnlyCorporate() {
      return !this.owner && this.level == 3
    },
    clearCorporateInList() {
      if ( this.form.id_customer == null && this.corporates.length > 0 ) return true
      const existInList = this.corporates.findIndex(item => item.id_corporate == this.form.id_corporate)
      return existInList == -1
    }
  }
}
</script>

<style>

</style>