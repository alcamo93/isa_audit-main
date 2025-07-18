<template>
	<fragment>

		<b-row v-if="inputFileEmpty">
			<b-col sm="8" md="8">
				<div class="d-flex flex-column justify-center align-center">
					<h5 class="center-item">{{ titleImage }}</h5>
					<div class="center-item">
						<image-item
              :item="{image: path}"
              type="generic"
							:size="15"
            />
					</div>
				</div>
			</b-col>
			<b-col sm="4" md="4">
				<div class="d-flex flex-column justify-content-center align-items-center h-100">
					<b-button @click="onloadImageClick" variant="info">
						Configurar Imagen
						<b-icon icon="bounding-box" aria-hidden="true"></b-icon>
					</b-button>
					<input ref="uploader" class="d-none" type="file" accept="image/*" @change="onFileChanged">
				</div>
			</b-col>
		</b-row>

		<b-row v-else>
			<b-col sm="8" md="8">
				<div class="d-flex flex-column justify-center align-center">
					<h5 class="center-item">{{ titleCropper }}</h5>
					<div class="center-item">
						<cropper class="cropper" :auto-zoom="false" :src="pathCropper" @change="getClipping" :debounce="false"
							:stencil-props="{
								handlers: {},
								movable: false,
								resizable: false,
								aspectRatio: aspectCropper
							}" :background-wrapper-component="$options.components.CustomBackgroundWrapper
								" :resize-image="{
								adjustStencil: false
							}" image-restriction="stencil" />
					</div>
				</div>
			</b-col>
			<b-col sm="4" md="4">
				<div class="d-flex flex-column justify-content-center align-items-center h-100">
					<b-button @click="getImageCropped" variant="success">
						Establecer recorte
						<b-icon icon="crop" aria-hidden="true"></b-icon>
					</b-button>
					<b-button @click="reset" variant="danger">
						Cancelar
						<b-icon icon="trash" aria-hidden="true"></b-icon>
					</b-button>
				</div>
			</b-col>
		</b-row>

	</fragment>
</template>

<script>
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
import CustomBackgroundWrapper from './CustomBackgroundWrapper'
import ImageItem from '../../customers/ImageItem'

export default {
	components: {
		Cropper,
		CustomBackgroundWrapper,
		ImageItem
	},
	props: {
		titleImage: {
			type: String,
			required: true,
		},
		path: {
			required: true,
		},
		aspect: {
			type: Number,
			required: false,
			default: 1.1
		}
	},
	data() {
		return {
			fileImage: null,
			clipping: null,
			pathCropper: null,
			newFileImage: null
		}
	},
	computed: {
		inputFileEmpty() {
			return this.fileImage == null
		},
		nameImageFile() {
			if (this.fileImage == null) return ''
			return this.fileImage.name
		},
		titleCropper() {
			return `Ajustar ${this.titleImage}`
		},
		aspectCropper() {
			return this.aspect
		},
	},
	methods: {
		onloadImageClick() {
			this.$refs.uploader.click()
		},
		onFileChanged(event) {
			this.fileImage = event.target.files[0]
			this.pathCropper = URL.createObjectURL(this.fileImage)
		},
		getClipping({ canvas }) {
			this.clipping = canvas.toDataURL();
		},
		getImageCropped() {
			this.newFileImage = this.dataURLtoFile(this.clipping, this.nameImageFile)
			this.$emit('successfully', this.newFileImage)
			this.reset()
		},
		dataURLtoFile(dataURL, filename) {
			const arr = dataURL.split(',')
			const mime = arr[0].match(/:(.*?);/)[1]
			const bstr = atob(arr[1])

			let n = bstr.length
			const u8arr = new Uint8Array(n)

			while (n--) {
				u8arr[n] = bstr.charCodeAt(n)
			}

			return new File([u8arr], filename, { type: mime })
		},
		reset() {
			this.clipping = null
			this.fileImage = null
			this.newFileImage = null
		}
	}
}
</script>

<style scoped>
.center-item {
	margin-left: auto !important;
	margin-right: auto !important;
}

.cropper {
	width: 500px;
	max-height: 300px;
	background: #DDD;
	object-fit: contain;
}

.cropper.error {
	width: 500px;
	max-height: 300px;
	background: #DDD;
}

.center-text {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100vh;
	/* Altura completa del viewport */
}
</style>