<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <template v-slot:action>
          <b-button
            class="float-right mt-2 mr-2"
            variant="success"
            @click="backAspects"
          >
            Regresar
          </b-button>
        </template>
        <filters
          :info="info"
          :total-progress="progress.total"
          @filterSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <b-card-text>
          <b-row>
            <b-col v-if="showLegend">
              <p v-if="isFinish" class="font-weight-bold">
                El aspecto has sido 
                <b-badge :variant="progress.status.color">{{ progress.status.status }}</b-badge> 
                no es permitido modificar las respuestas establecidas
              </p>
              <p v-else class="font-weight-bold">
                El aspecto ha sido
                <b-badge :variant="progress.status.color">{{ progress.status.status }}</b-badge> 
                todos los registros han sido evaluados
              </p>
            </b-col>
            <b-col>
              <b-button v-if="showFinishButton"
                class="float-right mr-1"
                variant="success"
                @click="completeAspect"
              >
                <b-icon icon="check-circle-fill" aria-hidden="true"></b-icon>
                Finalizar Aspecto
              </b-button>
            </b-col>
          </b-row>
          <b-table-simple class="group-table" hover responsive>
            <b-thead>
              <b-tr>
                <b-th class="text-center" 
                  v-for="field in headerTable" :key="field.key"
                >
                  {{ field.label }}
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="!groupList.length">
                <b-tr class="row-group">
                  <b-td class="text-center" :colspan="headerTable.length">
                    No hay registros que mostrar
                  </b-td>
                </b-tr>
              </template>
              <template v-else>
                <template v-for="aspect in groupList">
                  <b-tr 
                    :key="getKeyGroupLibrary(aspect, 'aspect')" 
                    class="row-group row-level-0"
                  >
                    <b-td class="text-center" :colspan="headerTable.length">
                      {{ getLabelGroup(aspect, 'aspect') }}
                    </b-td>
                  </b-tr>
                  <template v-for="requirement in aspect">  
                    <b-tr 
                      :key="getKeyGroupLibrary(requirement, 'requirement')" 
                      class="row-group row-level-1"
                    >
                      <b-td class="text-center" :colspan="headerTable.length">
                        {{ getLabelGroup(requirement, 'requirement') }}
                      </b-td>
                    </b-tr>
                    <maintable v-for="row in requirement" :key="`row-${row.id}`"
                      :row="row"
                      :params="params"
                      :evaluate-risk="evaluateRisk"
                      :colspan="headerTable.length"
                      @successfully="getRecords"
                      @handlerCollapse="collapseRow"
                    />
                  </template>
                </template>  
              </template>
            </b-tbody>
          </b-table-simple>
          <!-- Paginator -->
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
          <!-- End Paginator -->
        </b-card-text>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea.vue'
import AppPaginator from '../../components/app-paginator/AppPaginator.vue'
import Filters from './Filters.vue'
import Maintable from './rows/Maintable.vue'
import { getAudits } from '../../../../services/AuditService'
import { completeAuditAspect } from '../../../../services/AuditAspectService'
import { groupItems, getLabelGroup, getKeyGroupLibrary } from '../../components/scripts/texts'
  
