<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button
      v-b-tooltip.hover.left
      title="Ver históricos"
      variant="info"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="calendar3" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <b-form
          ref="formRegister"
          autocomplete="off"
        >
          <only-view-customers class="mb-2"
            :customer-name="form.customerName"
            :corporate-name="form.corporateName"
          />
          <b-row>
            <b-col>
              <p class="text-justify">
                El contrato <span class="font-weight-bold">{{ form.contract }}</span> tiene la licencia 
                <span class="font-weight-bold">{{ form.license.name }}</span> que consta de una duración de 
                <span class="font-weight-bold">{{ form.license.num_period }} {{ form.license.period }}</span> comenzando en la fecha 
                <span class="font-weight-bold">{{ this.form.start_date_format }}</span> y terminando en 
                <span class="font-weight-bold">{{ this.form.end_date_format }}</span> con la cantidad de usuarios siguiente:
              </p>
              <ul>
                <li v-for="item in form.license.quantity" :key="item.id_profile_type">
                  <span class="font-weight-bold">{{ item.pivot.quantity }}</span> 
                  usuario(s) tipo <span class="font-weight-bold">{{ item.type }}</span>
                </li>
              </ul>
            </b-col>
          </b-row>
          <b-row>
            <b-col>
              <h5 class="font-weight-bold">
                Histórico
                <span id="popover-historical" class="click-pointer" >
                  <b-icon icon="info-circle" variant="primary"></b-icon>
                </span>
              </h5>
              <b-popover target="popover-historical" triggers="hover" placement="top">
                <template #title>Histórico</template>
                La tabla muestra todos los moviemintos que se hicieron en el contrato <b>{{ form.contract }}</b>
                respecto a los periodos de fechas, la fila en color verde es el cambio actual.
              </b-popover>
              <b-table 
                responsive 
                striped 
                hover 
                show-empty
                empty-text="No hay registros que mostrar"
                :fields="headerTable" 
                :items="historicals"
                :tbody-tr-class="rowClass"
              >
                <template #cell(sequence)="data">
                  <span> {{ data.item.sequence }} </span>
                </template>
                <template #cell(type)="data">
                  <b-badge
                    pill 
                    :variant="data.item.color"
                  >
                  {{ data.item.type_contract }}
                </b-badge>
                </template>
                <template #cell(period)="data">
                  <b-badge pill variant="secondary">
                    {{ data.item.num_period }} {{ data.item.period.name }}
                  </b-badge>
                </template>
                <template #cell(start_date_format)="data">
                  <span> {{ data.item.start_date_format }} </span>
                </template>
                <template #cell(end_date_format)="data">
                  <span> {{ data.item.end_date_format }} </span>
                </template>
                <template #cell(created_date_format)="data">
                  <span> {{ data.item.created_date_format }} </span>
                </template>
              </b-table>
            </b-col>
          </b-row>
        </b-form>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button 
            variant="danger"
            class="float-right mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>
import OnlyViewCustomers from '../components/customers/OnlyViewCustomers'
import { getContract } from '../../../services/contractService'

export default {
  components: {
    OnlyViewCustomers,
  },
  props: {
    register: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      dialog: false,
      historicals: [],
      form: {
        customerName: null,
        corporateName: null,
        contract: null,
        start_date_format: null,
        end_date_format: null,
        license: {
          name: null,
          num_period: null,
          period: null,
          quantity: []
        },
      },
    }
  },
  computed: {
    titleModal() {
      return `Histórico de contrato: ${this.register.contract}`
    },
    headerTable() {
      return [
        {
          key: 'sequence',
          label: 'Secuencia',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'type',
          label: 'Tipo',
          sortable: false,
        },
        {
          key: 'period',
          label: 'Periodo',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'start_date_format',
          label: 'Fecha de inicio',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'end_date_format',
          label: 'Fecha de termino',
          class: 'text-center',
          sortable: false,
        },
        {
          key: 'created_date_format',
          label: 'Fecha de movimiento',
          class: 'text-center',
          sortable: false,
        },
      ]
    }
  },
  methods: {
    async showModal() {
      this.reset()
      await this.loadUpdateRegister()
      this.dialog = true
    },
    async loadUpdateRegister() {
      try {
        this.showLoadingMixin()
        const { data } = await getContract(this.register.id_contract)
        const { contract, license, start_date_format, end_date_format, historicals } = data.data
        const { customer, corporate } = data.data
        const { name, num_period, quantity, period } = license
        this.form.customerName = customer.cust_trademark
        this.form.corporateName = corporate.corp_tradename
        this.form.contract = contract
        this.form.start_date_format = start_date_format
        this.form.end_date_format = end_date_format
        this.form.license.name = name
        this.form.license.num_period = num_period
        this.form.license.period = period.name
        this.form.license.quantity = quantity
        this.historicals = historicals
        this.showLoadingMixin()
      } catch (error) {
        console.log(error)
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.customerName = null
      this.form.corporateName = null
      this.form.contract = null
      this.form.start_date_format = null
      this.form.end_date_format = null
      this.form.license.name = null
      this.form.license.num_period = null
      this.form.license.period = null
      this.form.license.quantity = []
      this.historicals = []
    },
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (item.id_status === 1) return 'table-success'
    }
  }
}
</script>

<style scoped>
.click-pointer {
  cursor: pointer;
}
</style>