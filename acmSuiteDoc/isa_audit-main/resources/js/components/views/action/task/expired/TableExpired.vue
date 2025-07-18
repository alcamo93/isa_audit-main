<template>
  <fragment>
    <p class="text-light bg-dark text-center mb-2">
      Histórico
    </p>
    <b-table-simple hover small responsive>
      <b-thead>
        <b-tr>
          <b-th class="text-center" v-for="field in headerTable" :key="field.key">
            {{ field.label }}
          </b-th>
        </b-tr>
      </b-thead>
      <b-tbody v-if="historicals.length">
        <b-tr v-for="row in historicals" :key="row.id">
          <b-td class="text-center">{{ row.original_date_format }}</b-td>
          <b-td class="text-center">{{ row.extension_date_format }}</b-td>
          <b-td class="text-justify">{{ row.cause }}</b-td>
        </b-tr>
      </b-tbody>
      <b-tbody v-else>
        <b-tr>
          <b-td colspan="3" class="text-center">
            Sin históricos aún
          </b-td>
        </b-tr>
      </b-tbody>
    </b-table-simple>
  </fragment>
</template>

<script>
export default {
  props: {
    historicals: {
      type: Array,
      required: true
    }
  },
  computed: {
    headerTable() {
      return [
        { key: 'original_date', label: 'Fecha anterior' },
        { key: 'extension_date', label: 'Fecha de extensión' },
        { key: 'cause', label: 'Causa' },
      ]
    }
  }
}
</script>

<style>

</style>