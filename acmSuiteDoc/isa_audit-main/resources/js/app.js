/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

window.Vue = require('vue')

/* Global Mixin */
import './mixin'

// Import Bootstrap and BootstrapVue CSS files (order is important)
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-vue/dist/bootstrap-vue.min.css'
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)

// import vue fragment
import Fragment from 'vue-fragment'
Vue.use(Fragment.Plugin)

// Import Vue select firles
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
Vue.component('multiselect', Multiselect)
Vue.component('v-select', vSelect)

// Import Vue date picker
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/es.js'
Vue.component('vue-date-picker', DatePicker)

// Import VueApexCharts (Apexcharts: https://apexcharts.com/docs/vue-charts/)
import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Details no auth
Vue.component('DetailsPage', require('./components/views/details/DetailsPage').default)
// Notifications
Vue.component('Account', require('./components/views/personal/account/Account').default)
Vue.component('Notifications', require('./components/views/personal/notification/Notifications').default)
// Modules v2
Vue.component('CustomerList', require('./components/views/customer/CustomerList').default)
Vue.component('CorporateList', require('./components/views/customer/corporate/CorporateList').default)
Vue.component('UserList', require('./components/views/user/UserList').default)
Vue.component('FormList', require('./components/views/catalogs/forms/FormList').default)
Vue.component('ProcessList', require('./components/views/process/ProcessList').default)
Vue.component('ObligationList', require('./components/views/obligation/ObligationList').default)
Vue.component('ActionPlanList', require('./components/views/action/ActionPlanList').default)
Vue.component('TaskList', require('./components/views/action/task/TaskList').default)
Vue.component('LibraryList', require('./components/views/files/LibraryList').default)
Vue.component('GeneralDashboardView', require('./components/views/dashboard/GeneralDashboardView').default)
Vue.component('CustomerDashboardView', require('./components/views/dashboard/CustomerDashboardView').default)
Vue.component('CorporateDashboardObligationView', require('./components/views/dashboard/CorporateDashboardObligationView').default)
Vue.component('CorporateDashboardAuditView', require('./components/views/dashboard/CorporateDashboardAuditView').default)
Vue.component('CorporateDashboardComplianceView', require('./components/views/dashboard/CorporateDashboardComplianceView').default)
Vue.component('ProjectDashboardView', require('./components/views/dashboard/ProjectDashboardView').default)
Vue.component('ProfileList', require('./components/views/profile/ProfileList').default)
Vue.component('IndustryList', require('./components/views/catalogs/industry/IndustryList').default)
Vue.component('MatterList', require('./components/views/catalogs/matter/MatterList').default)
Vue.component('LicenseList', require('./components/views/license/LicenseList').default)
Vue.component('ContractList', require('./components/views/contract/ContractList').default)
Vue.component('GuidelineList', require('./components/views/catalogs/guideline/GuidelineList').default)
Vue.component('LegalBasiList', require('./components/views/catalogs/guideline/legal_basi/LegalBasiList').default)
Vue.component('RequirementList', require('./components/views/catalogs/forms/requirement/RequirementList').default)
Vue.component('SpecificRequirementList', require('./components/views/specificRequirement/SpecificRequirementList').default)
Vue.component('SubrequirementList', require('./components/views/catalogs/forms/requirement/subrequirement/SubrequirementList').default)
Vue.component('QuestionList', require('./components/views/catalogs/forms/question/QuestionList').default)
Vue.component('AuditAspectList', require('./components/views/audit/AuditAspectList').default)
Vue.component('AuditAspectEvaluate', require('./components/views/audit/evaluate/AuditAspectEvaluate').default)
Vue.component('RiskList', require('./components/views/catalogs/risk/RiskList').default)
Vue.component('NewList', require('./components/views/new/NewList').default)
Vue.component('KnowledgeList', require('./components/views/knowledge/KnowledgeList').default)
Vue.component('ArticleList', require('./components/views/knowledge/article/ArticleList').default)
Vue.component('GuidelineTopicList', require('./components/views/knowledge/topic/GuidelineTopicList').default)
Vue.component('ViewNew', require('./components/views/new/ViewNew').default)
Vue.component('ApplicabilityAspectList', require('./components/views/applicability/ApplicabilityAspectList').default)
Vue.component('ApplicabilityAspectEvaluate', require('./components/views/applicability/evaluate/ApplicabilityAspectEvaluate').default)
Vue.component('BackupDownload', require('./components/views/files/BackupDownload.vue').default)
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
})
