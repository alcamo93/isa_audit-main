<template>
  <fragment>
    <b-button 
      @click="showModal"
      block pill variant="success"
    >
      Activar Plan de Acción
      <b-icon icon="plus" aria-hidden="true"></b-icon> 
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <loading :show="loadingMixin" />
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <b-card-text class="text-justify">
                    El periodo de vigencia de la información contestada en la sección de aplicabilidad es de 12 meses.
                    Al seleccionar la fecha de inicio se calculará la fecha de cierre.
                    Las notificaciones de seguimiento de planes de acción estarán activas durante este período.
                  </b-card-text>
                </b-form-group>
              </b-col>
            </b-row>
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
                    name="Especificar fecha de inicio"
                  >
                    <vue-date-picker 
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      :disabled-date="disabledDateMixin"
                      :disabled-calendar-changer="disabledDateMixin"
                      v-model="form.init_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group>
                  <label>
                    Fecha de Cierre
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Especificar fecha de cierre"
                  >
                    <vue-date-picker 
                      input-class="form-control"
                      format="DD/MM/YYYY"
                      value-type="YYYY-MM-DD"
                      :disabled="true"
                      v-model="form.end_date"
                    ></vue-date-picker>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row v-if="isInitDateFuture">
              <b-col sm="12" md="12">
                <b-form-group>
                  <b-card-text class="text-justify font-weight-bold">
                    <span class="font-italic text-uppercase text-danger">* Atención</span><br>
                    La fecha de inicio de activación esta programada para el 
                    <span class="font-italic text-success"> {{ initDateFormat }} </span>, 
                    por lo tanto hasta esa fecha comenzará 
                    <span class="font-italic text-success"> {{ sectionTitle }} </span>
                  </b-card-text>
                </b-form-group>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button
            variant="success"
            class="float-right"
            @click="setRecord"
          >
            Registrar
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
import { required } from '../../../validations'
import { storeActionRegister } from '../../../../services/ActionRegisterService'
import { endDateFuture } from '../../components/scripts/dates'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    idAplicabilityRegister: {
      type: Number,
      required: true,
    },
    section: {
      type: String,
      required: true,
      default: '',
    },
    registerableId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      required,
      dialog: false,
      aplicabilityRegister: null,
      thereAreRequirements: false,
      form: {
        init_date: null, 
        end_date: null,
      }
    }
  },
  watch: {
    'form.init_date': function(val) {
      if (val == null) {
        this.reset()
        return
      }
      this.form.end_date = endDateFuture(val)
    }
  },
  computed: {
    activateAudit() {
      return this.section == 'audit' ? true : false
    },
    sectionTitle() {
      return this.activateAudit ? 'Plan de acción de  Auditoría' : 'Plan de acción de Permisos Críticos'
    },
    titleModal() {
      const sectionName = this.activateAudit ? 'Auditoría' : 'Permisos Críticos'
      return `Activar Plan de acción de ${sectionName}` 
    },
    initDateFormat() {
      return this.formatDateMixin(this.form.init_date)
    },
    isInitDateFuture() {
      if (this.form.init_date == null) return false
      const todayDate = new Date()
      const initDate = new Date(this.form.init_date)
      return todayDate < initDate
    }
  },
  methods: {
    async showModal() {
      this.reset()
      this.dialog = true
    },
    async setRecord() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await storeActionRegister(this.idAuditProcess, this.idAplicabilityRegister, this.section, this.registerableId, this.form)
        this.showLoadingMixin()
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.form.init_date = null
      this.form.end_date = null
    }
  }
}
</script>

<style>
  
</style>