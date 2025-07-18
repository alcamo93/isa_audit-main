<template>
  <b-modal
    v-model="dialog"
    size="xl"
    :title="titleModal"
    :no-close-on-backdrop="true"
  >
    <loading :show="loadingMixin" />
    <b-container fluid>
      <count-requirement-per-aspect
        :series="series"
      />
    </b-container>
    <template #modal-footer>
      <div class="w-100">
        <b-button 
          variant="danger"
          class="float-right mr-2"
          @click="dialog = false"
        >
          Cerrar
        </b-button>
      </div>
    </template>
  </b-modal>
</template>

<script>
import ButtonGoToSection from '../../components/action_buttons/ButtonGoToSection'
import CountRequirementPerAspect from '../graphics/bar-chart/CountRequirementPerAspect'

export default {
  components: {
    ButtonGoToSection,
    CountRequirementPerAspect
  },
  props: {
    filters: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    titleModal() {
      const { matter_name, corp_tradename, year } = this.filters
      return `Plan de Acción ${matter_name} ${corp_tradename} ${year}`
    },
    series() {
      const matter = this.filters.matters.find(matter => matter.id_matter == this.filters.id_matter)
      if (matter == undefined) return []
       
      const serie = matter.aspects.map(aspect => {
        return { y: aspect.count_total, x: aspect.aspect, fillColor: aspect.color }
      })
      return [{ name: 'Conteo', data: serie }]
    }
  },
  methods: {
    async showModal() {
      this.dialog = true
    },
  }
}
</script>

<style>

</style>