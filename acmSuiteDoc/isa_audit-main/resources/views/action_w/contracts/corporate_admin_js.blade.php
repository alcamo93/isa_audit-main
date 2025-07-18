<script>
$(document).ready( () => {
    $('#comebackContracts').addClass('d-none');
    // Init Process
    var customer = '{{ $customer }}';
    var corporate = '{{ $corporate }}';
    var idActionRegister = '{{ $idActionRegister }}';
    if (idActionRegister != 0) { 
        showActionPlan(idActionRegister, customer, corporate);
    }
    else{
        Swal.fire({
            title: 'Sin acceso a Plan de Acción',
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
                window.location.href = '{{ asset('/dashboard') }}';
            }
        });
    }
});
</script>