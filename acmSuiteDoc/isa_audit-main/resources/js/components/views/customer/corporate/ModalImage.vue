<template>
	<fragment>
		<loading :show="loadingMixin" />
		<b-avatar v-if="imagePath == null"
			v-b-tooltip.hover.left
      title="Cargar Imagen"
			button 
			@click="showModal" 
			class="align-center" 
			icon="image" 
			size="3em"
		></b-avatar>

		<b-avatar v-else
			v-b-tooltip.hover.left
      title="Cambiar Imagen"
			button 
			@click="showModal" 
			:src="imagePath"
			icon="image" 
			size="3em"
		></b-avatar>

		<b-modal v-model="dialog" size="xl" :title="titleModal" :no-close-on-backdrop="true">
			<b-container fluid>

				<image-cropper 
					title-image="Imagen para Tablero de Control"
          :path="imagePath"
					:aspect="1.1"
					@successfully="uploadImage"
				/>

			</b-container>
		</b-modal>
	</fragment>
</template>

<script>
import { setImage } from '../../../../services/corporateService'
import ImageCropper from '../../components/files/crops/ImageCropper.vue'

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
		}
	},
	computed: {
		titleModal() {
			return 'Imagenes'
		},
		imagePath() {
			const { image } = this.record
			if ( image == null ) return null 
			const img = image.full_path
			return img == null ? null : image.full_path
		},
	},
	methods: {
		showModal() {
			this.dialog = true
		},
		async uploadImage(file) {
      try {
        this.showLoadingMixin()
        const formData = new FormData()
        formData.append(`file`, file)
        const { data } = await setImage(this.record.id_customer, this.record.id_corporate, formData)
        this.$emit('successfully')
        this.showLoadingMixin()
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    }
	}
}
</script>

<style>

</style>