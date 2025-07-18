<template>
  <div class="mb-2">
    <loading :show="loadingMixin" />
    <b-row>
      <b-col sm="12" md="12">
        <label>
          Marco jurídico (Ley, Reglamento, Norma, etc)
        </label>
        <div class="font-weight-bold">
          {{ guidelineName }}
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="4">
        <label>
          Siglas
        </label>
        <div class="font-weight-bold">
          {{ initialsName }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Clasificación
        </label>
        <div class="font-weight-bold">
          {{ classificationName }}
        </div>
      </b-col>
      <b-col sm="12" md="4">
        <label>
          Competencia
        </label>
        <div class="font-weight-bold">
          {{ applicationTypeName }}
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col sm="12" md="6">
        <label>Artículo</label>
        <b-form-input
          v-model="filters.legal_basis"
          placeholder="Búsqueda por Artículo"
          debounce="500"
        ></b-form-input>
      </b-col>
      <b-col sm="12" md="6">
        <label>Cita Legal</label>
        <b-form-input
          v-model="filters.legal_quote"
          placeholder="Búsqueda por Cita Legal"
          debounce="500"
        ></b-form-input>
      </b-col>
    </b-row>
    <b-row>

    </b-row>

  </div>
</template>

<script>
import { getGuideline } from '../../../../../services/guidelineService'

export default {
  async mounted() {
    await this.getGuideline()
  },
  props: {
    idGuideline: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      guideline: null,
      filters: {
        legal_basis: null,
        legal_quote: null,
      },
    }
  },
  watch: {
    'filters.legal_basis': function() {
      this.$emit('fieldSelected', this.filters)
    },
    'filters.legal_quote': function() {
      this.$emit('fieldSelected', this.filters)
    },
  },
  computed: {
    guidelineName() {
      if (this.guideline == null) return ''
      const { guideline } = this.guideline
      return guideline
    },
    initialsName() {
      if (this.guideline == null) return ''
      const { initials_guideline } = this.guideline
      return initials_guideline
    },
    classificationName() {
      if (this.guideline == null) return ''
      const { legal_classification } = this.guideline.legal_classification
      return legal_classification
    },
    applicationTypeName() {
      if (this.guideline == null) return ''
      const { application_type } = this.guideline.application_type
      return application_type
    },
  },
  methods: {
    async getGuideline() {
      try {
        this.showLoadingMixin()
        const { data } = await getGuideline(this.idGuideline)
        this.guideline = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
  }
}
</script>

<style>

</style>