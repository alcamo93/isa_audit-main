<template>
  <b-card>
    <div v-if="title" class="font-weight-bold m-1">
      {{ title }}
    </div>
    <b-row>
      <b-col>
        <b-form-group>
          <label>
            Nombre de evaluación <span class="text-danger">*</span>
          </label>
          <validation-provider
            #default="{ errors }"
            rules="required|max:100"
            name="Nombre de evaluación"
          >
            <b-form-input
              :value="name"
              @input="updateName"
            ></b-form-input>
            <small class="text-danger">{{ errors[0] }}</small>
          </validation-provider>
        </b-form-group>
      </b-col>
    </b-row>
  </b-card>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { required, max } from '../../../../validations'

export default {
  components: {
    ValidationProvider
  },
  props: {
    title: {
      type: String,
      required: false
    },
    name: {
      type: String,
      required: true,
    }
  },
  data() {
    return {
      required,
      max,
    }
  },
  methods: {
    updateName(event) {
      this.$emit('update:name', event);
    }
  }
}
</script>

<style></style>