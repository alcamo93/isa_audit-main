<template>
  <b-row>
    <loading :show="loadingMixin" />
    <b-col>
      <filter-area :opened="true">
        <filters
          @filterSelected="setFilters"
        />
      </filter-area>
      <b-card>
        <div class="d-flex justify-content-end">
          <download-all-files 
            :id-audit-process="filters.id_audit_processes ?? 0"
          />
        </div>
        <b-card-text>
          <b-table-simple class="group-table" hover responsive>
            <b-thead>
              <b-tr>
                <b-th
                  class="text-center" 
                  v-for="field in headerTable" :key="field.key"
                >
                  {{ field.label }}
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="!groupList.length">
                <b-tr class="row-group">
                  <b-td class="text-center" :colspan="headerTable.length">
                    {{ textEmptyTable }}
                  </b-td>
                </b-tr>
              </template>
              <template v-else>
                <template v-for="aspect in groupList">
                  <b-tr 
                    :key="getKeyGroupLibrary(aspect, 'aspect')" 
                    class="row-group row-level-0"
                  >
                    <b-td class="text-center" :colspan="headerTable.length">
                      {{ getLabelGroup(aspect, 'aspect') }}
                    </b-td>
                  </b-tr>
                  <template v-for="requirement in aspect">  
                    <b-tr 
                      :key="getKeyGroupLibrary(requirement, 'requirement')" 
                      class="row-group row-level-1"
                    >
                      <b-td class="text-center" :colspan="headerTable.length">
                        {{ getLabelGroup(requirement, 'requirement') }}
                      </b-td>
                    </b-tr>
                    <template>
                      <b-tr v-for="row in requirement" :key="row.id">
                        <b-td class="text-center">{{ getNoRequirementText(row) }}</b-td>
                        <b-td class="text-center">{{ row.requirement.matter.matter }}</b-td>
                        <b-td class="text-center">{{ row.requirement.aspect.aspect }}</b-td>
                        <b-td class="text-center">{{ getRequirementText(row) }}</b-td>
                        <b-td class="text-center">{{ getFieldRequirement(row, 'evidence') }}</b-td>
                        <b-td class="text-center">{{ getCategoryFile(row) }}</b-td>
                        <b-td class="text-center">{{ getNameFile(row) }}</b-td>
                        <b-td class="text-center">
                          <b-badge pill :variant="getVariantColor(row)">
                            {{ getDatesFile(row) }}
                          </b-badge>
                        </b-td>
                        <b-td class="text-center">{{ getFieldRequirement(row, 'condition') }}</b-td>
                        <b-td class="text-center">
                          <b-badge pill :variant="getVariantColor(row)">
                            {{ getHasLibrary(row) }}
                          </b-badge>
                        </b-td>
                        <b-td class="text-center td-actions">
                          <file-buttons
                            @successfully="getRegisters"
                            :parent-record="{
                              id_audit_process: filters.id_audit_processes ?? 0,
                              id_evaluate_requirement: row.id_evaluate_requirement,
                              id_aplicability_register: row.aplicability_register_id,
                              id_requirement: row.id_requirement,
                              id_subrequirement: row.id_subrequirement,
                              library: row.library,
                              requirement: row.requirement,
                              subrequirement: row.subrequirement,
                            }"
                            origin="Library"
                            :permissions="getPermissions(row)"
                          />
                        </b-td>
                        <b-td class="text-center">
                          <div v-if="row.library">
                            <b-avatar 
                              :src="row.library.auditor.image ? row.library.auditor.image.full_path : null "
                              variant="secondary"
                            ></b-avatar>
                            <b-link 
                              class="mr-auto btn btn-link go-to-process"
                            >
                              {{ row.library.auditor.person.full_name }}
                            </b-link>
                          </div>
                        </b-td>
                        <b-td class="text-center">
                          <b-badge pill variant="success">
                            {{ row.library ? row.library.load_date_format : '----' }}
                          </b-badge>
                        </b-td>
                      </b-tr>
                    </template>
                  </template>
                </template>  
              </template>
            </b-tbody>
          </b-table-simple>
          <!-- Paginator -->
          <app-paginator
            :data-list="paginate"
            @pagination-data="changePaginate"
          />
          <!-- End Paginator -->
        </b-card-text>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import FilterArea from '../components/slots/FilterArea.vue'