export default {
  components: {
    FilterArea,
    AppPaginator,
    Filters,
    Maintable
  },
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Auditoría`
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
    idAuditRegister: {
      type: Number,
      required: true
    },
    idAuditRegister: {
      type: Number,
      required: true
    },
    idAuditMatter: {
      type: Number,
      required: true
    },
    idAuditAspect: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        several_no_requirement: null,
        requirement: null,
        document: null,
        subrequirement: null,
        id_evidence: null,
        id_condition: null,
        answer: null,
        complete: null
      },
      progress: {
        total: 0,
        status: {
          id_status: 0,
          key: '',
        },
      },
      info: {
        audit_process: '----',
        customer_name: '----',
        corporate_name: '----',
        evaluate_risk: false,
      }
    }
  },
  watch: {
    'paginate.page': function() {
      this.getRecords()
    },
  },
  computed: {
    params() {
      return {
        idAuditProcess: this.idAuditProcess,
        idAplicabilityRegister: this.idAplicabilityRegister,
        idAuditRegister: this.idAuditRegister,
        idAuditMatter: this.idAuditMatter,
        idAuditAspect: this.idAuditAspect,
      }
    },
    isFinish() {
      return this.progress.status.key == 'FINISHED_AUDIT_AUDIT'
    },
    showFinishButton() {
      return this.progress.status.key == 'NOT_AUDITED_AUDIT' || this.progress.status.key == 'AUDITING_AUDIT'
    },
    showLegend() {
      return this.progress.status.key == 'FINISHED_AUDIT_AUDIT' || this.progress.status.key == 'AUDITED_AUDIT'
    },
    evaluateRisk() {
      return this.info.evaluate_risk
    },
    groupList() {
      return this.groupItems(this.items)
    },
    headerTable() {
      return [
        {
          key: 'no_requirement',
          label: 'No. Requerimiento'
        },
        {
          key: 'requirement',
          label: 'Requerimiento'
        },
        {
          key: 'evidence',
          label: 'Tipo de Evidencia'
        },
        {
          key: 'document',
          label: 'Documento en Especifico'
        },
        {
          key: 'description',
          label: 'Descripción'
        },
        {
          key: 'condition',
          label: 'Condición'
        },
        {
          key: 'application_type',
          label: 'Competencia'
        },
        {
          key: 'risk',
          label: 'Nivel de Riesgo'
        },
        {
          key: 'information',
          label: 'Información'
        },
      ]
    },
  },
  methods: {
    // external methods
    groupItems, 
    getLabelGroup, 
    getKeyGroupLibrary,
    // local methods
    async getRecords() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        this.items = [] // NOTE: BUG MAP DOOM ELEMENTS 
        const { data } = await getAudits(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister, this.idAuditMatter, this.idAuditAspect, params, this.filters)
        this.items = data.data.map( item => ({ ...item, actions: { show_details: item.requirement.has_subrequirement, opened: true } }) )
        this.setInfo(data.info)
        this.setProgress(data.progress)
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async completeAspect() {
      try {
        this.showLoadingMixin()
        const { data } = await completeAuditAspect(this.idAuditProcess, this.idAplicabilityRegister, this.idAuditRegister, this.idAuditMatter, this.idAuditAspect)
        await this.getRecords()
        this.showLoadingMixin()
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    collapseRow(id) {
      const index = this.items.findIndex(item => item.id === id)
      this.$set(this.items[index].actions, 'opened', !this.items[index].actions.opened)
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    async setFilters({ several_no_requirement, requirement, document, subrequirement, id_evidence, 
        id_condition, answer, complete }) {
      this.filters.several_no_requirement = several_no_requirement
      this.filters.requirement = requirement
      this.filters.document = document
      this.filters.subrequirement = subrequirement
      this.filters.id_evidence = id_evidence
      this.filters.id_condition = id_condition
      this.filters.answer = answer
      this.filters.complete = complete
      await this.getRecords()
    },
    setInfo({ audit_process, customer_name, corporate_name, evaluate_risk }) {
      this.info.audit_process = audit_process
      this.info.customer_name = customer_name
      this.info.corporate_name = corporate_name
      this.info.evaluate_risk = evaluate_risk
    },
    setProgress({ aspect_total_progress, aspect_status }) {
      this.progress.total = aspect_total_progress
      this.progress.status.status = aspect_status.status
      this.progress.status.key = aspect_status.key
      this.progress.status.color = aspect_status.color
      console.log('aqieo', this.progress)
    },
    backAspects() {
      window.location.href = `/v2/process/${this.idAuditProcess}/applicability/${this.idAplicabilityRegister}/audit/${this.idAuditRegister}/view`
    }
  }
}
</script>

<style scoped>

</style>