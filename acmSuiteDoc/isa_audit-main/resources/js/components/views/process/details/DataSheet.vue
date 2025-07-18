<template>
  <fragment>
  
    <div v-if="title" class="font-weight-bold text-uppercase py-1">
      {{ title }}
    </div>
    <all-data-card :record="record"/>
    <auditors-card 
      title="Auditores"
      :items="auditors"
    />
    <items-evaluated-card 
      title="Aspectos evaluados"
      :items="matters"
    />
    
  </fragment>
</template>

<script>
import AllDataCard from './cards/AllDataCard.vue'
import AuditorsCard from './cards/AuditorsCard.vue'
import ItemsEvaluatedCard from './cards/ItemsEvaluatedCard.vue'

export default {
  mounted() {
    this.matters = this.getObjectMatter()
    this.auditors = this.getObjectAuditor()
  },
  components: {
    AuditorsCard,
    ItemsEvaluatedCard,
    AllDataCard,
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
      matters: [],
      auditors: []
    }
  },
  methods: {
    getObjectMatter() {
      const iterable = this.record.aplicability_register.contract_matters
      return iterable.map(itemMatter => {
        const aspects =  itemMatter.contract_aspects.map(itemAspect => itemAspect.aspect)
        return { ...itemMatter.matter, aspects }
      })
    },
    getObjectAuditor() {
      const iterable = this.record.auditors
      return iterable.map(itemUser => {
        const { person, pivot } = itemUser
        const type = Boolean(pivot.leader) ? 'Auditor Lider' : 'Auditor'
        const color = Boolean(pivot.leader) ? 'success' : 'primary'
        return { id: itemUser.id_user, name: person.full_name, type, color }
      })
    }
  }
}
</script>

<style>

</style>