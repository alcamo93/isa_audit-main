<template>
  <ckeditor 
    :editor="editor" 
    v-model="textComputed"
    @input="onEditorInput"
    :config="editorConfig"      
    :disabled="disabled"
  />
</template>

<script>
import CKEditor from '@ckeditor/ckeditor5-vue2'
import ClassicEditor from './ckeditor5-build-classic-custom/build/ckeditor.js'
/**
 * If this component is used in v-modal component
 * add prop :no-enforce-focus="true"
 * no remove :root .ck-content .table in style section
 * https://ckeditor.com/docs/ckeditor5/latest/installation/integrations/css.html#compatibility-with-bootstrap
 */
export default {
  components: {
    ckeditor: CKEditor.component
  },
  props: {
    placeholder: {
      type: String,
      required: false,
      default: 'Ingresa texto...'
    },
    initialContent: {
      type: String,
      required: false,
      default: ''
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    onlyPresentation: {
      type: Boolean,
      required: false,
      default: false,
    },
    field: {
      type: String,
      required: false,
      default: 'default'
    },
    enableImage: {
      type: Boolean,
      required: false,
      default: false
    },
    enableVideo: {
      type: Boolean,
      required: false,
      default: false
    },
  },
  computed: {
    textComputed: {
      get: function () {
        return this.initialContent
      },
      // setter
      set: function (newValue) {
        this.content = newValue
      }
    }
  },
  data() {
    return {
      content: '',
      editor: ClassicEditor,
      editorConfig: {
        licenseKey: process.env.MIX_CKEDITOR_LICENCE_KEY,
        placeholder: this.placeholder,
        toolbar: this.onlyPresentation ? ['exportPdf', 'exportWord'] : {
          items: [
            'undo',
            'redo',
            '|',
            'heading',
            'fontSize',
            'fontColor',
            'fontBackgroundColor',
            'highlight',
            '|',
            'bold',
            'italic',
            'underline',
            '|',
            'alignment',
            'indent',
            'outdent',
            '|',
            'bulletedList',
            'numberedList',
            '|',
            'blockQuote',
            'link',
            (this.enableImage ? 'imageInsert' : ''),
            'insertTable',
            (this.enableVideo ? 'mediaEmbed' : ''),
            '|',
            'showBlocks',
            'removeFormat',
            'exportPdf', 
            'exportWord'
          ],
          shouldNotGroupWhenFull: true
        },
        language: 'es',
        heading: {
          options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
          ]
        },
        image: this.enableImage ? {
          styles: ['alignCenter', 'alignLeft', 'alignRight'],
          resizeOptions: [
            {
              name: 'resizeImage:original',
              label: 'Default image width',
              value: null,
            },
            {
              name: 'resizeImage:25',
              label: '25% page width',
              value: '25',
            },
            {
              name: 'resizeImage:50',
              label: '50% page width',
              value: '50',
            },
            {
              name: 'resizeImage:75',
              label: '75% page width',
              value: '75',
            },
          ],
          toolbar: [
            'imageTextAlternative',
            'toggleImageCaption',
            '|',
            'imageStyle:inline',
            'imageStyle:wrapText',
            'imageStyle:breakText',
            'imageStyle:side',
            '|',
            'resizeImage',
          ],
        } : [],
        table: {
          contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells',
            'tableProperties',
            'tableCellProperties',
            'toggleTableCaption',
          ],
          tableColumnResize: {
            resizeEnabled: true
          }
        },
        fontColor: {
          colors: [
            {
              color: '#010666',
              label: 'Deep Navy Blue'
            },
            {
              color: '#C1121F',
              label: 'Crimson Red'
            },
            {
              color: '#B8CFE8',
              label: 'Pale Sky Blue'
            },
            {
              color: '#FDEFD5',
              label: 'Light Vanilla Beige'
            },
            {
              color: 'hsl(0, 0%, 100%)',
              label: 'White',
              hasBorder: true
            },
          ]
        },
        fontBackgroundColor: {
          colors: [
            {
              color: '#010666',
              label: 'Deep Navy Blue'
            },
            {
              color: '#C1121F',
              label: 'Crimson Red'
            },
            {
              color: '#B8CFE8',
              label: 'Pale Sky Blue'
            },
            {
              color: '#FDEFD5',
              label: 'Light Vanilla Beige'
            },
            {
              color: 'hsl(0, 0%, 100%)',
              label: 'White',
              hasBorder: true
            },
          ]
        },
        fontSize: {
          options: [ 8, 10, 12, 14, 16, 18, 24, 36 ]
        },
        exportPdf: {
          fileName: 'documento.pdf',
          converterOptions: {
            format: 'Letter',
            // margin_top: '20mm',
            // margin_bottom: '20mm',
            // margin_right: '12mm',
            // margin_left: '12mm',
            // page_orientation: 'portrait'
          }
        },
        exportWord: {
          fileName: 'documento.docx',
          converterOptions: {
            format: 'Letter',
            // margin_top: '20mm',
            // margin_bottom: '20mm',
            // margin_right: '12mm',
            // margin_left: '12mm'
          }
        },
      }
    }
  },
  methods: {
    onEditorInput(value) {
      this.$emit('input', this.field, value)
    },
  }
}

</script>

<style>
:root {
  --ck-z-default: 100;
  --ck-z-panel: calc( var(--ck-z-default) + 999 );
}
.ck-content .table {
  width: auto;
}

.ck .ck-editor__main div {
  background: transparent !important;
}

</style>