<script>
function setFormValidation(idForm) {
    $(idForm).validate({
        highlight: (element) => {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            $(element).closest('.form-check').removeClass('has-success').addClass('has-error');
        },
        success: (element) => {
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
            $(element).closest('.form-check').removeClass('has-error').addClass('has-success');
        },
        errorPlacement: (error, element) => {
            $(element).closest('.form-group').append(error).addClass('has-error');
        }
    });
}
</script>