import Vue from 'vue'
import moment from 'moment'
import Loading from '../components/views/components/loading/LoadingScreen'

Vue.mixin({
  components: {
    Loading,
  },
  data() {
    return {
      loadingMixin: false,
    }
  },
  methods: {
    isObjectMixin(record) {
      return record !== null ? record : {}
    },
    showLoadingMixin() {
      this.loadingMixin = !this.loadingMixin
    },
    formatDateMixin(value) {
      if (value == null) return '--/--/----'
      return moment(value).format('DD/MM/YYYY')
    },
    formatDateDBMixin(value) {
      if (value == null) return '----/--/--'
      return moment(value).format('YYYY-MM-DD')
    },
    formatDateTimeMixin(value) {
      if (value == null) return '--/--/---- --:--:--'
      return moment(value).format('DD/MM/YYYY HH:mm:ss')
    },
    disabledDateMixin(date) {
      // for vue-date-picker component
      // set in props: 
      // :disabled-date="disabledDateMixin"
      // :disabled-calendar-changer="disabledDateMixin"
      const today = new Date().setHours(0, 0, 0, 0)
      return date < new Date(today)
    },
    responseMixin(res, time = 3000) {
      const normalizedResponse = this.normalizeResponse(res)
      const swalOptions = {
        icon: normalizedResponse.status,
        confirmButtonText: 'OK',
      }
      if (normalizedResponse.type === 'modal') {
        Swal.fire({
          ...swalOptions,
          title: normalizedResponse.title || '',
          html: normalizedResponse.messages.join('<br>'),
        })
      } else {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: time,
          timerProgressBar: true,
          onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          },
        })
        normalizedResponse.messages.forEach((message) => {
          Toast.fire({
            ...swalOptions,
            title: message,
          })
        })
      }
    },
    normalizeResponse(res) {
      let status = 'error'
      let messages = []
      let type = 'toast'
      let title = ''
      
      if (res?.response) {
        const { data } = res.response
        messages = data.message || ['Algo inesperado ocurrio']
      } else if (res?.info) {
        const { title: infoTitle, message } = res.info
        title = infoTitle
        messages = [message]
        status = 'info'
        type = 'modal'
      } else if (res?.message) {
        messages = Array.isArray(res.message) ? res.message : [res.message]
        status = res.success ? 'success' : 'error'
      }
      
      return { status, messages, type, title }
    },
    responseFileMixin(data, headers) {
      const contentDisposition = headers['content-disposition'];
      if (contentDisposition) {
        let fileName = 'fileNameDefault'
        const parts = contentDisposition.split(';');
        const fileNamePart = parts.find((part) => part.trim().startsWith('filename='))
        if (fileNamePart) {
          fileName = fileNamePart.split('=')[1].replace(/"/g, '')
        }

        const url = window.URL.createObjectURL(new Blob([data]))
        const elementLink = document.createElement('a')
        elementLink.href = url
        elementLink.download = fileName
        elementLink.click()
        window.URL.revokeObjectURL(url)
      } else {
        const info = {
          title: 'No hay información suficiente para descargar',
          message: 'La información no es suficiente para construir un documento'
        }
        this.responseMixin({ info })
      }
    },
    alertDeleteMixin(question) {
      return Swal.fire({
        title: question,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar!',
        cancelButtonText: 'No, cancelar!',
        allowEscapeKey: false,
        allowOutsideClick: false,
      })
    },
    alertQuestionMixin(title, question, icon = 'warning') {
      return Swal.fire({
        title,
        text: question,
        icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aceptar!',
        cancelButtonText: 'No, cancelar!',
        allowEscapeKey: false,
        allowOutsideClick: false,
      })
    },
    alertMessageOk(title, icon) {
      return Swal.fire({
        title,
        icon,
        confirmButtonText: 'OK',
      })
    },
  }
})
