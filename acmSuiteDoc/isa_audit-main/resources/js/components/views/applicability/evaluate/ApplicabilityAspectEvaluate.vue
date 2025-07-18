<template>
  <fragment>
    <loading :show="loadingMixin" />
    <div class="d-flex">
      <filter-area class="flex-fill" opened>
        <template v-slot:action>
          <b-button
            size="sm"
            class="mx-1"
            variant="success"
            @click="backAspects"
          >
            Regresar
          </b-button>
        </template>
        <filters 
          :info="info"
        />  
      </filter-area>
    </div>
    <b-card>
      <template #header>
        <toolbar-wizard 
          :total-questions="totalQuestions"
          :index.sync="questionIndex"
          :show-vertical.sync="showVertical"
          :complete="info.complete"
          :key-status="info.status.key"
          @classify="classifyAspect"
          @sync="syncAspect"
        />
      </template>
      <b-tabs class="acm-suite-wizard" :vertical="showVertical" fill pills v-model="questionIndex">
        <b-tab v-for="question, index in questions" :key="question.id"
          :title-link-class="getKeysPerQuestion(question.evaluate)"
          :title="(index + 1).toString()"
        >
          <question-card 
            :idAuditProcess="idAuditProcess"
            :idAplicabilityRegister="idAplicabilityRegister"
            :idContractMatter="idContractMatter"
            :idContractAspect="idContractAspect"
            :idEvaluateQuestion="question.id"
            :evaluates="question.evaluate"
            :question="question.question"
            :applicability="question.applicability"
            :comment="question.comment?.comment ?? ''"
            @successfully="getRecords"
          />
        </b-tab>
        <template #empty>
          <div class="text-center text-muted">
            No hay preguntas aún
          </div>
        </template>
      </b-tabs>
    </b-card>
  </fragment>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea.vue'
import Filters from './Filters.vue'
import QuestionCard from './question/QuestionCard.vue'
import ToolbarWizard from './ToolbarWizard.vue'
import { getAplicability } from '../../../../services/applicabilityService'
import { completeContractAspect, syncContractAspect } from '../../../../services/contractAspectService'

export default {
  components: {
    FilterArea,
    Filters,
    ToolbarWizard,
    QuestionCard
  },
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Aplicabilidad`
    this.getRecords()
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true
    },
    idAplicabilityRegister: {
      type: Number,
      required: true
    },
    idContractMatter: {
      type: Number,
      required: true
    },
    idContractAspect: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      info: {
        complete: false,
        customer_name: '----',
        corporate_name: '----',
        audit_process: '----',
        scope: '----',
        application_type: '----',
        matter: '----',
        aspect: '----',
        status: {
          status: '----',
          color: 'light',
          key: 'NOT_CLASSIFIED_APLICABILITY'
        }
      },
      questionIndex: 0,
      showVertical: false,
      questions: [],
    }
  },
  computed: {
    totalQuestions() {
      return this.questions.length
    },
  },
  methods: {
    async getRecords() {
      try {
        this.showLoadingMixin()
        const { data } = await getAplicability(this.idAuditProcess, this.idAplicabilityRegister, this.idContractMatter, this.idContractAspect)
        this.questions = data.data
        const { customer_name, corporate_name, audit_process, scope, application_type, matter, aspect, aspect_status, complete } = data.info
        this.info.complete = complete
        this.info.customer_name = customer_name
        this.info.corporate_name = corporate_name
        this.info.audit_process = audit_process
        this.info.scope = scope
        this.info.application_type = application_type
        this.info.matter = matter
        this.info.aspect = aspect
        this.info.status.status = aspect_status.status
        this.info.status.color = aspect_status.color
        this.info.status.key = aspect_status.key
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async classifyAspect() {
      try {
        this.showLoadingMixin()
        const { data } = await completeContractAspect(this.idAuditProcess, this.idAplicabilityRegister, this.idContractMatter, this.idContractAspect)
        console.table(data.data.logs)
        this.alertMessageOk(data.data.message, 'success')
        this.getRecords()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async syncAspect() {
      try {
        this.showLoadingMixin()
        const { data } = await syncContractAspect(this.idAuditProcess, this.idAplicabilityRegister, this.idContractMatter, this.idContractAspect)
        this.alertMessageOk(data.message, 'success')
        this.getRecords()
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    getKeysPerQuestion(evaluate) {
      return evaluate.map(item => item.key)
    },
    backAspects() {
			window.location.href = `/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/view`
		}
  }
}
</script>

<style>

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  color: #333333 !important;
  font-weight: bolder !important;
  background-color: #d0cece !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link:hover {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #afabab !important;
  color: #333333 !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.active {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #afabab !important;
  color: #333333 !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.error {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #ff0000 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.error:hover {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #900 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.active.error {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #900 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.complete {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #15578d !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.complete:hover {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.active.complete {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.blocked {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #15578d !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.blocked:hover {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.nav-fill .nav-item .nav-link.active.blocked {
  padding: 0px 0px !important;
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}


/* vertical */

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link {
  border-radius: 0 !important;
  color: #333333 !important;
  font-weight: bolder !important;
  background-color: #d0cece !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link:hover {
  border-radius: 0 !important;
  background-color: #afabab !important;
  color: #333333 !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.active {
  border-radius: 0 !important;
  background-color: #afabab !important;
  color: #333333 !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.error {
  border-radius: 0 !important;
  background-color: #ff0000 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.error:hover {
  border-radius: 0 !important;
  background-color: #900 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.active.error {
  border-radius: 0 !important;
  background-color: #900 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.complete {
  border-radius: 0 !important;
  background-color: #15578d !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.complete:hover {
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.active.complete {
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.blocked {
  border-radius: 0 !important;
  background-color: #15578d !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.blocked:hover {
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

.tabs.acm-suite-wizard .nav.nav-pills.flex-column .nav-item .nav-link.active.blocked {
  border-radius: 0 !important;
  background-color: #203864 !important;
  color: #FFFFFF !important;
  font-weight: bolder !important;
}

</style>