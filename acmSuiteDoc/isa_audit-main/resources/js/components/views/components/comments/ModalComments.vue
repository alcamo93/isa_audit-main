<template>
  <fragment>
    <loading :show="loadingMixin" />

    <b-button 
      variant="primary"
      v-b-tooltip.hover.left
      title="Comentarios"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="chat-text-fill" aria-hidden="true"></b-icon>
    </b-button>

    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <form-comment 
        v-if="showForm"
        :module-name="moduleName"
        :params-url="paramsUrl"
        :id-comment="load.id_comment"
        :content="load.comment"
        @successfully="onSuccessfully"
        @reset="reset"
      />

      <b-container fluid v-else>

        <div class="d-flex flex-wrap justify-content-end">
          <b-button
            size="sm"
            variant="success"
            @click="newComment"
          >
            Agregar
            <b-icon icon="plus" aria-hidden="true"></b-icon>
          </b-button>
        </div>
        
        <template>

          <b-row>
            <b-col>
              <label class="text-uppercase">Comentario</label>
              <b-form-input
                v-model="filters.comment"
                placeholder="Búsqueda por Comentario"
                debounce="500"
              ></b-form-input>
            </b-col>
          </b-row>

          <b-row class="mt-2" v-for="comment in items" :key="comment.id_comment">
            <b-col cols="12">
              <b-card
                class="mb-2"
                :title="comment.user.person.full_name" 
              >
                <b-card-text class="mt-2 text-justify">
                  <span class="font-weight-bold">Comentario: </span>
                  {{ comment.comment }}
                </b-card-text>

                <b-card-text class="font-weight-bold text-muted mb-0">
                  <span class="font-weight-bold">Creado: </span>
                  {{ comment.created_at_format }}
                  <br>
                  <template v-if="comment.created_at_format != comment.updated_at_format">
                    <span class="font-weight-bold">Editado: </span>
                    {{ comment.updated_at_format }}
                  </template>
                </b-card-text>
                
                <div class="d-flex flex-wrap justify-content-end">
                  <remove-comment 
                    :module-name="moduleName"
                    :params-url="paramsUrl"
                    :item="comment"
                    @successfully="onSuccessfully"
                  />
                  <b-button
                    variant="warning"
                    v-b-tooltip.hover.left 
                    size="sm"
                    title="Modificar este comentario"
                    class="ml-1 mr-1"
                    @click="loadComment(comment)"
                  >
                    <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
          <b-row class="mt-2" v-if="items.length == 0">
            <b-col cols="12">
              <b-card>
                <b-card-body class="text-center">
                  <b-card-title>No se han encontrado comentarios</b-card-title>
                </b-card-body>
              </b-card>
            </b-col>
          </b-row>
          <!-- Paginator -->
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
          <!-- End Paginator -->
        </template>
        
      </b-container>
      <!-- footer -->
      <template #modal-footer>
        <div class="d-flex flex-wrap justify-content-end w-100">
          <b-button 
            size="sm"
            variant="danger"
            class="mr-2"
            @click="dialog = false"
          >
            Cerrar
          </b-button>
        </div>
      </template>
    </b-modal>
  </fragment>
</template>

<script>

import AppPaginator from '../../components/app-paginator/AppPaginator.vue'
import { getTaskComments } from '../../../../services/taskCommentService'
import FormComment from './FormComment.vue'
import RemoveComment from './RemoveComment.vue'

export default {
  components: {
    AppPaginator,
    FormComment,
    RemoveComment,
  },
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
    nameParent: {
      type: String,
      required: true
    }
  },
  data() {
    return {      
      dialog: false,
      showForm: false,
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        comment: null,
      },
      load: {
        id_comment: null,
        comment: null,
      }
    }
  },
  computed: {
    titleModal() {
      return `Comentarios para: ${this.nameParent}`
    },
    titleTooltip() {
      return `Mostrar comentarios para ${this.nameParent}`
    },
  },
  watch: {
    'paginate.page': function() {
      this.getComments()
    },
    'filters.comment': function() {
      this.getComments()
    }
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()
      this.reset()
      await this.getComments()
      this.showLoadingMixin()
      this.dialog = true
    },
    async getComments() {
      try {
        this.showLoadingMixin()
        let response = null
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        if ( this.moduleName == 'task' ) {
          const { idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask } = this.paramsUrl
          const { data } = await getTaskComments(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, params, this.filters)
          response = data
        }
        this.items = response.data
        this.paginate.total = response.total
        this.paginate.rows = response.data.length
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    changePaginate({ perPage, page }) {
      this.paginate.perPage = perPage
      this.paginate.page = page
    },
    newComment() {
      this.load.id_comment = 0
      this.load.comment = ''
      this.showForm = true
    },
    loadComment({id_comment, comment}) {
      this.load.id_comment = id_comment
      this.load.comment = comment
      this.showForm = true
    },
    onSuccessfully() {
      this.getComments()
      this.reset()
    },
    reset() {
      this.load.id_comment = 0
      this.load.comment = ''
      this.showForm = false
    }
  }
}
</script>

<style>

</style>