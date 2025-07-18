<template>
  <fragment>
    <loading :show="loadingMixin" />
    <div 
      v-if="hasAuditor"
      v-b-tooltip.hover.left
      title="Selección de Responsables"
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
      title="Agregar Responsables"
      class="btn-link"
      variant="success"
      @click="showModal"
    >
      <b-icon icon="person-plus-fill" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container fluid>
        <b-row>
          <b-col>
            <b-button class="float-right ml-2" 
              variant="success"
              title="Agregar responsable de aprobación"
              @click="addResponsible"
            >
              Agregar responsable de aprobación 
              <b-icon icon="plus" aria-hidden="true"></b-icon>
            </b-button>
          </b-col>
        </b-row>
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >

            <b-row v-for="(user, index) in form" :key="user.level"
              align-v="center"
              align-h="center"
            >
              <b-col sm="4" md="4" class="text-center">
                <b-avatar 
                  variant="primary" 
                  :src="getImageByUser(user.id_user)"
                  size="5rem"
                ></b-avatar>
                <br>
                <b-button
                  v-if="user.level != 1"
                  v-b-tooltip.hover.left
                  :title="`Remover ${getTypeResponsible(user.level)}`"
                  class="mt-2"
                  variant="danger"
                  :disabled="index != Object.keys(form).length - 1"
                  @click="removeResponsible(index)"
                >
                  <b-icon icon="trash" aria-hidden="true"></b-icon>
                </b-button>
              </b-col>
              <b-col sm="8" md="8">
                <b-row>
                  <b-col cols="12">
                    <b-form-group>
                      <label>
                        {{ getTypeResponsible(user.level) }}
                        <span class="text-danger">*</span>
                      </label>
                      <validation-provider
                        #default="{ errors }"
                        rules="required"
                        :name="getTypeResponsible(user.level)"
                      >
                        <v-select 
                          v-model="form[index].id_user"
                          :options="users"
                          :reduce="e => e.id"
                          label="name"
                          @input="validateNoRepeatUser(index)"
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
                <b-row>
                  <b-col cols="12">
                    <b-form-group>
                      <label>
                        Días de notificación antes de fecha de vencimiento
                        <span class="text-danger">*</span>
                      </label>
                      <validation-provider
                        #default="{ errors }"
                        rules="required|min_value:1"
                        name="Días de notificación antes de fecha de vencimiento"
                      >
                        <b-form-input
                          v-model="form[index].days"
                          type="number"
                        ></b-form-input>
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-form-group>
                  </b-col>
                </b-row>
              </b-col>
              <hr>
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
import { required, integer, min_value } from '../../validations'
import { updateActionUser } from '../../../services/actionPlanService'
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
    origin: {
      type: String,
      required: true,
    },
    idSectionRegister: {
      type: Number,
      required: true,
    },
    idActionRegister: {
      type: Number,
      required: true,
    },
    idActionPlan: {
      type: Number,
      required: true,
    },
    action: {
      type: Object,
      required: true,
      default: null
    },
  },
  data() {
    return {
      required,
      integer,
      min_value,
      dialog: false,
      users: [],
      form: []
    }
  },
  computed: {
    titleModal() {
      if (this.action == null) return ''
      const name = getNoRequirementText(this.action)
      return `Responsables para requerimiento ${name}` 
    },
    hasAuditor() {
      if (this.action == null) return false
      const { auditors } = this.action
      return auditors.length != 0
    },
    auditorName() {
      if (this.action == null || this.action.auditors.length == 0) return ''
      const find = this.action.auditors.find((item => item.pivot.level == 1))
      return find === undefined ? '---' : find.person.full_name
    },
    auditorPhoto() {
      if (this.action == null || this.action.auditors.length == 0) return ''
      const find = this.action.auditors.find((item => item.pivot.level == 1))
      if (find.image === null) return ''
      return find === undefined ? '' : find.image.full_path
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
    getTypeResponsible(level) {
      const levels = {
        1: 'Responsable de aprobación',
        2: 'Responsable de aprobación secundario',
        3: 'Responsable de aprobación terciario',
      }
      return levels[level]
    },
    async setUsers() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const { data } = await updateActionUser(this.idAuditProcess, this.idAplicabilityRegister, this.origin, this.idSectionRegister, this.idActionRegister, this.idActionPlan, this.form)
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
      const { full_name } = item.person
      return { id: item.id_user, name: full_name, path: item.image?.full_path ?? '' }
    },
    addResponsible() {
      if (this.form.length == 3) {
        this.alertMessageOk('Solo puede haber máximo tres resposables', 'info')
        return
      }
      const levels = [1, 2, 3]
      const currentLevels = this.form.map(item => item.level)
      const missingLevels = levels.filter(level => !currentLevels.includes(level))
      const missLevel =  Math.min(...missingLevels.map(miss => miss))
      this.form.push({id_user: null, level: missLevel, days: null})
    },
    validateNoRepeatUser(index) {
      const idUser = this.form[index].id_user
      const countUsers = this.form.filter(item => item.id_user == idUser).length
      if (countUsers >= 2) {
        this.form[index].id_user = null
        this.alertMessageOk('No se puede repetir el usuario', 'info')
      }
    },
    removeResponsible(index) {
      this.form.splice(index, 1)
    },
    getImageByUser(idUser) {
      const find = this.users.find(({id}) => id == idUser)
      return find === undefined ? '' : find.path
    },
    reset() {
      this.form = []
      if (this.action.auditors.length == 0) {
        this.form = [{id_user: null, level: 1, days: null}]
        return
      }
      this.action.auditors.forEach(({ id_user, pivot}) => {
        this.form.push({id_user, level: pivot.level, days: pivot.days})
      })
    }
  }
}
</script>

<style>
  
</style>