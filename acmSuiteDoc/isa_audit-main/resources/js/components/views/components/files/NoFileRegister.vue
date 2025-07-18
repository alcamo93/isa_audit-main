<template>
	<fragment>
		<b-form-group>
			<b-form-checkbox class="mt-4" v-model="has_file" :value="1" :unchecked-value="0" switch showModal>
				{{ labelCheckFile }}
			</b-form-checkbox>
		</b-form-group>
		<b-modal v-model="dialog" size="md" title="No cuento con el documento" :no-close-on-backdrop="true">
			<loading :show="loadingMixin" />
			<b-container fluid>
				<validation-observer ref="rulesForm">
					<b-form ref="formRegister" autocomplete="off">
						<b-row>
							<b-col>
								<h5 class="font-weight-bold text-center">{{ question }}</h5>
								<p class="font-weight-bold text-justify">{{ description }}</p>
							</b-col>
						</b-row>
						<b-row>
							<b-col sm="12" md="12">
								<b-form-group>
									<label>
										Usuario Responsable
									</label>
									<validation-provider #default="{ errors }" rules="required" name="Usuario Responsable">
										<v-select v-model="form.id_user" :options="users" :reduce="e => e.id" label="name">
											<div slot="no-options">
												No se encontraron registros
											</div>
										</v-select>
										<small class="text-danger">{{ errors[0] }}</small>
									</validation-provider>
								</b-form-group>
							</b-col>
						</b-row>

					</b-form>
				</validation-observer>
			</b-container>
			<!-- footer -->
			<template #modal-footer>
				<div class="w-100">
					<b-button variant="success" class="float-right" @click="updateObligationHasFile">
						Aceptar
					</b-button>
					<b-button variant="danger" class="float-right mr-2" @click="has_file = 1">
						Cancelar
					</b-button>
				</div>
			</template>
		</b-modal>
	</fragment>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '../../../validations'
import { getSingleProcess } from '../../../../services/catalogSingleService'
import { updateObligationHasFile } from '../../../../services/obligationService'

export default {
	components: {
		ValidationProvider,
		ValidationObserver,
	},
	props: {
		parentRecord: {
			type: Object,
			required: true
		},
		evaluateableId: {
			type: Number,
			required: false,
			default: 0
		},
	},
	data() {
		return {
			required,
			dialog: false,
			users: [],
			form: {
				id_user: null
			},
			has_file: 1,
			question: '¿Desea establecer el requeriemiento como "Sin Permiso/documento"?',
			description: 'Al no contar con el documento se establecerá como estatus "Sin Permiso/documento"'
		}
	},
	watch: {
		has_file(value) {
			const isOpen = !Boolean(value)
			this.showModal(isOpen)
		}
	},
	computed: {
		labelCheckFile() {
			return `${(this.has_file ? 'Si,' : 'No, no')} cuento con el documento para agregarlo`
		},
	},
	methods: {
		async showModal(open) {
			if (open) {
				await this.getProcess()
				this.dialog = true
				return
			}
			this.dialog = false
		},
		async getProcess() {
			try {
				this.showLoadingMixin()
				const params = { scope: 'users' }
				const { data } = await getSingleProcess(this.parentRecord.id_audit_process, params)
				const { users } = data.data.corporate
				this.users = users.map(item => this.getAuditors(item))
				this.showLoadingMixin()
				this.dialog = true
			} catch (error) {
				this.showLoadingMixin()
				this.responseMixin(error)
			}
		},
		getAuditors(item) {
			const { full_name } = item.person
			return { id: item.id_user, name: full_name, path: item.image?.full_path ?? '' }
		},
		async updateObligationHasFile() {
			try {
				const isValid = await this.$refs.rulesForm.validate()
				if (!isValid) return
				this.showLoadingMixin()
				const { data } = await updateObligationHasFile(this.parentRecord.id_audit_process, this.parentRecord.id_aplicability_register, this.parentRecord.id_section_register, this.evaluateableId, this.form)
				const response = data
				this.dialog = false
				this.showLoadingMixin()
				this.$emit('successfully')
				this.responseMixin(response)
			} catch (error) {
				this.showLoadingMixin()
				this.responseMixin(error)
			}
		}
	},
}
</script>

<style></style>
