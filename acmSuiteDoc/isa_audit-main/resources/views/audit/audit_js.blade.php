<script>
$(document).ready( () => {
    // setFormValidation('#wizardForm');
    // setFormValidation('#wizardSubForm');
    setFormValidation('#formFinding');
});
activeMenu(12, 'Auditoria');
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
/**
 * Constants for audit
 */
const NOT_AUDITED = 7;
const AUDITED = 9;
const AUDITING = 8;
const FINISHED = 10;
/**
 * currentAplicabiltyRegister
 */
const currentAR = {
    idAuditProcess: '{{ $idAuditProcesses }}',
    idAuditRegister: '{{ $idAuditRegister }}',
    idContract: '{{ $idContractAR }}',
    idCorporate: '{{ $idCorporate }}',
    customer: '{{ $customer }}',
    corporate: '{{ $corporate }}',
    auditProcess: '{{ $auditProcesses }}',
    scopeAuditProcess: '{{ $scopeAuditProcesses }}',
    totalRisk: '{{ $totalRisk }}',
    evaluateRisk: ('{{ $evaluateRisk }}' == 1) ? true : false,
    idState: '{{ $idState }}',
    idCity: '{{ $idCity }}'
}
/**
 * Current Aspect evaluate
 */
const currentAspect = {
    idAuditAspect: null,
    idAspect: null,
    matter: null,
    aspect: null,
    idStatus: null,
    idApplicationType: null,
    totalRequirements: null,
    totalAnswers: null,
    data: null,
    validateWizard: null,
}
/**
 * Current element
 */
const currentElement = {
    idRequirement: null,
    idSubrequirement: null
}
/**
 * Data for subrequirement
 */
const requirementWithSub = {
    idRequirement: null,
    noRequirement: null,
    totalSubrequirements: null,
    totalAnswers: null,
    validateSubWizard: null,
}
 /**
  * current Status
  */
  const currentStatus = {
    audit: [],
    matters: [],
    global: null
}

</script>