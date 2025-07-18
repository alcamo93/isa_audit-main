<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <template v-slot:action>
          <b-button class="float-right mt-2 mr-2" variant="success" @click="goToKnowledge">
            Regresar
          </b-button>
        </template>
        <filters :id-guideline="idGuideline" @fieldSelected="setFilters" />
      </filter-area>
      <b-card>
        <b-row class="text-center text-legal-basis">
          <b-col cols="3">
            COMPETENCIA
            <div>{{ convertToUppercase(competence) }}</div>
          </b-col>
          <b-col cols="2">
            MATERIA
            <div>{{ convertToUppercase(matter) }}</div>
          </b-col>
          <b-col cols="2">
            ASPECTO
            <template v-if="this.items[0]?.guideline?.aspects.length > 0">
              <div v-for="(aspect) in this.items[0]?.guideline?.aspects">
                <span>&#8226; </span>{{ convertToUppercase(aspect.aspect) }}<br>
              </div>
            </template>
          </b-col>
          <b-col cols="2">
            TIPO
            {{ convertToUppercase(type) }}
          </b-col>
          <b-col cols="3">
            ULTIMA REFORMA:
            {{ last_date }}
          </b-col>
        </b-row>
        <b-row class="d-flex justify-content-center">
          <b-button v-if="arrayDownloads.length" @click="showDownloadsModal" variant="success" class="mt-2 mr-2">
            <b-icon icon="file-earmark-arrow-down-fill">
            </b-icon>
            Descargar seleccionados
          </b-button>
        </b-row>
      </b-card>
      <b-card>
        <b-card-text>
          <b-table responsive striped hover show-empty empty-text="No hay registros que mostrar" :fields="headerTable"
            :items="items">
            <template #cell(legal_basis)="data">
              <div class="d-flex align-items-center justify-content-between w-100">
                <b-icon class="text-legal-basis" icon="file-earmark-text-fill" aria-hidden="true">
                </b-icon>
                <b-button v-b-toggle="'collapse-' + data.index" variant="link" class="text-legal-basis">
                  {{ data.item.legal_basis }}
                </b-button>
                <b-form-checkbox v-model="selectedIds" :value="data.item.id_legal_basis"
                  @change="handleDownload(data.item.id_legal_basis, data.item.legal_basis, data.item.legal_quote_env)"
                  title="Agregar a descarga">
                  <b-icon icon="file-earmark-arrow-down-fill">
                  </b-icon>
                  Agregar a descarga
                </b-form-checkbox>
              </div>
              <b-collapse :id="'collapse-' + data.index" class="mt-2" :visible="isOpen">
                <rich-text-edit :initial-content="data.item.legal_quote_env" :disabled="true" :onlyPresentation="true" />
              </b-collapse>
            </template>
          </b-table>
          <!-- Paginator -->
          <app-paginator :data-list="paginate" @pagination-data="changePaginate" />
          <!-- End Paginator -->
          <modal-download ref="modalDownload" :record="downloads" />
        </b-card-text>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import Filters from './Filters'
import AppPaginator from '../../components/app-paginator/AppPaginator'
import RichTextEdit from '../../components/rich_text/RichTextEdit'
import { getLegalBasis } from '../../../../services/legalBasiService'
import ModalDownload from './ModalDownload'

export default {
  mounted() {
    this.getLegalBasis()
  },
  props: {
    idGuideline: {
      type: Number,
      required: true
    }
  },
  components: {
    FilterArea,
    Filters,
    AppPaginator,
    RichTextEdit,
    ModalDownload
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
        legal_basis: null,
        legal_quote: null,
      },
      isOpen: true,
      competence: '',
      matter: '',
      aspect: '',
      type: '',
      date: '',
      last_date: '',
      selectedIds: [],
      arrayDownloads: [],
      downloads: ''
    }
  },
  watch: {
    'paginate.page': function () {
      this.getLegalBasis()
    },
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'legal_basis',
          label: this.getGuidelineName(),
          class: 'text-center',
          sortable: false,
        },
      ]
    },
  },
  methods: {
    async getLegalBasis() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getLegalBasis(this.idGuideline, params, this.filters)
        this.items = data.data
        document.querySelector('#titlePage').innerHTML = this.items[0]?.guideline?.guideline
          ? `Fundamentos Legales - ${this.items[0].guideline.guideline}`
          : `Fundamentos Legales`
        this.setGuidelineData()
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    changePaginate(paginateData) {
      const { perPage, page } = paginateData
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    getGuidelineName() {
      return this.items[0]?.guideline?.guideline || '';
    },
    goToKnowledge() {
      window.location.href = '/v2/knowledge/view'
    },
    setGuidelineData() {
      const item = this.items[0]?.guideline || null;
      if (item) {
        this.competence = item.application_type.application_type,
          this.matter = item.aspects.length != 0 ? item.aspects[0].matter.matter : ''
        this.aspect = item.another_field
        this.type = item.legal_classification.legal_classification
        this.date = item.last_date_format_text
        this.last_date = item.last_date_format_text
      }
    },
    async setFilters({ legal_basis, legal_quote }) {
      this.filters.legal_basis = legal_basis
      this.filters.legal_quote = legal_quote
      await this.getLegalBasis()
    },
    convertToUppercase(text) {
      return text != null ? text.toUpperCase() : ''
    },
    handleDownload(id, title, text) {
      if (!this.selectedIds.includes(id)) {
        this.arrayDownloads = this.arrayDownloads.filter(item => item.id !== id)
      } else {
        this.arrayDownloads.push({
          'id': id,
          'title': title,
          'text': text
        })
      }
      this.downloads = ''
      this.arrayDownloads.forEach(({ title, text }) => {
        this.downloads = this.downloads + '<p><strong>' + title + ':</strong></p>' + text
      })
    },
    showDownloadsModal() {
      this.$refs.modalDownload.showModal();
    },
  }
}
</script>

<style>
.text-legal-basis {
  color: #010666 !important;
  font-weight: bolder;
}
</style>