<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      :title="titleTooltip"
      :variant="isNew ? 'info' : 'success'"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="geo-alt" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <div v-for="(item, index) in form" :key="index">
              <b-row>
                <b-col sm="12" md="6">
                  <h5 class="font-weight-bold">
                    Dirección {{ typeAddress(item.type) }}
                  </h5>
                </b-col>
                <b-col sm="12" md="6">
                  <b-button v-if="item.type == 0"
                    variant="info"
                    class="float-right mr-2"
                    @click="duplicateAddress"
                  >
                    Copiar Dirección Física
                  </b-button>
                </b-col>
              </b-row>
              <b-row>
                <b-col sm="12" md="6">
                  <b-form-group>
                    <label>
                      Calle <span class="text-danger">*</span>
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="required|max:150"
                      :name="`Calle (Dirección ${typeAddress(item.type)})`"
                    >
                      <b-form-input
                        v-model="form[index].street"
                        type="email"
                        placeholder="Calle"
                      ></b-form-input>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col sm="12" md="6">
                  <b-form-group>
                    <label>
                      Colonia <span class="text-danger">*</span>
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="required|max:50"
                      :name="`Colonia (Dirección ${typeAddress(item.type)})`"
                    >
                      <b-form-input
                        v-model="form[index].suburb"
                        placeholder="Colonia"
                      ></b-form-input>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row>
                <b-col sm="12" md="4">
                  <b-form-group>
                    <label>
                      Número exterior
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="max:60"
                      :name="`Número exterior (Dirección ${typeAddress(item.type)})`"
                    >
                      <b-form-input
                        v-model="form[index].ext_num"
                        placeholder="#50"
                      ></b-form-input>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col sm="12" md="4">
                  <b-form-group>
                    <label>
                      Número interior
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="max:60"
                      :name="`Número interior (Dirección ${typeAddress(item.type)})`"
                    >
                      <b-form-input
                        v-model="form[index].int_num"
                        placeholder="#13"
                      ></b-form-input>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col sm="12" md="4">
                  <b-form-group>
                    <label>
                      Código Postal
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="numeric|max:10"
                      :name="`Código Postal (Dirección ${typeAddress(item.type)})`"
                    >
                      <b-form-input
                        v-model="form[index].zip"
                        placeholder="12340"
                      ></b-form-input>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row>
                <b-col sm="12" md="4">
                  <b-form-group>
                    <label>
                      País <span class="text-danger">*</span>
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="required"
                      :name="`País (Dirección ${typeAddress(item.type)})`"
                    >
                      <v-select 
                        v-model="form[index].id_country"
                        :options="countries"
                        :reduce="e => e.id_country"
                        label="country"
                        placeholder="Seleccionar"
                        @input="getStates(item.type)"
                      >
                        <div slot="no-options">
                          No se encontraron registros
                        </div>
                      </v-select>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col sm="12" md="4">
                  <b-form-group>
                    <label>
                      Estado <span class="text-danger">*</span>
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="required"
                      :name="`Estado (Dirección ${typeAddress(item.type)})`"
                    >
                      <v-select 
                        v-model="form[index].id_state"
                        :options="getLists('states', item.type)"
                        :reduce="e => e.id_state"
                        label="state"
                        placeholder="Seleccionar"
                        @input="getCities(item.type)"
                      >
                        <div slot="no-options">
                          No se encontraron registros
                        </div>
                      </v-select>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-form-group>
                </b-col>
                <b-col sm="12" md="4">
                  <b-form-group>
                    <label>
                      Ciudad <span class="text-danger">*</span>
                    </label>
                    <validation-provider
                      #default="{ errors }"
                      rules="required"
                      :name="`Ciudad (Dirección ${typeAddress(item.type)})`"
                    >
                      <v-select 
                        v-model="form[index].id_city"
                        :options="getLists('cities', item.type)"
                        :reduce="e => e.id_city"
                        label="city"
                        placeholder="Seleccionar"
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
            </div>
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <!-- footer -->
        <div class="w-100">
          <b-button
            variant="success"
            class="float-right"
            @click="sendAddress"
          >
            {{ ( isNew ? 'Registrar' : 'Actualizar' ) }}
          </b-button>
          <b-button
            variant="danger" 
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cancelar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, max, numeric } from '../../../validations'
import { getAddresses, storeAddress, updateAddress } from '../../../../services/addressService'
import { getCountries, getStates, getCities } from '../../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    register: {
      type: Object,
      required: false,
    },
    idCustomer: {
      type: Number,
      required: true,
    },
    idCorporate: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      required,
      max,
      numeric,
      dialog: false,
      countries: [],
      states: [
        { id_type: 0, list: [] },
        { id_type: 1, list: [] }
      ],
      cities: [
        { id_type: 0, list: [] },
        { id_type: 1, list: [] }
      ],
      form: [
        {
          id_corporate: null,
          id_address: null,
          street: null,
          ext_num: null,
          int_num: null,
          zip: null,
          suburb: null,
          type: 1,
          type_text: null,
          id_country: null,
          id_state: null,
          id_city: null
        },
        {
          id_corporate: null,
          id_address: null,
          street: null,
          ext_num: null,
          int_num: null,
          zip: null,
          suburb: null,
          type: 0,
          type_text: null,
          id_country: null,
          id_state: null,
          id_city: null
        }
      ],
    }
  },
  computed: {
    isNew() {
      return this.register.addresses.length == 0
    },
    titleModal() {
      return `Dirección de: ${this.register.corp_tradename}`
    },
    titleTooltip() {
      const stage = this.isNew ? 'Agregar' : 'Modificar'
      return `${stage} Dirección`
    },
  },
  methods: {
    async showModal() {
      this.reset()
      await this.getCountries()
      if (!this.isNew) await this.loadUpdateRegister()
      this.dialog = true
    },
    getLists(list, type) {
      if (list == 'states') {
        const findIndex = this.states.findIndex(group => type == group.id_type)
        return this.states[findIndex].list
      } else {
        const findIndex = this.cities.findIndex(group => type == group.id_type)
        return this.cities[findIndex].list
      }
    },
    async getCountries() {
      try {
        const { data } = await getCountries()
        this.countries = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getStates(type) {
      try {
        // search by type
        const indexForm = this.form.findIndex(group => type == group.type)
        const indexState = this.states.findIndex(group => type == group.id_type)
        const indexCity = this.cities.findIndex(group => type == group.id_type)
        // evaluate change
        const idCountry = this.form[indexForm].id_country
        this.form[indexForm].id_state = null
        this.form[indexForm].id_city = null
        this.states[indexState].list = []
        this.cities[indexCity].list = []
        // stop without data country
        if (idCountry == null) return
        // get list 
        const filters = { id_country: idCountry }
        const { data } = await getStates({}, filters)
        this.states[indexState].list = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async getCities(type) {
      try {
        // search by type
        const indexForm = this.form.findIndex(group => type == group.type)
        const indexCity = this.cities.findIndex(group => type == group.id_type)
        // evaluate change
        const idState = this.form[indexForm].id_state
        this.form[indexForm].id_city = null
        this.cities[indexCity].list = []
        // stop without data country
        if (idState == null) return
        // get list 
        const filters = { id_state: idState }
        const { data } = await getCities({}, filters)
        this.cities[indexCity].list = data.data
      } catch (error) {
        this.responseMixin(error)
      }
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getAddresses(this.idCustomer, this.idCorporate)
        data.data.forEach(item => {
          const { id_address, street, ext_num, int_num, zip, suburb, type, id_country, id_state, id_city } = item
          const findIndex = this.form.findIndex(group => type == group.type)
          this.form[findIndex].id_address = id_address
          this.form[findIndex].street = street
          this.form[findIndex].ext_num = ext_num
          this.form[findIndex].int_num = int_num
          this.form[findIndex].zip = zip
          this.form[findIndex].suburb = suburb
          this.form[findIndex].type = type
          this.form[findIndex].id_country = id_country
          this.getStates(type)
          this.form[findIndex].id_state = id_state
          this.getCities(type)
          this.form[findIndex].id_city = id_city
        })
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async sendAddress() {
      const isValid = await this.$refs.rulesForm.validate()
      if (!isValid) return
      
      try {
        this.showLoadingMixin()
        let response = null
        if (this.isNew) {
          const responsePhysical = await this.setAddress(1)
          const responseFiscal = await this.setAddress(0) 
          response = this.getResponse([responsePhysical, responseFiscal])
        } else {
          const responsePhysical = await this.updateAddress(1)
          const responseFiscal = await this.updateAddress(0)
          response = this.getResponse([responsePhysical, responseFiscal])
        }
        if (response.hasInfo) {
          this.showLoadingMixin()
          this.responseMixin(response.response)
          return
        }
        this.showLoadingMixin()
        this.dialog = false
        this.responseMixin(response.response)
        this.$emit('successfully')
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getResponse(responses) {
      const hasInfo = responses.find(item => item.info)
      const evaluate = hasInfo !== undefined
      if (evaluate) {
        return { hasInfo: evaluate, response: hasInfo }
      }
      return { hasInfo: evaluate, response: responses.find(item => item.success) } 
    },
    async setAddress(type) {
      const findIndex = this.form.findIndex(frm => type == frm.type)
      const { data } = await storeAddress(this.idCustomer, this.idCorporate, this.form[findIndex])
      return data
    },
    async updateAddress(type) {
      const findIndex = this.form.findIndex(frm => type == frm.type)
      const { data } = await updateAddress(this.idCustomer, this.idCorporate, this.form[findIndex].id_address, this.form[findIndex])
      return data
    },
    reset() {
      this.form = [
        {
          id_address: null,
          street: null,
          ext_num: null,
          int_num: null,
          zip: null,
          suburb: null,
          type: 1,
          id_country: null,
          id_state: null,
          id_city: null
        },
        {
          id_address: null,
          street: null,
          ext_num: null,
          int_num: null,
          zip: null,
          suburb: null,
          type: 0,
          id_country: null,
          id_state: null,
          id_city: null
        }
      ]
    },
    typeAddress(type) {
      return ( Boolean(type) ? 'Física' : 'Fiscal' )
    },
    checkAddress(type) {
      const find = this.register.addresses.filter(item => item.type == type)
      return find.length == 0
    },
    async duplicateAddress() {
      const physicalIndex = this.form.findIndex(frm => 1 == frm.type)
      const fiscalIndex = this.form.findIndex(frm => 0 == frm.type)
      const { street, ext_num, int_num, zip, suburb, id_country, id_state, id_city } = this.form[physicalIndex]
      this.form[fiscalIndex].street = street
      this.form[fiscalIndex].ext_num = ext_num
      this.form[fiscalIndex].int_num = int_num
      this.form[fiscalIndex].zip = zip
      this.form[fiscalIndex].suburb = suburb
      this.form[fiscalIndex].id_country = id_country
      await this.getStates(0)
      this.form[fiscalIndex].id_state = id_state
      await this.getCities(0)
      this.form[fiscalIndex].id_city = id_city
    }
  }
}
</script>

<style>

</style>