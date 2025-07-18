<script>
$(document).ready( () => {
    // Set title audit process
    $(".auditTitle").html(currentAR.auditProcess);
    $(".scopeAudit").html(currentAR.scopeAuditProcess);
    // Init Process
    if (currentAR.idAplicabilityRegister != 0) {
        setAspectsToAplicability();
    }
    else{
        Swal.fire({
            title: 'Sin acceso a Aplicabilidad',
            text: "Contacte al administrador para saber la situación de su contracto",
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                window.location.href = '/dashboard';
            }
        });
    }
});
</script> 