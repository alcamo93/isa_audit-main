<template>
  <fragment>
    <div class="d-flex flex-wrap justify-content-between">
      <div>
        <h5 class="font-weight-bold mt-1">
          {{ titleSection }}
        </h5>
      </div>
      <div>
        <b-button class="mx-2" 
          variant="success"
          v-b-tooltip.hover.left
          title="Agregar responsable"
          @click="addResponsible"
        >
          Agregar responsable 
          <b-icon icon="plus" aria-hidden="true"></b-icon>
        </b-button>
      </div>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between" v-for="(user, index) in form" :key="user.level">
      <div class="py-1 px-3 d-flex flex-column align-content-center">
        <b-avatar 
          variant="primary" 
          :src="getImageByUser(user.id_user)"
          size="5rem"
        ></b-avatar>
      </div>
      <div class="py-1 px-3 flex-fill">
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
      </div>
      <div v-if="user.level != 1" class="p-3 d-flex flex-column align-content-center">
        <b-button
          v-b-tooltip.hover.left
          :title="`Remover ${getTypeResponsible(user.level)}`"
          variant="danger"
          class="mt-2"
          :disabled="index != Object.keys(form).length - 1"
          @click="removeResponsible(index)"
        >
          <b-icon icon="trash" aria-hidden="true"></b-icon>
        </b-button>
      </div>
      <hr>
    </div>

  </fragment>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { required } from '../../../validations'
import { getSingleProcess } from '../../../../services/catalogSingleService'

export default {
  mounted() {
    this.getProcess()
    this.reset()
  },
  components: {
    ValidationProvider,
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    auditors: {
      type: Array,
      required: true,
      default: null
    },
    isMain: {
      type: Boolean,
      required: true
    },
  },
  data() {
    return {
      required,
      users: [],
      form: []
    }
  },
  watch: {
    form: {
      handler() {
        this.$emit('update:auditors', this.form)
      },
      deep: true
    },
  },
  computed: {
    typeTask() {
      return this.isMain ? 'Tarea' : 'Subtarea'
    },
    titleSection() {
      return `Resposanbles de ${this.typeTask}`
    }
  },
  methods: {
    async getProcess() {
      try {
        this.showLoadingMixin()
        const params = { scope: 'users' }
        const { data } = await getSingleProcess(this.idAuditProcess, params)
        const { users } = data.data.corporate
        this.users = users.map(item => this.getAuditors(item))
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getAuditors(item) {
      return { id: item.id_user, name: item.person.full_name, path: item.image?.full_path }
    },
    getTypeResponsible(level) {
      const levels = {
        1: `Responsable de ${this.typeTask}`,
        2: `Responsable de ${this.typeTask} secundario`,
        3: `Responsable de ${this.typeTask} terciario`,
      }
      return levels[level]
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
      this.form.push({id_user: null, level: missLevel})
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
      return find?.path ?? ''
    },
    reset() {
      if (this.auditors.length == 0) {
        this.form = [{id_user: null, level: 1}]
        return
      }
      this.auditors.forEach(({ id_user, pivot }) => {
        this.form.push({id_user, level: pivot.level})
      })
    }
  }
}
</script>

<style>

</style>