<template>
	<div class="d-flex flex-wrap justify-content-end">
		
		<b-button 
			class="mx-1"
			size="sm" variant="success"
			@click="changeDistribute"
		>
			<b-icon :icon="iconDistrubuteName" aria-hidden="true"></b-icon>
			{{ buttonDistrubuteName }}
		</b-button>

		<b-button 
			class="mx-1"
			size="sm" variant="success"
			v-if="showSyncButton"
			@click="$emit('sync')"
		>
			<b-icon icon="check-circle-fill" aria-hidden="true"></b-icon>
				Sincronizar con Catálogos
		</b-button>

		<b-button 
			class="mx-1"
			size="sm" variant="success"
			v-if="showClassifyButton"
			@click="$emit('classify')"
		>
			<b-icon icon="check-circle-fill" aria-hidden="true"></b-icon>
				Clasificar Aspecto
		</b-button>

		<b-button-group size="sm" class="mx-1">
			<b-button 
				:disabled="disabledPreviousBtn" 
				size="sm" variant="success" 
				@click="handlerTabs('previous')"
			>
				<b-icon icon="arrow-left-circle-fill" aria-hidden="true"></b-icon>
				Anterior
			</b-button>
			<b-button size="sm" variant="outline-success">
				{{ currentNaturalTab }}
			</b-button>
			<b-button 
				:disabled="disabledNextBtn" 
				size="sm" variant="success" 
				@click="handlerTabs('next')"
			>
				Siguiente
				<b-icon icon="arrow-right-circle-fill" aria-hidden="true"></b-icon>
			</b-button>
		</b-button-group>

	</div>
</template>

<script>
export default {
	props: {
		index: {
			type: Number,
			required: true
		},
		totalQuestions: {
			type: Number,
			required: true
		},
		showVertical: {
			type: Boolean,
			required: false,
			default: false
		},
		complete: {
			type: Boolean,
			required: true
		},
		keyStatus: {
			type: String,
			required: true
		}
	},
	computed: {
    currentNaturalTab() {
      return this.index + 1
    },
    disabledPreviousBtn() {
      return this.index == 0
    },
    disabledNextBtn() {
      return this.currentNaturalTab == this.totalQuestions
    },
		iconDistrubuteName() {
      const distribute = this.showVertical ? 'horizontal' : 'vertical'
      return `grip-${distribute}`
    },
    buttonDistrubuteName() {
      return this.showVertical ? 'Horizontal' : 'Vertical'
    },
		showClassifyButton() {
			return this.complete && this.keyStatus == 'EVALUATING_APLICABILITY'
		},
		showSyncButton() {
			return this.keyStatus != 'CLASSIFIED_APLICABILITY' || this.keyStatus != 'FINISHED_APLICABILITY'
		}
	},
	methods: {
		handlerTabs(action) {
			let indexValue = this.index
      if (action == 'next' && this.currentNaturalTab < this.totalQuestions) indexValue++
      if (action == 'previous' && this.currentNaturalTab != 0) indexValue--
			this.$emit('update:index', indexValue)
    },
		changeDistribute() {
			this.$emit('update:show-vertical', !this.showVertical)
    },
	}
}
</script>