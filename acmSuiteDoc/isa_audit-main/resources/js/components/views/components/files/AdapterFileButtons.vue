<template>
  <file-buttons
    @successfully="reloadParent"
    :origin="origin"
    :parent-record="{
      id_audit_process: idAuditProcess,
      id_aplicability_register: idAplicabilityRegister,
      id_section_register: idSectionRegister,
      id_requirement: idRequirement,
      id_subrequirement: idSubrequirement,
      library: library,
      requirement: requirement,
      subrequirement: subrequirement,
      id_task: idTask,
    }"
    :show-library="showLibrary"
    :evaluateable-id="evaluateableId"
    :permissions="permissions"
  />
</template>

<script>
import FileButtons from './FileButtons.vue'

export default {
  components: {
    FileButtons
  },
  props: {
    idAuditProcess: {
      type: Number,
      required: true,
    },
    idAplicabilityRegister: {
      type: Number,
      required: true
    },
    idSectionRegister: {
      type: Number,
      required: true
    },
    item: {
      type: Object,
      required: true
    },
    origin: {
      type: String,
      required: false,
      validator: value => {
        const types = ['Obligation', 'Task']
        return types.indexOf(value) !== -1
      }
    },
    showLibrary: {
      type: Boolean,
      required: false,
      default: true,
    },
    evaluateableId: {
      type: Number,
      required: false,
      default: 0
    },
  },
  computed: {
    record() {
      if (this.origin == 'Task') {
        return this.item.action
      }
      return this.item
    },
    hasLibrary() {
      return this.item.evaluates.length > 0
    },
    idRequirement() {
      return this.record.id_requirement
    },
    idSubrequirement() {
      return this.record.id_subrequirement
    },
    requirement() {
      return this.record.requirement
    },
    subrequirement() {
      return this.record.subrequirement
    },
    library() {
      return this.hasLibrary ? this.item.evaluates[0].library : null
    },
    idTask() {
      if (this.origin == 'Task') {
        return this.item.id_task
      }
      return null
    },
    permissions() {
      const { can_approve, can_upload } = this.item.permissions 
      return { can_approve, can_upload }
    },
  },
  methods: {
    reloadParent() {
      this.$emit('successfully')
    }
  }
}
</script>

<style>

</style>