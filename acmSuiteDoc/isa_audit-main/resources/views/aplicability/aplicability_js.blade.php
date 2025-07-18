<script>
$(document).ready( () => {
    setFormValidation('#wizardForm');
});
activeMenu(12, 'Aplicabilidad');
const CREATE = true
const MODIFY = true
const DELETE = false
/**
 * Constants for applicability
 */
const NOT_CLASSIFIED = 3;
const CLASSIFIED = 4;
const EVALUATING = 5;
const FINISHED = 6;
/**
 * current data Aplicabilty Register
 */
const currentAR = {
    idAuditProcess: '{{ $idAuditProcesses }}',
    idAplicabilityRegister: '{{ $idAplicabilityRegister }}',
    idContract: '{{ $idContractAR }}',
    customer: '{{ $customer }}',
    corporate: '{{ $corporate }}',
    auditProcess: '{{ $auditProcesses }}',
    scopeAuditProcess: '{{ $scopeAuditProcesses }}'
}
/**
 * current data contract aspect 
 */
 const currentAspect = {
    idForm: null,
    idAspect: null,
    idContractAspect: null,
    idStatus: null,
    matter: null,
    aspect: null,
    totalQuestions: null,
    totalAnswers: null,
    validateWizard: null,
    dependency: null,
    freeDependency: null,
    questionInForm: [],
    dependencyInForm: [],
 }
 /**
  * current Status
  */
const currentStatus = {
    aplicability: [],
    matters: []
}
</script>