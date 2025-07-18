<script>
/**
 * Open users table 
 */

function openUsers(obligation, idUser)
{
    $('.loading').removeClass('d-none')
    obligations.selected = null
    obligations.selected = obligation
    $('#userListModalTitle').html('Usuario asignado')
    if($('.userAsigned').css('display') == 'none') $('.userAsigned').css('display', 'block')
    if($('.usersList').css('display') == 'block') $('.usersList').css('display', 'none')
    $('#listUsersSelection').html('')
    $('#userAsigned').html(`<div class="card">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-2">
                                <img src="/assets/img/faces/default.png" class="card-img" alt="...">
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-8  text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Sin usuario asignado</h5>
                                </div>
                            </div>
                        </div>
                    </div>`)
    if(idUser)
        getUserInfo(idUser)
        .then(()=>{
            $('#userListModal').modal({backdrop:'static', keyboard: false});
            $('.loading').addClass('d-none')
        })
        .catch((e)=>{
            $('.loading').addClass('d-none')
        })
    else  setTimeout(() => {
        $('#userListModal').modal({backdrop:'static', keyboard: false});
        $('.loading').addClass('d-none')
    }, 1000)
}

function getUserInfo(idUser)
{
    return new Promise ((resolve, reject) => {
        $.get('/users/'+idUser,
            {
                '_token': '{{ csrf_token() }}'
            },
            (data) => {
                $('#userAsigned').html(`<div class="card">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-2">
                            <img src="/assets/img/faces/${data.user[0].picture}" class="card-img" alt="...">
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-8  text-center">
                            <div class="card-body">
                                <h5 class="card-title">${data.user[0].complete_name}</h5>
                            </div>
                        </div>
                    </div>
                </div>`)
                resolve(true)
            }
        )
        .fail((e)=>{
            reject(false)
            toastAlert(e.statusText, 'error')
        })
    })
}

$('#btnSelectUser').click((e)=>{
    e.preventDefault()
    $('.userAsigned').toggle()
    showLoading('#userListModal')
    getUserList()
    .then((data)=>{
        let userList = ''
        data.forEach((currentValue, index, array)=>{
            userList +=  `
                    <div class="card">
                        <div class="row">
                            <div class="col-md-1"></div>                       
                            <div class="col-md-2">
                                <img src="/assets/img/faces/${currentValue.picture}" class="card-img" alt="...">
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-8  text-center">
                                <div class="card-body">
                                    <h5 class="card-title">${currentValue.complete_name}</h5>
                                </div>
                                <a  
                                    href="javascript:asignUser(${currentValue.id_user})"
                                    class="btn btn-success float-right btn-sm mr-2"
                                    data-toggle="tooltip"
                                    title="Seleccionar usuario "
                                    tabindex="-1"
                                    role="button"
                                    aria-disabled="true">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </a>
                            </div>     
                        </div>
                    </div>`
        })
        $('#listUsersSelection').html(userList)
        $('#userListModalTitle').html('Lista de usuarios de la planta')
        $('[data-toggle="tooltip"]').tooltip();
        $('.usersList').toggle()
        showLoading('#userListModal')
    })
    .catch(()=>{
        console.log(false)
        $('.userAsigned').toggle()
        showLoading('#userListModal')
    })
})

function getUserList()
{
    return new Promise ((resolve, reject)=>{
        $.post('/users-list',
            {
                '_token': '{{ csrf_token() }}',
                idCorporate : obligations.idCorporate
            },
            (data) => {
                resolve(data)
            }
        )
        .fail((e)=>{
            toastAlert(e.statusText, 'error')
            reject(false)
        })
    })
}

function asignUser(idUser)
{
    showLoading('#userListModal')
    $('.usersList').toggle()
    $.post(
        '/obligations/asign-user',
        {
            '_token': '{{ csrf_token() }}',
            idObligation : obligations.selected,
            idUser: idUser
        },
        (data) => {
            toastAlert(data.msg, data.status)
            getUserInfo(idUser)
            .then(()=>{
                $('#userListModalTitle').html('Usuario asignado')
                showLoading('#userListModal')
                $('.userAsigned').toggle()
                $('.loading').addClass('d-none')
                reloadObligations()
                reloadObligations()
            })
            .catch((e)=>{
                $('.usersList').toggle()
                $('.loading').addClass('d-none')
            })
        }
    )
    .fail((e)=>{
        showLoading('#userListModal')
        toastAlert(e.statusText, 'error')
    })
}

</script>