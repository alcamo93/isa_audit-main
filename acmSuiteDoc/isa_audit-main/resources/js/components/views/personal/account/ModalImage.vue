<template>
	<fragment>
		<loading :show="loadingMixin" />
		<div class="d-flex justify-content-center">
			<b-avatar v-if="imagePath == null" 
				v-b-tooltip.hover.left 
				title="Cargar Imagen" 
				button 
				@click="showModal"
				class="align-center" 
				icon="person-fill" 
				size="7em"
			></b-avatar>

			<b-avatar v-else 
				v-b-tooltip.hover.left 
				title="Cambiar Imagen" 
				button 
				@click="showModal" 
				:src="imagePath"
				icon="person-fill" 
				size="7em"
			></b-avatar>
		</div>

		<b-modal v-model="dialog" 
			size="xl" 
			:title="titleModal" 
			:no-close-on-backdrop="true"
		>
			<b-container fluid>

				<image-cropper 
					title-image="Foto de perfil" 
					:path="imagePath" 
					:aspect="1.1" 
					@successfully="uploadFileLogo" 
				/>

			</b-container>
		</b-modal>
	</fragment>
</template>

<script>
import { setImage } from '../../../../services/accountService'
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
			return 'Imagen de Perfil'
		},
		imagePath() {
			const { image } = this.record
			return image == null ? null : image.full_path
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
				const { data } = await setImage(formData)
				this.$emit('successfully')
				this.showLoadingMixin()
				this.responseMixin(data)
			} catch (error) {
				this.showLoadingMixin()
				this.responseMixin(error)
			}
		},
	}
}
</script>

<style></style>