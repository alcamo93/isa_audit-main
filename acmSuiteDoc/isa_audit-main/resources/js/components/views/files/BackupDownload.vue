<template>
  <fragment>
    <loading :show="loadingMixin" />
    <b-card>
      <b-card-header>
        <h3 class="font-weight-bold">
          Descarga de respaldo de mi Biblioteca
        </h3>
      </b-card-header>
      <b-card-body v-if="hasBackup">
        <div class="d-flex flex-wrap">
          <div class="my-2 mx-auto">
            <label>
              Cliente
            </label>
            <div class="font-weight-bold">
              {{ data.process.customer.cust_tradename_format }}
            </div>
          </div>
          <div class="my-2 mx-auto">
            <label>
              Planta
            </label>
            <div class="font-weight-bold">
              {{ data.process.corporate.corp_tradename_format }}
            </div>
          </div>
          <div class="my-2 mx-auto">
            <label>
              Nombre de evaluación
            </label>
            <div class="font-weight-bold">
              {{ data.process.audit_processes }}
            </div>
          </div>
          <div class="my-2 mx-auto">
            <label>
              Fecha de creación
            </label>
            <div class="font-weight-bold">
              {{ data.init_date_format }}
            </div>
          </div>
        </div>
        <div class="d-flex flex-column justify-content-center align-items-center">
          <b-button
            variant="info"
            block
            @click="downloadZip"
          >
            Desacargar Respaldo
            <b-icon icon="file-earmark-zip" aria-hidden="true"></b-icon>
          </b-button>
        </div>
      </b-card-body>
      <b-card-body v-else>
        <p class="text-center">Buscando respaldo...</p>
      </b-card-body>
    </b-card>
  </fragment>
</template>

<script>
import { getBackup, downloadBackup } from '../../../services/backupService';

export default {
  mounted() {
    this.getRecord()
  },
  props: {
    idBackup: {
      type: Number,
      require: true,
    }
  },
  data() {
    return {
      data: null
    }
  },
  computed: {
    hasBackup() {
      return this.data != null
    }
  },
  methods: {
    async getRecord() {
      try {
        this.showLoadingMixin()
        const { data } = await getBackup(this.idBackup)
        this.data = data.data
        this.showLoadingMixin()
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
    async downloadZip() {
      try {
        this.showLoadingMixin()
        const { data, headers } = await downloadBackup(this.idBackup)
        this.responseFileMixin(data, headers)
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