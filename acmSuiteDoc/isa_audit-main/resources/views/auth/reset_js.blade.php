<script>
 $(document).ready( () => {
    checkFullPageBackgroundImage();
    setTimeout( () => {
        // after 800 ms we add the class animated to the login/register card
        $('.card').removeClass('card-hidden');
    }, 700);
    setFormValidation('#setResetForm');
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
 * Set new password
 */
$('#setResetForm').submit( (event) => {
    event.preventDefault();
    if($('#setResetForm').valid()) {
        $.post('{{asset('/login/setReset')}}', {
            '_token':'{{ csrf_token() }}',
            newPassword: document.querySelector('#newPassword').value, 
            repitPassword: document.querySelector('#repitPassword').value, 
            'resetToken':'{{ $resetToken }}'
        },
        (data) => {
            toastAlert(data.msg, data.status);
            setTimeout(  () => {
                window.location.href = '{{ asset('/') }}';
            }, 3000);
        });
    }
});
</script>