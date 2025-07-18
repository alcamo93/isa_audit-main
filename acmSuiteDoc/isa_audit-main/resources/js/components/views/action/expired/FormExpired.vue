<template>
  <validation-observer ref="rulesForm">
    <b-form
      ref="formRegister"
      autocomplete="off"
    >
      <b-row>
        <b-col cols="12">
          <b-form-group>
            <label>
              No. Requerimiento
            </label>
            <div>
              <span class="font-weight-bold"> 
                {{ noRequirement }} 
              </span>
            </div>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-form-group>
            <label>
              Requerimiento
            </label>
            <div>
              <span class="font-weight-bold"> 
                {{ requirement }} 
              </span>
            </div>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-form-group>
            <label>
              Causa de desviación
              <span class="text-danger">*</span>
            </label>
            <validation-provider
              #default="{ errors }"
              rules="required"
              name="Causa de desviación"
            >
              <b-form-textarea
                :value="cause"
                placeholder="Especifica el criterio"
                rows="3"
                max-rows="6"
                @input="updateCause"
              ></b-form-textarea>
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-form-group>
            <label>
              Fecha de resolución
              <span class="text-danger">*</span>
            </label>
            <validation-provider
              #default="{ errors }"
              rules="required"
              name="Fecha de resolución"
            >
              <vue-date-picker 
                input-class="form-control"
                format="DD/MM/YYYY"
                value-type="YYYY-MM-DD"
                :disabled-date="disabledDateMin"
                :disabled-calendar-changer="disabledDateMin"
                :value="extensionDate"
                @input="updateDate"
              ></vue-date-picker>
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
        </b-col>
      </b-row>
    </b-form>
  </validation-observer>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { getNoRequirementText, getRequirementText } from '../../components/scripts/texts'
import { required } from '../../../validations'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    record: {
      type: Object,
      required: true
    },
    cause: {
      type: [String, null],
      required: false
    },
    extensionDate: {
      type: [String, null],
      required: false
    }
  },
  data() {
    return {
      required
    }
  },
  computed: {
    noRequirement() {
      if (this.record == null) return ''
      return getNoRequirementText(this.record)
    },
    requirement() {
      if (this.record == null) return ''
      return getRequirementText(this.record)
    },
  },
  methods: {
    disabledDateMin(date) {
      return date < new Date()
    },
    updateCause(event) {
      this.$emit('update:cause', event)
    },
    updateDate(event) {
      this.$emit('update:extension-date', event)
    },
    async validate() {
      return await this.$refs.rulesForm.validate()
    }
  }
}
</script>

<style>

</style>