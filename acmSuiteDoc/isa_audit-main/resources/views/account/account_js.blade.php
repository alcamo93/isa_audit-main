<script>
$(document).ready( () => {
    var idUser = '{{ $idUser }}';
    getUser(idUser);
    setFormValidation('#setDataUser');
});
/**
 * Get info user
 */
function getUser(idUser){
    $.get(`{{asset('/users')}}/${idUser}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        var user = data['user'][0];
        document.querySelector('#idUser').value = user.id_user;
        document.querySelector('#imgAccount').src = `{{ asset('/assets/img/faces') }}/${user.picture}`;
        document.querySelector('#corporate').value = user.corp_tradename;
        document.querySelector('#email').value = user.email;
        document.querySelector('#secondaryEmail').value = user.secondary_email ;
        document.querySelector('#profile').value = user.profile_name;
        // Set person data
        var person = data['person'][0];
        document.querySelector('#idPerson').value = person.id_person;
        document.querySelector('#name').value = person.first_name;
        document.querySelector('#secondName').value = person.second_name;
        document.querySelector('#lastName').value = person.last_name;
        document.querySelector('#rfc').value = person.rfc;
        document.querySelector('#gender').value = person.gender;
        document.querySelector('#cell').value = person.phone;
        document.querySelector('#birthdate').value = person.birthdate;
    });
}
/**
 * Handler to submit update user form 
 */
$('#setDataUser').submit( (event) => {
    event.preventDefault();
    if($('#setDataUser').valid()) {
        // Set user data
        var user = {};
        user['idUser'] = document.querySelector('#idUser').value;
        user['email'] = document.querySelector('#email').value;
        user['secondaryEmail'] = document.querySelector('#secondaryEmail').value;
        // Set person data
        var person = {};
        person['idPerson'] = document.querySelector('#idPerson').value;
        person['name'] = document.querySelector('#name').value;
        person['secondName'] = document.querySelector('#secondName').value;
        person['lastName'] = document.querySelector('#lastName').value;
        person['rfc'] = document.querySelector('#rfc').value;
        person['gender'] = document.querySelector('#gender').value;
        person['cell'] = document.querySelector('#cell').value;
        person['birthdate'] = document.querySelector('#birthdate').value;
        //handler notificaction
        $.post('{{ asset('/account/set') }}', {
            '_token': '{{ csrf_token() }}',
            user: user,
            person: person
        },
        (data) => {
            toastAlert(data.msg, data.status);
            if(data.status == 'success') {
                $('#sidebarUserName').text(person['name']+' '+person['secondName'])
            }
        });
    }
});

/**
 * Open de image selection for profile picture
 */
function choosePic () { 
    var inputFile = document.getElementById('accountPic');
    inputFile.click();
}
/**
 * Open initilize de crop for the image selecction 
 */
function openCropModal(obj, url, ratio, user) {
    $('.loading').removeClass('d-none')
    $('#formLogo').scrollintoview({ duration: 1, dirección : "vertical", viewPadding: { y: 100 } });
    $('#perfilImgModal').modal({backdrop:'static', keyboard: false});
    setTimeout(function(){
        cropImage(obj, url, ratio, user)        
        $('.loading').addClass('d-none')
    }, 1000);
}
/**
 * Consumes the promise for saving the crop image in /components/validate_img_js.blade.php
 */
function saveImgProfile(){
    $('.loading').removeClass('d-none')
    saveCrop()
    .then(data => {
        $('#imgAccount').attr('src', '/assets/img/faces/'+data.url)
        $('#sidebarImgProfile').attr('src', '/assets/img/faces/'+data.url)
        $('.imgCropModal').modal('hide');
        $('.loading').addClass('d-none')
    })
}

</script>