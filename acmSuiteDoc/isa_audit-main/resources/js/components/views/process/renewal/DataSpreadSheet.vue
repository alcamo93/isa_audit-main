<template>
  <fragment>

    <b-row cols-sm="1" cols-md="1" cols-lg="2" cols-xl="2">
      <b-col>
        <owner-data-card 
          title="Datos de evaluación"
          :record="record"
        />
      </b-col>
      <b-col>
        <owner-data-card-modify 
          title="Datos de renovación"
          :name.sync="form.audit_processes"
        />
      </b-col>
    </b-row>

    <b-row cols-sm="1" cols-md="1" cols-lg="2" cols-xl="2">
      <b-col>
        <evaluate-data-card 
          title="Opciones a evaluar"
          :record="record"
        />
      </b-col>
      <b-col>
        <evaluate-data-card-modify
          title="Opciones de renovación"
          :evaluate-risk.sync="form.evaluate_risk"
          :evaluate-specific.sync="form.evaluate_especific"
          :date.sync="form.date"
          :keep-risk.sync="form.keep_risk"
          :keep-files.sync="form.keep_files"
        />
      </b-col>
    </b-row>

    <b-row cols-sm="1" cols-md="1" cols-lg="2" cols-xl="2">
      <b-col>
        <auditors-card 
          title="Auditores en la evaluación"
          :items="auditors"
        />
      </b-col>
      <b-col>
        <auditors-card-modify
          title="Auditores para la renovación"
          :id-corporate="idCorporate"
          :auditors.sync="form.auditors"
        />
      </b-col>
    </b-row>

    <b-row cols-sm="1" cols-md="1" cols-lg="2" cols-xl="2">
      <b-col>
        <items-evaluated-card 
          title="Aspectos evaluados"
          :items="matters"
        />
      </b-col>
      <b-col>
        <items-evaluated-card-modify 
          title="Aspectos a renovar"
          :items="allMatters"
          :forms.sync="form.forms"
        />
      </b-col>
    </b-row>
    <div class="d-flex justify-content-end">
      <b-button 
        variant="success"
        @click="setRenewal"
      >
        Renovar
      </b-button>
    </div>
  </fragment>
</template>

<script>
import OwnerDataCard from '../details/cards/OwnerDataCard.vue'
import OwnerDataCardModify from './cards/OwnerDataCard.vue'
import EvaluateDataCard from '../details/cards/EvaluateDataCard.vue'
import EvaluateDataCardModify from './cards/EvaluateDataCard.vue'
import AuditorsCard from '../details/cards/AuditorsCard.vue'
import AuditorsCardModify from './cards/AuditorsCard.vue'
import ItemsEvaluatedCard from '../details/cards/ItemsEvaluatedCard.vue'
import ItemsEvaluatedCardModify from './cards/ItemsEvaluatedCard.vue'
import { todayDate } from "../../components/scripts/dates"
import { getFormsSource} from '../../../../services/catalogService'
import { setRenewal } from '../../../../services/processService'

export default {
  async mounted() {
    await this.getFormsSource()
    this.setValues()
  },
  components: {
    OwnerDataCard,
    OwnerDataCardModify,
    EvaluateDataCard,
    EvaluateDataCardModify,
    AuditorsCard,
    AuditorsCardModify,
    ItemsEvaluatedCard,
    ItemsEvaluatedCardModify
  },
  props: {
    title: {
      type: String,
      required: false,
    },
    record: {
      type: null || Object,
      required: true,
    }
  },
  data() {
    return {
      forms: [],
      auditors: [],
      matters: [],
      allMatters: [],
      idCorporate: 0,
      form: {
        audit_processes: '',
        evaluate_risk: 2,
        evaluate_especific: false,
        date: '', 
        keep_risk: true,
        keep_files: true,
        auditors: [],
        forms: []
      }
    }
  },
  methods: {
    async setRenewal() {
      this.alertMessageOk('Funcionalidad en desarrollo', 'info')
      return 
      try {
        const validate = this.validateData()
        if ( !validate ) {
          this.ok
        }
        this.showLoadingMixin()
        const { data } = await setRenewal(this.record.id_audit_processes, this.form)
        this.responseMixin(data)
        this.$emit('successfully')
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async getFormsSource() {
      try {
        this.showLoadingMixin()
        const { data } = await getFormsSource()
        this.forms = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    setValues() {
      this.reset()
      this.idCorporate = this.record.id_corporate
      this.form.audit_processes = this.record.audit_processes
      this.form.audit_processes = this.record.audit_processes
      this.form.evaluate_risk = this.record.evaluate_risk
      this.form.audit_processes = this.record.audit_processes
      this.form.date = todayDate()
      this.form.forms = this.getForms()
      this.form.auditors = this.getObjectAuditor('normal')
      this.auditors = this.getObjectAuditor('view')
      this.matters = this.buildItemEvaluated()
      this.allMatters = this.buildAllItemEvaluated()

    },
    getForms() {
      return this.record.aplicability_register.contract_matters.flatMap(item => item.contract_aspects)
        .map(item => item.form_id)
    },
    buildItemEvaluated() {
      const iterableMatters = this.record.aplicability_register.contract_matters
      const iterableAspects = this.record.aplicability_register.contract_matters.flatMap(item => item.contract_aspects)
      return this.getObjectMatter(iterableMatters, iterableAspects)
    },
    buildAllItemEvaluated() {
      const iterableMatters = this.forms.filter((item, index, self) => {
        return index === self.findIndex(find => find.matter.id_matter === item.matter.id_matter)
      })
      const iterableAspects = this.forms.map(item => {
        const aspectWithForm = { ...item }
        aspectWithForm.aspect.id_form = item.id
        return aspectWithForm
      })
      return this.getObjectMatter(iterableMatters, iterableAspects)
    },
    getObjectMatter(iterable, childs) {
      return iterable.map(item => {
        const aspects =  childs.filter(child => {
          return child.aspect.id_matter == item.matter.id_matter
        }).map(child => child.aspect)
        return { ...item.matter, aspects }
      })
    },
    getObjectAuditor(type) {
      if (type == 'normal') {
        return this.record.auditors.map(({id_user, person, pivot}) => {
          return { id_user: id_user, name: person.full_name, leader: pivot.leader }
        })
      }
      return this.record.auditors.map(item => {
        const { person, pivot } = item
        const type = Boolean(pivot.leader) ? 'Auditor Lider' : 'Auditor'
        const color = Boolean(pivot.leader) ? 'success' : 'primary'
        return { id: item.id_user, name: person.full_name, type, color }
      })
    },
    async validateData() {
      const validate = { isValid: true, message: 'Información validada' }
      const isValid = await this.$refs.rulesForm.validate()
      if (!isValid) {
        validate.isValid = false 
        validate.message = 'Revisa los campos que son obligatorios'
        return validate
      }
      const hasAuditors = this.form.auditors.length
      if (!hasAuditors) {
        validate.isValid = false 
        validate.message = 'Debes tener por lo menos un auditor lider'
        return validate
      }
      const hasForms = this.form.forms.length
      if (!hasForms) {
        validate.isValid = false 
        validate.message = 'Debes tener al menos un aspecto a evaluar'
        return validate
      }
    },
    reset() {
      this.form.audit_processes = ''
      this.form.evaluate_risk = 2
      this.form.evaluate_especific = false
      this.form.date = '' 
      this.form.keep_risk = true
      this.form.keep_files = true
      this.form.auditors = []
      this.form.forms = []
    }
  }
}
</script>

<style>

</style>