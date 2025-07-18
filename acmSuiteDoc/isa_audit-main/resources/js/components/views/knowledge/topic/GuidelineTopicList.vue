<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <b-card>
        <b-row class="d-flex justify-content-end">
          <b-button class="mt-2 mr-2" variant="success" @click="goToKnowledge">
            Regresar
          </b-button>
        </b-row>
      </b-card>
      <b-card>
        <b-card-text>
          <b-table responsive striped hover show-empty empty-text="No hay registros que mostrar" :fields="headerTable"
            :items="items">
            <template #cell(guideline)="data">
              <b-col cols="12" class="align-items-center topics-color">
                <div class="col-12 text-center">
                  <b-icon class="float-left mt-2 mr-2" icon="file-earmark-text-fill" aria-hidden="true"></b-icon>
                  {{ convertToUppercase(data.item.guideline) }}
                </div>
                </br>
                <b-row class="text-center">
                  <b-col cols="3">
                    COMPETENCIA
                    <div>{{ convertToUppercase(data.item.application_type.application_type) }}</div>
                  </b-col>
                  <b-col cols="2">
                    MATERIA
                    <div v-if="data.item.aspects.length > 0">{{ data.item.aspects[0].matter.matter }}</div>
                  </b-col>
                  <b-col cols="2">
                    ASPECTO
                    <template v-if="data.item.aspects.length > 0">
                      <div v-for="(aspect) in data.item.aspects">
                        <span>&#8226; </span>{{ aspect.aspect }}<br>
                      </div>
                    </template>
                  </b-col>
                  <b-col cols="2">
                    TIPO
                    {{ convertToUppercase(data.item.legal_classification.legal_classification) }}
                  </b-col>
                  <b-col cols="3">
                    ULTIMA REFORMA:
                    {{ data.item.last_date_format_text }}
                  </b-col>
                </b-row>
                </br>
                <b-col cols="12" class="text-justify">
                  {{ convertToUppercase(data.item.objective) }}
                </b-col>
              </b-col>
              </br>
              <b-col cols="12">
                <b-button class="float-left mt-2 mr-2" variant="info" @click="goToArticles(data.item)">
                  VER
                </b-button>
              </b-col>
            </template>
          </b-table>
          <!-- Paginator -->
          <app-paginator :data-list="paginate" @pagination-data="changePaginate" />
          <!-- End Paginator -->
        </b-card-text>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import FilterArea from '../../components/slots/FilterArea'
import AppPaginator from '../../components/app-paginator/AppPaginator'
import { getKnowledgeTopic } from '../../../..//services/knowledgeTopicService'

export default {
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Biblioteca jurídica -Temas`
    this.getKnowledgeTopic()
  },
  props: {
    idTopic: {
      type: Number,
      required: true
    }
  },
  components: {
    FilterArea,
    AppPaginator,
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
      },
    }
  },
  watch: {
    'paginate.page': function () {
      this.getKnowledgeTopic()
    },
  },
  computed: {
    headerTable() {
      return [
        {
          key: 'guideline',
          label: this.getTopicName(),
          class: 'text-center',
          sortable: false,
        },
      ]
    },
  },
  methods: {
    async getKnowledgeTopic() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getKnowledgeTopic(this.idTopic, params, this.filters)
        this.items = data.data
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
    async setFilters({ legal_basis, legal_quote }) {
      this.filters.legal_basis = legal_basis
      this.filters.legal_quote = legal_quote
      await this.getKnowledgeTopic()
    },
    goToArticles({ id_guideline }) {
      window.location.href = `/v2/catalogs/guideline/${id_guideline}/article/view`
    },
    goToKnowledge() {
      window.location.href = '/v2/knowledge/view'
    },
    getTopicName() {
      const firstGuideline = this.items[0];
      if (firstGuideline) {
        const topic = firstGuideline.topics.find(t => t.id === this.idTopic);
        if (topic) {
          return topic.name;
        }
      }
    },
    convertToUppercase(text) {
      return text != null ? text.toUpperCase() : '';
    }
  }
}
</script>

<style>
.topics-color {
  font-weight: bolder;
  color: #010666;
}
</style>