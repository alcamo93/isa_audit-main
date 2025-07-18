<template>
  <fragment>
    <loading :show="loading"/>
    <!-- Can select Customers and Corporates -->
    <b-row v-if="canSelectCustomerCorporate">
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Cliente
          </label>
          <v-select 
            v-model="form.id_customer"
            :options="customers"
            :reduce="e => e.id_customer"
            label="cust_trademark"
            :append-to-body="!useInModal"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Planta
          </label>
          <v-select 
            v-model="form.id_corporate"
            :options="corporates"
            :reduce="e => e.id_corporate"
            label="corp_tradename"
            :append-to-body="!useInModal"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
    </b-row>
    <!-- Can select only Corporates -->
    <b-row v-if="canSelectOnlyCorporate">
      <b-col>
        <b-form-group>
          <label>
            Planta
          </label>
          <v-select 
            v-model="form.id_corporate"
            :options="corporates"
            :reduce="e => e.id_corporate"
            label="corp_tradename"
            :append-to-body="!useInModal"
            placeholder="Todos"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
        </b-form-group>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { getCustomersSource, getCorporatesSource } from "../../../../services/catalogService";
import { whatProfile } from '../../../../services/profileService'

export default {
  async created() {
    this.loading = true
    await this.whatsProfile()
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
      loading: false,
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
        const { id_customer, id_corporate } = data.data
        await this.buildFilters({id_customer, id_corporate})
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async buildFilters({id_customer, id_corporate}) {
      try {
        // Global Admin
        if (this.level == 1 || this.level == 2) {
          await this.getCustomers()
        }
        // All corporates only customer
        if (this.level == 3) {
          this.form.id_customer = id_customer
          await this.getCorporates()
        }
        // Only corporate
        if (this.level == 4 || this.level == 5) {
          this.form.id_customer = id_customer
          this.form.id_corporate = id_corporate
        }
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
    async getCorporates() {
      try {
        const filters = { id_customer: this.form.id_customer }
        const { data } = await getCorporatesSource({}, filters)
        this.corporates = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
  },
  watch: {
    'form.id_customer': function() {
      const modifyCorporateField = this.canSelectCustomerCorporate
      if (modifyCorporateField) this.form.id_corporate = null
      this.corporate = []
      if (modifyCorporateField) this.getCorporates()
      this.$emit('fieldSelected', this.form)
    },
    'form.id_corporate': function() {
      this.$emit('fieldSelected', this.form)
    }
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