<template>
  <div class="accordion" role="tablist">
    <b-card no-body class="mb-1">
      <b-card-header class="p-1">
        <div class="d-flex flex-wrap flex-sm-column justify-content-sm-center flex-md-row justify-content-md-between">
          <div
            class="align-self-center">
            <b-button 
              size="sm"
              pill variant="primary" class="float-left mt-1 ml-2"
              :class="isVisibleClass"
              :aria-expanded="isExpanded"
              aria-controls="filter-area-collapse"
              @click="activeCollapse"
            >
              <b-icon :icon="defineIcon" aria-hidden="true"></b-icon>
            </b-button>
          </div>
          <div class="align-self-center">
            <label :class="classTitle">
              {{ titleArea }}
            </label>
          </div>
          <div class="align-self-center">
            <slot name="action"></slot>
          </div>
        </div>
      </b-card-header>
      <b-collapse 
        :id="collapseId" 
        v-model="visible"
      >
        <b-card-body>
          <slot></slot>
        </b-card-body>
      </b-collapse>
    </b-card>
  </div>
</template>

<script>
export default {
  created() {
    if (this.opened) this.activeCollapse()
  },
  props: {
    title: {
      type: String,
      required: false,
      default: 'Área de filtrado',
    },
    opened: {
      type: Boolean,
      required: false,
    },
    customTitle: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  computed: {
    titleArea() {
      return this.title
    },
    isVisibleClass() {
      return this.visible ? null : 'collapsed'
    },
    isExpanded() {
      return this.visible ? 'true' : 'false'
    },
    defineIcon() {
      return this.visible ? 'chevron-up' : 'chevron-down'
    },
    classTitle() {
      return this.customTitle ? 'global-dashboard-title' : 'font-weight-bold'
    },
    titleId() {
      return this.title.toLowerCase().replace(/ /g, "-");
    },
    collapseId() {
      return `filter-area-collapse-${this.titleId}`;
    }
  },
  data() {
    return {
      visible: false
    }
  },
  methods: {
    activeCollapse() {
      this.visible = !this.visible
    }
  }
}
</script>

<style scoped>
.global-dashboard-title {
    text-transform: capitalize !important;
    color: #113C53;
    font-family: Verdana;
    font-weight: 600;
    font-size: 18px;
  }
</style>