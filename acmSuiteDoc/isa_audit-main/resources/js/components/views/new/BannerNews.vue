<template>
  <fragment>
    <!--HORIZONTAL-->
    <div class="d-flex" style="overflow-x: auto; max-width: 100%; flex-wrap: nowrap;">
      <div class="card" v-for="(item, index) in items" :key="index" 
        style="flex-shrink: 0; max-width: 20rem; margin-right: 1rem; margin-bottom: 1rem"
        @click="goToNewView(item.id)"
      >
        <img :src="item.image.full_path" class="card-img-top">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <p class="card-text text-center font-weight-bolder" style="color: #010666">
            {{ item.headline }}
          </p>
          <p class="card-text text-center font-weight-bolder" style="color: #010666">
            {{ item.publication_start_date_format_text }}
          </p>
        </div>
      </div>
    </div>
  </fragment>
</template>

<script>
import { getNews } from '../../../services/newService'
  
  export default {
    mounted() {
      this.getNews()
    },
    data() {
      return {
        items: [],
        filters: {
          custom_filter: null,
          banner: true,
        },
        paginate: {
          page: 1,
          perPage: 15,
          total: 0,
          rows: 0,
        },
        newData: []
      }
    },
    methods: {
      async getNews() {
        try {
          this.showLoadingMixin()
          const params = {
            perPage: this.paginate.perPage,
            page: this.paginate.page,
          }
          const { data } = await getNews(params, this.filters)
          this.items = data.data
          this.paginate.total = data.total
          this.paginate.rows = data.data.length
          this.showLoadingMixin()
        } catch (error) {
          this.showLoadingMixin()
          this.responseMixin(error)
        }
      },
      showNews(item){
        this.newData = item
        this.$refs.modalNew.showModal();
      },
      goToNewView(id_new){
        window.location.href = `/v2/new/${id_new}/view`
      },
    }
  }
  </script>