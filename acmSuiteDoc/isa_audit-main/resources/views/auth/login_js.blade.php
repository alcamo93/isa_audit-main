<script>
 $(document).ready( () => {
    checkFullPageBackgroundImage();
    setTimeout( () => {
        // after 800 ms we add the class animated to the login/register card
        $('.card').removeClass('card-hidden');
    }, 700);
    setFormValidation('#loginForm');
    setFormValidation('#recoveryForm');
});
/**
 * Set background in login
 */
function checkFullPageBackgroundImage() {
    $page = $('.full-page');
    image_src = $page.data('image');

    if (image_src !== undefined) {
        image_container = `<div class="full-page-background" style="background-image: url(${image_src})"/>`
        $page.append(image_container);
    }
}
/**
 * Handler to submit login form
 */
$('#loginForm').submit( (event) => {
    event.preventDefault();
    if($('#loginForm').valid()) {
        //handler notificaction
        $.post('{{ asset('/login') }}', {
            '_token': '{{ csrf_token() }}',
            email: document.querySelector('#email').value,
            password: document.querySelector('#password').value,
            // remember: (document.querySelector('#remember').value == 'on') ? true : false
        },
        (data) => {
            if (data.status == 'error' || data.status == 'warning') {
                toastAlert(data.msg, data.status);
            }else if (data.status == 'success'){
                window.location.href = '{{ asset('/v2/dashboard/customers/view') }}';
            }
        });
    }
});
/**
 * Show recovery card 
 */
document.querySelector('#show_password_recovery').onclick = () => {
    document.querySelector('#login_card').style.display = 'none';
    document.querySelector('#forget_card').style.display = 'block';
};
/**
 * Show login card
 */
document.querySelector('#show_login').onclick = () => {
    document.querySelector('#forget_card').style.display = 'none';
    document.querySelector('#login_card').style.display = 'block';
    document.querySelector('#recoveryForm').reset();
    $('#recoveryForm').validate().resetForm();
    $('#recoveryForm').find(".error").removeClass("error");
};
/**
 * Handler to submit recovery form 
 */
$('#recoveryForm').submit( (event) => {
    event.preventDefault();
    if($('#recoveryForm').valid()) {
        //handler notificaction
        $.post('{{ asset('/login/reset') }}', {
            '_token': '{{ csrf_token() }}',
            email: document.querySelector('#emailRecovery').value
        },
        (data) => {
            if (data.status == 'error' || data.status == 'warning') {
                toastAlert(data.msg, data.status);
            }else{
                document.querySelector('#forget_card').style.display = 'none';
                document.querySelector('#recoveryForm').reset();
                $('#recoveryForm').validate().resetForm();
                $('#recoveryForm').find(".error").removeClass("error");
                // show login clean
                document.querySelector('#loginForm').reset();
                $('#loginForm').validate().resetForm();
                $('#loginForm').find(".error").removeClass("error");
                document.querySelector('#login_card').style.display = 'block';
                toastAlert(data.msg, data.status);
            }
        });
    }
});
</script>