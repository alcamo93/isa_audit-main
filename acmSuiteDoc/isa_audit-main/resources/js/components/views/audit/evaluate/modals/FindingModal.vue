<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      class="btn-link"
      v-b-tooltip.hover.left 
      :title="titleTooltip"
      :variant="variantBtn"
      @click="showModal"
    >
      <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesFormProcess">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Hallazgo
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Hallazgo"
                  >
                    <b-form-textarea
                      v-model="text"
                      placeholder="Escriba el hallazgo..."
                      rows="3"
                      max-rows="25"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
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
            class="float-right"
            variant="success"
            @click="setAuditFinding"
          >
            Registrar
          </b-button>
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
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '../../../../validations'
import { getNoRequirementText, getFieldRequirement } from '../../../components/scripts/texts'
import { getAudit, setAuditFinding } from '../../../../../services/AuditService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver
  },
  props: {
    params: {
      type: Object,
      required: true
    },
    record: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      dialog: false,
      required,
      text: null,
    }
  },
  computed: {
    isRequirement() {
      return this.record.subrequirement == null ? true : false
    },
    getNameRecord() {
      const type = this.isRequirement ? 'requerimiento' : 'subrequerimiento'
      return `${type} ${getNoRequirementText(this.record)}`
    },
    titleModal() {
      if (this.record == null) return ''
      return `Hallazgo para ${this.getNameRecord}`
    },
    titleTooltip() {
      if (this.record == null) return ''
      return `Ver hallazgo para ${this.getNameRecord}`
    },
    noFindingRequired() {
      const isSubrequirement = this.record.id_subrequirement == null
      const hasSubrequirements = Boolean(this.record.requirement.has_subrequirement)
      return hasSubrequirements && isSubrequirement
    },
    variantBtn() {
      if (this.record.audit == null) return 'info'
      const { audit } = this.record
      const finding = audit.finding
      const isNegativeAnswer = audit.key_answer.key == 'NEGATIVE'
      return (finding == null) && isNegativeAnswer && !this.noFindingRequired ? 'danger' : 'info'
    }
  },
  methods: {
    async showModal() {
      await this.getRecord()
    },
    async getRecord() {
      try {
        this.showLoadingMixin()
        const { idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect } = this.params
        const { data } = await getAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, this.record.id)
        const { audit } = data.data
        // default data
        const finding = audit.finding
        const description = getFieldRequirement(data.data, 'description')
        this.text = finding == null ? description : finding
        this.dialog = true
        this.showLoadingMixin()
      } catch (error) {  
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async setAuditFinding() {
      try {
        const isValid = await this.$refs.rulesFormProcess.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect } = this.params
        const form =  { finding: this.text }
        const { data } = await setAuditFinding(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, this.record.id, form)
        this.dialog = false
        this.$emit('successfully')
        this.responseMixin(data)
        this.showLoadingMixin()
      } catch (error) { 
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style scoped>
.btn-link {
  padding: 3px;
}
</style>