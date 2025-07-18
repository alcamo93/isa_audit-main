<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-button 
      v-b-tooltip.hover.left
      :title="titleTooltip"
      variant="warning"
      class="btn-link"
      @click="showModal"
    >
      <b-icon icon="card-checklist" aria-hidden="true"></b-icon>
    </b-button>
    <b-modal
      v-model="dialog"
      size="lg"
      :title="titleModal"
      :no-close-on-backdrop="true"
    >
      <b-container v-if="showForm">
        <validation-observer ref="rulesForm">
          <b-form
            ref="formRegister"
            autocomplete="off"
          >
            <b-row>
              <b-col cols="12">
                <b-form-group>
                 <label>
                    Recomendación
                    <span class="text-danger">*</span>
                  </label>
                  <validation-provider
                    #default="{ errors }"
                    rules="required"
                    name="Recomendación"
                  >
                    <b-form-textarea
                      v-model="form.recomendation"
                      placeholder="Escribe la recomendación"
                      rows="3"
                      max-rows="6"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col cols="12">
                <b-button
                  variant="success"
                  class="float-right"
                  @click="pushRecomendation"
                >
                  {{ titleButton }}
                </b-button>
                <b-button
                  variant="danger" 
                  class="float-right mr-2"
                  @click="reset"
                >
                  Cancelar
                </b-button>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </b-container>

      <b-container fluid v-else>
        <b-row>
          <b-col cols="12">
            <b-button
              variant="success"
              class="float-right"
              @click="newRecomendation"
            >
              Agregar 
              <b-icon icon="plus" aria-hidden="true"></b-icon>
            </b-button>
          </b-col>
        </b-row>
        <template>
          <b-row>
            <b-col>
             <label>Recomendación</label>
              <b-form-input
                v-model="filters.recomendation"
                placeholder="Búsqueda por recomendación"
                debounce="500"
              ></b-form-input>
            </b-col>
          </b-row>
          <b-row class="mt-2" v-for="recomendation in items" :key="recomendation.id_recomendation">
            <b-col cols="12">
              <b-card class="mb-2">
                <b-card-text class="mt-2 text-justify">

                  {{ recomendation.recomendation }}
                </b-card-text>
                
                <b-button
                  v-b-tooltip.hover.left 
                  title="Eliminar está Recomendación"
                  variant="danger"
                  class="float-right btn-sm mr-1"
                  @click="removeRecomendation(recomendation)"
                >
                  <b-icon icon="trash" aria-hidden="true"></b-icon>
                </b-button>
                <b-button
                  v-b-tooltip.hover.left 
                  title="Modificar está Recomendación"
                  variant="warning"
                  class="float-right btn-sm mr-1"
                  @click="loadRecomendation(recomendation)"
                >
                  <b-icon icon="pencil-square" aria-hidden="true"></b-icon>
                </b-button>
              </b-card>
            </b-col>
          </b-row>
          <b-row class="mt-2" v-if="items.length == 0">
            <b-col cols="12">
              <b-card>
                <b-card-body class="text-center">
                  <b-card-title>No se han encontrado Recomendaciones</b-card-title>
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
  </fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import AppPaginator from '../../../../components/app-paginator/AppPaginator.vue'
import { required } from '../../../../../validations'
import { getSubRecomendations, deleteSubRecomendation, setSubRecomendation, updateSubRecomendation } from '../../../../../../services/recomendationService'
import { truncateText } from '../../../../components/scripts/texts'

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    AppPaginator,
  },
  props: {
    nameParent: {
      type: String,
      required: true
    },
    idForm: {
      type: Number,
      required: true
    },
    idRequirement: {
      type: Number,
      required: true
    },
    idSubrequirement: {
      type: Number,
      required: true,
      default: null
    },
  },
  data() {
    return {      
      dialog: false,
      showForm: false,
      required,
      items: [],
      paginate: {
        page: 1,
        perPage: 15,
        total: 0,
        rows: 0,
      },
      filters: {
        recomendation: null,
      },
      id_recomendation: null,
      form: {
        recomendation: null,
      }
    }
  },
  computed: {
    titleModal() {
      return `Recomendaciones para: ${this.nameParent}`
    },
    titleTooltip() {
      return `Mostrar Recomendaciones para ${this.nameParent}`
    },
    titleButton() {
      return this.id_recomendation == null ? 'Registrar' : 'Actualizar'
    }
  },
  watch: {
    'paginate.page': function() {
      this.getSubRecomendations()
    },
    filters: {
      handler() {
        this.getSubRecomendations()
      },
      deep: true
    }
  },
  methods: {
    async showModal() {
      this.showLoadingMixin()
      this.reset()
      await this.getSubRecomendations()
      this.showLoadingMixin()
      this.dialog = true
    },
    async getSubRecomendations() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page
        }
        const { data } = await getSubRecomendations(this.idForm, this.idRequirement, this.idSubrequirement, params, this.filters)
        this.items = data.data
        this.paginate.total = data.total
        this.paginate.rows = data.data.length
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
    async pushRecomendation() {
      try {
        const isValid = await this.$refs.rulesForm.validate()
        if (!isValid) return
        this.showLoadingMixin()
        const typeFunction = this.id_recomendation == null ? setSubRecomendation(this.idForm, this.idRequirement, this.idSubrequirement, this.form) : updateSubRecomendation(this.idForm, this.idRequirement, this.idSubrequirement, this.id_recomendation, this.form)
        const { data } = await typeFunction
        this.showLoadingMixin()
        this.reset()
        await this.getSubRecomendations()
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    newRecomendation() {
      this.id_recomendation = null
      this.form.recomendation = null
      this.showForm = true
    },
    loadRecomendation({id_recomendation, recomendation}) {
      this.id_recomendation = id_recomendation
      this.form.recomendation = recomendation
      this.showForm = true
    },
    async removeRecomendation({ id_recomendation, recomendation }) {
      try {
        const title = truncateText(recomendation)
        const question = `¿Estás seguro de eliminar el Recomendación: '${title}'?`
        const { value } = await this.alertDeleteMixin(question)
        if (value) {
          this.showLoadingMixin()
          const { data } = await deleteSubRecomendation(this.idForm, this.idRequirement, this.idSubrequirement, id_recomendation)
          this.responseMixin(data)
          await this.getSubRecomendations()
          this.showLoadingMixin()
        }
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    reset() {
      this.id_recomendation = null
      this.form.recomendation = null
      this.showForm = false
    }
  }
}
</script>

<style>

</style>