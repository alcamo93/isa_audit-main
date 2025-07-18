<template>
  <fragment>
    <loading :show="loadingMixin" />
    <div 
      v-if="hasAuditor"
      v-b-tooltip.hover.left
      title="Selección de Responsable"
      @click="showModal"
    >
      <b-avatar 
        :src="auditorPhoto"
        variant="secondary"
      ></b-avatar>
      <b-button 
        class="mr-auto btn-link go-to-process"
      >
        {{ auditorName }}
      </b-button>
    </div>
    <b-button
      v-else
      v-b-tooltip.hover.left
      title="Agregar Responsable"
      class="btn-link"
      variant="success"
      @click="showModal"
    >
      <b-icon icon="person-plus-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="md"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col class="text-center">
                <b-avatar 
                  v-if="form.id_user"
                  variant="primary" 
                  :src="auditorPhoto"
                  size="6rem"
                ></b-avatar>
              </b-col>
              <b-col sm="12" md="12">
                <b-form-group>
                  <label>
                    Usuario Responsable
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Usuario Responsable"
                  >
                    <v-select 
                      v-model="form.id_user"
                      :options="users"
                      :reduce="e => e.id"
                      label="name"
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

          </b-form>
        </validation-observer>
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="w-100">
          <b-button
            variant="success"
            class="float-right"
            @click="setUsers"
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
import { required } from '../../validations'
import { updateObligationUser } from '../../../services/obligationService'
import { getSingleProcess } from '../../../services/catalogSingleService'
import { getNoRequirementText } from '../components/scripts/texts'

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
    idObligationRegister: {
      type: Number,
      required: true,
    },
    obligation: {
      type: Object,
      required: true,
      default: null,
    },
  },
  data() {
    return {
      required,
      dialog: false,
      users: [],
      form: {
        id_user: null, 
      }
    }
  },
  computed: {
    titleModal() {
      if (this.obligation?.id_obligation == null) return ''
      const name = getNoRequirementText(this.obligation)
      return `Responsable para requerimiento: ${name}` 
    },
    hasAuditor() {
      const auditor = this.obligation?.auditor ?? null
      return auditor != null
    },
    auditorName() {
      return this.obligation?.auditor?.user?.person?.full_name ?? ''
    },
    auditorPhoto() {
      return this.obligation?.auditor?.user?.image?.full_path ?? ''
    },
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()
      this.getProcess()
      this.reset()
      this.showLoadingMixin()
      this.dialog = true
    },
    async setUsers() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await updateObligationUser(this.idAuditProcess, this.idAplicabilityRegister, this.idObligationRegister, this.obligation.id_obligation, this.form)
        const response = data
        this.dialog = false
        this.showLoadingMixin()
        this.$emit('successfully')
        this.responseMixin(response)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getProcess() {
      try {
        this.showLoadingMixin()
        const params = { scope: 'users' }
        const { data } = await getSingleProcess(this.idAuditProcess, params)
        this.record = data.data
        const { users } = data.data.corporate
        this.users = users.map(item => this.getAuditors(item))
        this.showLoadingMixin()
        this.dialog = true
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getAuditors(item) {
      const { id_user, person, image } = item
      return { id: id_user, name: person.full_name, path: image?.full_path ?? '' }
    },
    reset() {
      const userSelected = this.obligation?.auditor?.id_user ?? null
      this.form.id_user = userSelected
    }
  }
}
</script>

<style>
  
</style>