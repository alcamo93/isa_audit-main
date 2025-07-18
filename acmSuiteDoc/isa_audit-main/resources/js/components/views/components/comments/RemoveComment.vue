<template>
  <b-button
    variant="danger"
    v-b-tooltip.hover.left 
    size="sm"
    title="Modificar este comentario"
    class="ml-1 mr-1"
    @click="removeComment()"
  >
    <b-icon icon="trash" aria-hidden="true"></b-icon>
  </b-button>
</template>

<script>
import { deleteTaskComment } from '../../../../services/taskCommentService'
import { truncateText } from '../../components/scripts/texts'

export default {
  props: {
    moduleName: {
      type: String,
      required: true,
       validator: value => {
        const types = ['task']
        return types.indexOf(value) !== -1
      }
    },
    paramsUrl: {
      type: Object,
      required: true,
    },
    item: {
      type: Object,
      required: true
    }
  },
  methods: {
    async removeComment() {
      try {
        const { id_comment, comment } = this.item
        const title = truncateText(comment)
        const question = `¿Estás seguro de eliminar el comentario: '${title}'?`
        const { value } = await this.alertDeleteMixin(question)
        if ( !value ) return
          
        this.showLoadingMixin()
        let response = null
        if (this.moduleName == 'task') {
          const { idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask } = this.paramsUrl
          const { data } = await deleteTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, id_comment)
          response = data
        }
        this.responseMixin(response)
        this.$emit('successfully')
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