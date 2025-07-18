<template>
  <fragment>
    <loading :show="loadingMixin" />

    <div v-if="no_content" class="content space-detail">
      <div class="container">
        <b-card class="text-center no-borde">
          <b-card-header>
            <b-row class="justify-content-center">
              <b-col sm="12" md="12" lg="12">
                <div class="header-text">
                  <h2 class="text-dark font-weight-bold">{{ message_page }}</h2>
                </div>
              </b-col>
            </b-row>
          </b-card-header>
        </b-card>
      </div>
    </div>

    <div v-else class="content space-detail">
      <div class="container">
        <b-card class="text-center no-borde">
          <b-card-header>
            <b-row class="justify-content-center">
              <b-col sm="12" md="12" lg="12">
                <div class="header-text">
                  <h2 class="text-dark font-weight-bold">{{ title_page }}</h2>
                  <h4 class="text-dark font-weight-bold font-italic">{{ title_detail }}</h4>
                  <hr />
                </div>
              </b-col>
            </b-row>
          </b-card-header>
          <b-card-body class="p-0">
            
            <b-row v-for="detail in details" :key="detail.id" class="justify-content-center mb-1">
              <b-card class="no-borde p-0">
                <b-card-body class="text-dark font-weight-bold p-0">
                  <h5 class="font-weight-bold mb-3">{{ detail.title }}</h5>
                  <h5 v-if="detail.subtitle" class="font-weight-bold font-italic mb-3">{{ detail.subtitle }}</h5>
                  <rich-text-edit v-if="render_type == 'rich_text'"
                    :initial-content="detail.description"
                    :disabled="true"
                    :onlyPresentation="true"
                  />
                  <b-img v-if="render_type == 'image'"
                    thumbnail fluid 
                    :src="detail.description" 
                    :alt="detail.title">
                  </b-img>
                  <p v-if="render_type == 'default'" class="text-justify">{{ detail.description }}</p>
                </b-card-body>
              </b-card>
            </b-row>
            
          </b-card-body>
        </b-card>
      </div>
    </div>
    
  </fragment>
</template>

<script>
import RichTextEdit from '../components/rich_text/RichTextEdit'
import { getDataRenderDetail } from '../../../services/detailService'

export default {
  mounted() {
    this.message_page = 'Buscando contenido...'
    this.getDataRenderDetail()
  },
  props: {
    items: {
      type: String,
      required: true
    },
    data: {
      type: String,
      required: true
    }
  },
  components: { 
    RichTextEdit 
  },
  data() {
    return {
      no_content: true,
      message_page: '',
      title_page: '',
      title_detail: '',
      render_type: 'default',
      details: []
    }
  },
  methods: {
    async getDataRenderDetail() {
      try {
        this.showLoadingMixin()
        const { data } = await getDataRenderDetail(this.items, this.data)
        const { title_page, title_detail, render_type, details } = data.data
        this.title_page = title_page
        this.title_detail = title_detail
        this.render_type = render_type
        this.details = details
        this.no_content = false
        this.showLoadingMixin()
      } catch (error) {
        this.message_page = 'No se encontro ningun contenido'
        this.no_content = true
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
  }
}
</script>

<style scoped>
.content.space-detail div {
  background-color: transparent !important;
}
.space-detail {
  padding-top: 5vh !important;
}
.no-borde {
  border: none !important;
}

hr {
  margin-top: 1rem;
  margin-bottom: 1rem;
  border: 0;
  border-top: 1px solid #212529;
}
</style>