<template>
  <fragment>
	<loading :show="loadingMixin" />
 	<div class="d-flex justify-content-center">
	  <b-button
	    variant="success"
		class="float-right"
		@click="showModal"
      >
	    Cargar Imagen
	    <b-icon icon="image" aria-hidden="true"></b-icon>
	  </b-button>
	</div>
		
	<div v-if="this.file_name != ''" class="d-flex justify-content-center">
	  <label>Imagen cargada: {{ this.file_name }}</label>	
	</div>

	<b-modal v-model="dialog" 
	  size="xl" 
	  :title="titleModal" 
	  :no-close-on-backdrop="true"
	>
	  <b-container fluid>
	    <image-cropper 
	      title-image="Imagen Panel" 
		  :aspect="1.3" 
		  @successfully="setFileImage" 
	    />
	  </b-container>
    </b-modal>
  </fragment>
</template>

<script>
import ImageCropper from '../components/files/crops/ImageCropper'

export default {
	components: {
		ImageCropper,
	},
	props: {
		record: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			dialog: false,
			file_name: '',
		}
	},
	computed: {
		titleModal() {
			return 'Imagen Panel'
		},
	},
	methods: {
		showModal() {
			this.dialog = true
		},
		async setFileImage(file) {
		  this.file_name = file.name
		  this.showLoadingMixin()
		  this.$emit('successfully', file)
		  this.dialog = false
		  this.showLoadingMixin()
		},
	}
}
</script>