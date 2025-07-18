<template>
  <fragment>
    <b-row>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Fecha de Inicio
            <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Fecha de Inicio"
          >
            <vue-date-picker 
              input-class="form-control"
              format="DD/MM/YYYY"
              value-type="YYYY-MM-DD"
              v-model="localInitDate"
              :disabled-date="handlerDatesInit"
            />
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
      <b-col sm="12" md="6">
        <b-form-group>
          <label>
            Fecha de Vencimiento
            <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Fecha de Vencimiento"
          >
            <vue-date-picker 
              :disabled="!localInitDate"
              input-class="form-control"
              format="DD/MM/YYYY"
              value-type="YYYY-MM-DD"
              v-model="localEndDate"
              :disabled-date="handlerDateEnd"
            />
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="12">
        <b-form-group>
          <label>
            Fechas para recordatorio
            <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required"
            name="Fechas para recordatorio"
          >
            <vue-date-picker 
              :disabled="!localEndDate"
              input-class="form-control"
              format="DD/MM/YYYY"
              value-type="YYYY-MM-DD"
              :disabled-date="handlerDatesSelected"
              v-model="localDates"
              multiple
            ></vue-date-picker>
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
    </b-row>
  </fragment>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { required } from '../../../../validations'

export default {
  components: {
    ValidationProvider,
  },
  props: {
    initDate: {
      type: [String, null],
      required: false
    },
    endDate: {
      type: [String, null],
      required: false
    },
    dates: {
      type: Array,
      required: false
    },
    maxDate: {
      type: [String, null],
      required: false
    }
  },
  data() {
    return {
      required,
    } 
  },
  computed: {
    localInitDate: {
      get() {
        return this.initDate
      },
      set(value) {
        if (value == null) {
          this.$emit('update:endDate', null)
        }
        this.$emit('update:dates', [])
        this.$emit('update:initDate', value)
      },
    },
    localEndDate: {
      get() {
        return this.endDate
      },
      set(value) {
        this.$emit('update:dates', [])
        this.$emit('update:endDate', value)
      },
    },
    localDates: {
      get() {
        return this.dates
      },
      set(value) {
        this.$emit('update:dates', value)
      },
    },
  },
  methods: {
    handlerDatesInit(date) {
      if ( this.maxDate != null ) {
        return date > new Date(this.maxDate)
      }
      return false
    },
    handlerDateEnd(date) {
      if ( this.maxDate != null ) {
        return date < new Date(this.initDate) || date > new Date(this.maxDate)
      }
      return date < new Date(this.initDate)
    },
    handlerDatesSelected(date) {
      return date < new Date(this.initDate) || date > new Date(this.endDate)
    },
  }
}
</script>

<style>

</style>