import AppPaginator from '../components/app-paginator/AppPaginator.vue'
import FileButtons from '../components/files/FileButtons.vue'
import Filters from './Filters.vue'
import DownloadAllFiles from './DownloadAllFiles.vue'
import { getLibraries } from '../../../services/libraryService'
import { groupItems, getLabelGroup, getNoRequirementText, getRequirementText,
  getKeyGroupLibrary, getFieldRequirement } from '../components/scripts/texts'
  
export default {
  components: {
    FilterArea,
    AppPaginator,
    FileButtons,
    Filters,
    DownloadAllFiles,
  },
  mounted() {
    document.querySelector('#titlePage').innerHTML = `Mis archivos`
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
        id_audit_processes: 0,
        id_category: null,
        name: null,
        id_matter: null,
        id_aspect: null,
      }  
    }
  },
  computed: {
    groupList() {
      return this.groupItems(this.items)
    },
    headerTable() {
      return [
        {
          key: 'no_requirement',
          label: 'No. Requerimiento',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'matter',
          label: 'Materia',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'aspect',
          label: 'Aspecto',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'requirement',
          label: 'Requerimiento',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'evidence',
          label: 'Tipo de evidencia',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'category',
          label: 'Clasificaciión',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'file_name',
          label: 'Nombre Archivo',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'dates',
          label: 'Vigencia',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'condition',
          label: 'Condición',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'has_file',
          label: 'Cuenta con documento/evidencia',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'actions',
          label: 'Acciones',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'auditor',
          label: 'Cargado por',
          sortable: false,
          class: 'text-center'
        },
        {
          key: 'date',
          label: 'Fecha',
          sortable: false,
          class: 'text-center'
        },
      ]
    },
    textEmptyTable() {
      const processUnselected = this.filters.id_audit_processes == null
      if ( processUnselected ) {
        return 'Selecciona una Evaluación por favor'
      }
      if ( !processUnselected && this.items.length == 0 ) {
        return 'No hay requerimientos de esta Evaluación para biblioteca'
      }
    }
  },
  watch: {
    'filters.id_audit_processes': function(value) {
      if (value == null) return 
      this.getRegisters()
    },
    'paginate.page': function() {
      this.getRegisters()
    }
  },
  methods: {
    // external methods
    groupItems, 
    getLabelGroup, 
    getNoRequirementText, 
    getRequirementText,
    getKeyGroupLibrary,
    getFieldRequirement,
    // local methods
    getVariantColor(item) {
      return item.library != null ? 'success' : 'warning'
    },
    getHasLibrary(item) {
      return item.library != null ? 'Si' : 'No'
    },
    getCategoryFile(item) {
      if (item.library != null) {
        const { category } = item.library.category
        return category
      }
      return '----'
    },
    getNameFile(item) {
      if (item.library != null) {
        const { name } = item.library
        return name
      }
      return '----'
    },
    getDatesFile(item) {
      if (item.library != null) {
        const { init_date_format, end_date_format  } = item.library
        return `${init_date_format} - ${end_date_format}`
      }
      return '----'
    },
    async getRegisters() {
      try {
        this.showLoadingMixin()
        const params = {
          perPage: this.paginate.perPage,
          page: this.paginate.page,
        }
        const { data } = await getLibraries(params, this.filters)
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
    async setFilters({ id_audit_processes, id_category, name, id_matter, id_aspect}) {
      this.filters.id_audit_processes = id_audit_processes ?? 0
      this.filters.id_category = id_category
      this.filters.name = name
      this.filters.id_matter = id_matter
      this.filters.id_aspect = id_aspect
      await this.getRegisters()
    },
    getPermissions({obligations, tasks}) {
      // permission if no has section
      const permissions = { can_approve: true, can_upload: true }
      // permissions only has obligations
      if (obligations.length > 0 && tasks.length == 0) {
        const { can_approve, can_upload } = obligations[0].permissions
        permissions.can_approve = can_approve
        permissions.can_upload = can_upload
      } 
      // permission if has task
      if (tasks.length > 0) {
        const { can_approve, can_upload } = tasks[0].permissions
        permissions.can_approve = can_approve
        permissions.can_upload = can_upload
      }
      return permissions
    }
  }
}
</script>

<style>

</style>