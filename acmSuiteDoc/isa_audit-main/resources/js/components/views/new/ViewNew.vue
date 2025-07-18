<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <b-card>
        <rich-text-edit :initial-content="newData" disabled onlyPresentation />
        <b-button class="float-right mt-2 mr-2" variant="primary" @click="goToKnowledge">
          Regresar
        </b-button>
      </b-card>
      <b-card>
        <b-row class="justify-content-center text-center title-banner form-group">
          HISTORIAL DE NOTICIAS
        </b-row>
        <banner-news />
      </b-card>
    </b-col>
  </b-row>
</template>

<script>

import BannerNews from './BannerNews'
import RichTextEdit from '../components/rich_text/RichTextEdit'
import { getNew } from '../../../services/newService'

export default {
  mounted() {
    this.getNew()
  },
  props: {
    idNew: {
      type: Number,
      required: true
    }
  },
  components: {
    RichTextEdit,
    BannerNews,
  },
  data() {
    return {
      new: []
    }
  },
  computed: {
    newData() {
      return this.new.description_env
    }
  },
  methods: {
    async getNew() {
      try {
        this.showLoadingMixin()
        const params = {}
        const { data } = await getNew(this.idNew, params, '')
        this.new = data.data
        document.querySelector('#titlePage').innerHTML = `<p class="new-headline">` + this.new.headline + `</p>`
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    goToKnowledge() {
      window.location.href = '/v2/knowledge/view'
    },
  }
}

</script>

<style>
.new-headline {
  font-weight: bolder;
  color: #010666;
}

.title-banner {
  font-weight: bolder;
}
</style>