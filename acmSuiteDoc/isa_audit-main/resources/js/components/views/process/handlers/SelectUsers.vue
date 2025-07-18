<template>
  <div :class="rowClass">
    <div :class="colClass">
      <b-form-group>
        <label>
          Auditor Lider/Responsable 
          <span class="text-danger">*</span>
        </label>
        <validation-provider
          #default="{ errors }"
          rules="required"
          name="Auditor Lider"
        >
          <v-select 
            autoscroll
            v-model="selected.leader"
            :options="options.leaders"
            @input="mergeBuildUsers"
            :reduce="e => e.id_user"
            label="name"
          >
            <div slot="no-options">
              No se encontraron registros
            </div>
          </v-select>
          <small class="text-danger">{{ errors[0] }}</small>
        </validation-provider>
      </b-form-group>
    </div>
    <div :class="colClass">
      <b-form-group>
        <label>
          Auditores/Responsables
        </label>
        <validation-provider
          #default="{ errors }"
          name="Auditores"
        >
          <multiselect 
            v-model="selected.auditors"
            :options="options.auditors"
            @input="mergeBuildUsers"
            multiple
            placeholder=""
            label="name" 
            track-by="id_user"
            selectLabel="Clic para seleccionar"
            selectedLabel="Seleccionado"
            deselectLabel="Clic para remover"
          >
            <template slot="noOptions">
              No se encontraron registros
            </template>
          </multiselect>
          <small class="text-danger">{{ errors[0] }}</small>
        </validation-provider>
      </b-form-group>
    </div>
  </div>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '../../../validations'
import { getUsersSource } from '../../../../services/catalogService'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    idCorporate: {
      type: Number,
      required: true
    },
    auditors: {
      type: Array,
      required: true
    },
    allowFlex: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data() {
    return {
      required,
      options: {
        all: [],
        leaders: [],
        auditors: [],
      },
      selected: {
        leader: null,
        auditors: [],
      },
    }
  },
  computed: {
    rowClass() {
      return this.allowFlex ? 'd-flex flex-wrap' : 'row'
    },
    colClass() {
      return this.allowFlex ? 'flex-grow-1 px-2 py-2 mx-2 my-1' : 'col-sm-12 col-md-6 col-lg-6'
    }
  },
  watch: {
    idCorporate() {
      this.getUsers()
    },
    auditors(auditors) {
      // set leader
      const leaderSelected = auditors.find(item => item.leader == 1)
      this.selected.leader = leaderSelected?.id_user ?? null
      // set auditors
      this.selected.auditors = auditors.filter(item => item.leader != 1)
    },
    'selected.leader': function() {
      this.setValues()
    },
    'selected.auditors': function() {
      this.setValues()
    }
  },
  methods: {
    async getUsers() {
      try {
        if ( this.idCorporate == 0 ) {
          this.clearComponent()
          return
        }
        const filters = { id_corporate: this.idCorporate }
        const { data }  = await getUsersSource({}, filters)
        this.options.all = data.data.map(user => {
          return { id_user: user.id_user, name: user.person.full_name } 
        })
        this.setValues()
      } catch (error) {
        this.responseMixin(error)
      }
    },
    setValues() {
      this.setValuesForLeader()
      this.setValuesForAuditors()
    },
    setValuesForLeader() {
      if ( this.selected.auditors.length == 0 ) {
        this.options.leaders = this.options.all
        return
      }
      const auditorsUsed = this.selected.auditors.map(item => item.id_user)
      this.options.leaders = this.options.all.filter( item => !auditorsUsed.includes(item.id_user) )
    },
    setValuesForAuditors() {
      if ( !this.selected.leader ) {
        this.options.auditors = this.options.all
        return
      }
      const leaderUsed = this.selected.leader
      this.options.auditors = this.options.all.filter( item =>  item.id_user != leaderUsed )
    },
    mergeBuildUsers() {
      let arrayLeader = []
      if ( this.selected.leader ) {
        const foundLeader = this.options.all.find(item => item.id_user == this.selected.leader)
        arrayLeader.push({ id_user: foundLeader.id_user, name: foundLeader.name, leader: 1 })  
      }
      const arrayAuditors = this.selected.auditors.map(user => { 
        const foundAuditor = this.options.all.find(item => item.id_user == user.id_user)
        return { id_user: foundAuditor.id_user, name: foundAuditor.name, leader: 0 }
      })
      this.$emit( 'update:auditors', arrayLeader.concat(arrayAuditors) ) 
    },
    clearComponent() {
      this.selected.leader = null
      this.selected.auditors = []
      this.options.all = []
      this.options.leaders = null
      this.options.auditors = []
      this.$emit( 'update:auditors', [] ) 
    }
  }
}
</script>

<style>

</style>