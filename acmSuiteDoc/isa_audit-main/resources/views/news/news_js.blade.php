<script>
    var today = '{{ $today }}';
    var picture;

    window.addEventListener('DOMContentLoaded', function () {
        const inputImage = document.getElementById('imgNew');
        const uinputImage = document.getElementById('u-imgNew');
        const img = document.getElementById('img');
        var crop = null;
        var imgCrop;
        var initialAvatarURL;
        var canvas;
        inputImage.addEventListener('change', function (e) {
            let files = e.target.files;
            img.src = URL.createObjectURL(files[0]);
            $("#modalCrop").modal('show');
            imgCrop = document.getElementById('imgCrop');
        });
        uinputImage.addEventListener('change', function (e) {
            let files = e.target.files;
            img.src = URL.createObjectURL(files[0]);
            $("#modalCrop").modal('show');
            imgCrop = document.getElementById('u-imgCrop');
        });
        $("#modalCrop").on('shown.bs.modal', function () {
            crop = new Cropper(img, {
                aspectRatio: 4/1,
                data:{ width: 5016, height: 1104, },
            });
        }).on('hidden.bs.modal', function () {
            crop.destroy();
            crop = null;
        });
        document.getElementById('crop').addEventListener('click', function () {
            canvas = crop.getCroppedCanvas({

            });
            initialAvatarURL = imgCrop.src;
            imgCrop.src = canvas.toDataURL();
            $("#modalCrop").modal('hide');
        });
    });

    const news = $('#newsTable').DataTable({
        dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
        language: {
            url: '/assets/lenguage.json'
        },
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        ajax: {
            url: '/get/news', 
            type: 'POST',
            data: (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.fIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.fIdCorporate = document.querySelector('#filterIdCorporate').value
        }
        },
        columns: [
            { data: 'new.title', className: 'text-center', orderable : true },
            { data: 'new.start_date', className: 'text-center', orderable : true },
            { data: 'new.clear_date', className: 'text-center', orderable : true },
            { data: 'new.id_new', className: 'text-center td-actions', orderable : false, visible: true}
        ],
        columnDefs: [
            {
                render: ( data, type, row ) => {
                    let btnEdit = `<a class="btn btn-warning btn-link btn-xs" rel="tooltip" title="Editar" 
                                        onclick="openEditNew('${row.new.title}', '${row.new.id_new}')">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>`;
                    let btnDelete = `<a class="btn btn-danger btn-link btn-xs" rel="tooltip" title="Eliminar" 
                                        onclick="deleteNew('${row.new.title}', '${row.new.id_new}')">
                                        <i class="fa fa-times fa-lg"></i>
                                    </a>`;
                    return btnEdit+btnDelete;
                },
                targets: 3
            }
        ]
    });

    const reloadProfiles = () => { profilesTable.ajax.reload() }
    const reloadProfilesKeepPage = () => { profilesTable.ajax.reload(null, false) }

    function openAddNew () {
        document.querySelector('#setNewForm').reset();
        $('#setNewForm').validate().resetForm();
        $('#setNewForm').find(".error").removeClass("error");
        document.querySelector('#imgCrop').src = '';
        document.querySelector("#startDate").value = today;
        document.querySelector("#clearDate").value = today;
        document.querySelector("#startDate").min = today; 
        document.querySelector("#clearDate").min = today;
        $('#addModal').modal({backdrop:'static', keyboard: false});
    }

    const openEditNew = (title, idNew) => {
        document.querySelector('#updateNewForm').reset();
        document.querySelector('#u-postTitle').removeAttribute('checked')
        document.querySelector('#u-postContent').removeAttribute('checked')
        $('#updateNewForm').validate().resetForm();
        $('#updateNewForm').find(".error").removeClass("error");
        document.querySelector('#titleEdit').innerHTML = `Información: "${title}"`;
        // get new
        $('.loading').removeClass('d-none')
        $.get(`/news/${idNew}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        (data) => {
            if (data.status == 'success') { 
                const {id_new, title, start_date, clear_date, description, show_title, show_description, id_customer, id_corporate} = data.data;
                document.querySelector('#idNew').value = id_new;
                document.querySelector('#u-title').value = title;
                document.querySelector('#u-startDate').value = start_date;
                document.querySelector('#u-clearDate').value = clear_date;
                document.querySelector('#u-description').value = description;
                document.querySelector('#u-idCustomer').value = id_customer;
                if(show_title) document.querySelector('#u-postTitle').setAttribute('checked', true)
                if(show_description) document.querySelector('#u-postContent').setAttribute('checked', true)
                document.querySelector('#u-imgCrop').src = `/assets/img/news/new_${id_new}.jpg`;
                setCorporatesActive(id_customer, '#u-idCorporate')
                .then(res => {
                    document.querySelector('#u-idCorporate').value = id_corporate;
                    $('.loading').addClass('d-none');
                    $('#editModal').modal({backdrop:'static', keyboard: false});
                })
                .catch(e => {
                    $('.loading').addClass('d-none')
                    console.log(e);
                })
                
            }else{
                $('.loading').addClass('d-none')
                toastAlert(data.msg, data.status);
            }
        })
        .fail(e => {
            $('.loading').addClass('d-none')
            toastAlert(e.statusText, 'error');
        });
    }

    $('#setNewForm').on('submit', function (e) {
        e.preventDefault();
        if($('#setNewForm').valid())
        {
            showLoading('#addModal')
            var checkImage = 0;
            var checkTitle = 0;
            var checkContent =0;
            var data = new FormData();
            data.append('_token',  document.querySelector('meta[name="csrf-token"]').content);
            data.append('title', $("#title").val());
            data.append('start_date', $("#startDate").val());
            data.append('clear_date', $("#clearDate").val());
            data.append('description', $("#description").val());
            data.append('id_new', $("#btnAddNew").attr("data-new"));

            if($('#postTitle').prop('checked'))
                checkTitle = 1;

            if($('#postContent').prop('checked'))
                checkContent = 1;

            data.append('postImage', checkImage);
            data.append('postTitle', checkTitle);
            data.append('postContent', checkContent);
            picture = $("#imgCrop").attr("src");
            var newPicture = dataURLtoFile(picture, 'image.jpg');
            data.append('imgNew', newPicture);

            $.ajax({
                url: 'news/set',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function (data) {
                    showLoading('#addModal')
                    if (data.status == 'success') {
                        toastAlert(data.message, data.status);
                        $('#addModal').modal('hide');
                        document.querySelector('#imgCrop').src = '';
                        reloadKeepPage();
                    } else {
                        toastAlert(data.message, data.status);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    toastAlert(textStatus, 'error');
                    showLoading('#addModal')
                } 
            });
        }
    });

    $('#updateNewForm').on('submit', function (e) {
        e.preventDefault();
        if($('#updateNewForm').valid()) {
            showLoading('#editModal')
            var checkImage = 0;
            var checkTitle = 0;
            var checkContent =0;
            var data = new FormData();
            data.append('_token',  document.querySelector('meta[name="csrf-token"]').content);
            data.append('title', $("#u-title").val());
            data.append('start_date', $("#u-startDate").val());
            data.append('clear_date', $("#u-clearDate").val());
            data.append('description', $("#u-description").val());
            data.append('id_new', $("#idNew").val());

            if($('#u-postTitle').prop('checked'))
                checkTitle = 1;

            if($('#u-postContent').prop('checked'))
                checkContent = 1;

            data.append('postImage', checkImage);
            data.append('postTitle', checkTitle);
            data.append('postContent', checkContent);
            var myFile = $('#u-imgNew').prop('files');
            if(myFile.length==1) {
                //picture = getBase64Image(document.getElementById("u-imgCrop"));
                picture = $("#u-imgCrop").attr("src");
                var newPicture = dataURLtoFile(picture, 'image.jpg');
            }else{
                var newPicture = null;
            }
            data.append('imgNew', newPicture);
            $.ajax({
                url: 'news/update',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function (data) {
                    showLoading('#editModal')
                    if (data.status == 'success') {
                        toastAlert(data.msg, data.status);
                        $('#editModal').modal('hide');
                        document.querySelector('#u-imgCrop').src = '';
                        reloadKeepPage();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    toastAlert(textStatus, 'error');
                    showLoading('#editModal')
                }
            });
        }
    });

    function deleteNew(title, idNew) {
        Swal.fire({
            title: `¿Estas seguro de eliminar "${title}"?`,
            text: 'El cambio será permanente al confirmar',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar!',
            cancelButtonText: 'No, cancelar!'
        }).then((result) => {
            if (result.value) {
                // send to server
                $.post('/news/delete',
                {
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    idNew: idNew
                },
                (data) => {
                    toastAlert(data.msg, data.status);
                    if(data.status == 'success'){
                        reloadKeepPage();
                    }
                })
                .fail((e)=>{
                    toastAlert(e.statusText, 'error');
                })
            }
        });
    }

    function reload() {
        news.ajax.reload();
    }

    function reloadKeepPage() {
        news.ajax.reload(null, false);
    }

    function dataURLtoFile(dataurl, filename) {

        var arr = dataurl.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]),
            n = bstr.length,
            u8arr = new Uint8Array(n);

        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }

        return new File([u8arr], filename, {type: mime});
    }

    function getBase64Image(img) {
        var canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        var dataURL = canvas.toDataURL("image/png");
        return dataURL;
    }

</script>