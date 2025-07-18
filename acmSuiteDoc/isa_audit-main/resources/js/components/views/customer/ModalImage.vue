<template>
	<fragment>
		<loading :show="loadingMixin" />
		<b-avatar v-if="logoImagePath == null"
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
			:src="logoImagePath"
			icon="image" 
			size="3em"
		></b-avatar>

		<b-modal v-model="dialog" size="xl" :title="titleModal" :no-close-on-backdrop="true">
			<b-container fluid>

				<image-cropper 
					title-image="Logo Cliente"
          :path="logoImagePath"
					:aspect="1.1"
					@successfully="uploadFileLogo"
				/>
				<hr>
				<image-cropper 
					title-image="Imagen para Tablero de Control"
          :path="customerImagePath"
					:aspect="1.1"
					@successfully="uploadFileCustomer"
				/>

			</b-container>
		</b-modal>
	</fragment>
</template>

<script>
import { setImage } from '../../../services/customerService'
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
		}
	},
	computed: {
		titleModal() {
			return 'Imagenes'
		},
		logoImagePath() {
			const { images } = this.record
			if ( images.length == 0 ) return null 
			const img = images.find(item => item.usage == 'logo')
			return img == null ? null : img.full_path
		},
		customerImagePath() {
			const { images } = this.record
			if ( images.length == 0 ) return null 
			const img = images.find(item => item.usage == 'dashboard')
			return img == null ? null : img.full_path
		},
	},
	methods: {
		showModal() {
			this.dialog = true
		},
		async uploadFileLogo(file) {
      try {
        this.showLoadingMixin()
        const formData = new FormData()
        formData.append(`file`, file)
        const { data } = await setImage(this.record.id_customer, 'logo', formData)
        this.$emit('successfully')
        this.showLoadingMixin()
        this.responseMixin(data)
      } catch (error) {
        this.showLoadingMixin()
        this.responseMixin(error)
      }
    },
		async uploadFileCustomer(file) {
      try {
        this.showLoadingMixin()
        const formData = new FormData()
        formData.append(`file`, file)
        const { data } = await setImage(this.record.id_customer, 'dashboard', formData)
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