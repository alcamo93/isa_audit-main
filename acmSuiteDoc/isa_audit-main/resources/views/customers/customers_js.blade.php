<script>
$(document).ready( () => {
    setFormValidation('#setCustomerForm');
    setFormValidation('#updateCustomerForm');
    setFormValidation('#formLogo');
    setFormValidation('#formLogoSM');
    setFormValidation('#formLogoLG');
});
/*************** Active menu ***************/
activeMenu(2, 'Clientes');
/**
 * Customers datatables
 */
const customersTable = $('#customersTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/customers',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.filterName = document.querySelector('#filterName').value
        }
    },
    columns: [
        { data: 'logo', className: 'text-center', orderable : false },
        { data: 'cust_trademark', className: '', orderable : true },
        { data: 'id_customer', className: 'td-actions text-center', orderable : false }
    ],
    columnDefs: [
        { 
            render: (data, type, row) => {
                return `<img id="img_${row.id_customer}" src="{{ asset('/assets/img/customers/') }}/${data}" width="50px">`;
            },
            targets: 0
        },
        {
            render: (data, type, row) => {
                let btnCorporates = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver plantas" 
                                        href="javascript:openCorporate(${row.id_customer})">
                                        <i class="fa fa-th-list fa-lg corp"></i>
                                    </a>`;
                let btnLogos = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver logos"
                                    href="javascript:openLogos('${row.cust_trademark}', ${row.id_customer})">
                                    <i class="fa fa-file-image-o fa-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditCustomer('${row.cust_trademark}', ${row.id_customer})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>`: '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteCustomer('${row.cust_trademark}', ${row.id_customer})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                let actions = btnCorporates+btnLogos+btnEdit+btnDelete;
                if (row.owner == 1) {
                    actions = btnCorporates+btnLogos+btnEdit;
                }
                return actions;
            },
            targets: 2
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * open corporate
 */
function openCorporate(idCorp)
{
    $('.loading').removeClass('d-none')
    window.location.href = `{{asset('/corporates/customer')}}/${idCorp}`;
}

/**
 * Reload Customers Datatables
 */
const reloadCustomers = () => { customersTable.ajax.reload() }
const reloadCustomersKeepPage = () => { customersTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openAddCustomer () {
    document.querySelector('#setCustomerForm').reset();
    $('#setCustomerForm').validate().resetForm();
    $('#setCustomerForm').find(".error").removeClass("error");
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set customer form 
 */
$('#setCustomerForm').submit( (event) => {
    event.preventDefault();
    if($('#setCustomerForm').valid()) {
        showLoading('#addModal');
        //handler notificaction
        $.post('/customers/set', {
            '_token': '{{ csrf_token() }}',
            sTradename: document.querySelector('#s-tradename').value,
            sTrademark: document.querySelector('#s-trademark').value
        },
        (data) => { console.log(data)
            showLoading('#addModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                reloadCustomersKeepPage();
                $('#addModal').modal('hide');
            }
        });
    }
});

/**
 * Open modal edit form
 */
function openEditCustomer (customer, idCustomer) {
    $('.loading').removeClass('d-none')
    document.querySelector('#updateCustomerForm').reset();
    $('#updateCustomerForm').validate().resetForm();
    $('#updateCustomerForm').find(".error").removeClass("error");
    document.querySelector('#titleEdit').innerHTML = `Información: "${customer}"`;
    // get customer
    $.get(`/customers/${idCustomer}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        if (data.length > 0) {
            let customer = data[0];
            document.querySelector('#idCustHidden').value = customer.id_customer;
            document.querySelector('#u-tradename').value = customer.cust_tradename;
            document.querySelector('#u-trademark').value = customer.cust_trademark;
            $('.loading').addClass('d-none')
            $('#editModal').modal({backdrop:'static', keyboard: false});
        }else{
            reloadCustomersKeepPage();
        }
    });
}
/**
 * Handler to submit update customer form 
 */
$('#updateCustomerForm').submit( (event) => {
    event.preventDefault();
    if($('#updateCustomerForm').valid()) {
        showLoading('#editModal');
        //handler notificaction
        $.post('/customers/update', {
            '_token': '{{ csrf_token() }}',
            idCustomer: document.querySelector('#idCustHidden').value,
            uTradename: document.querySelector('#u-tradename').value,
            uTrademark: document.querySelector('#u-trademark').value
        },
        (data) => {
            showLoading('#editModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success' || data.status == 'error'){
                reloadCustomersKeepPage();
                $('#editModal').modal('hide');
            }
        });
    }
});
/**
 * Delete customer
 */
function deleteCustomer(customerName, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${customerName}"?`,
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
            $.post('/customers/delete',
            {
                '_token':'{{ csrf_token() }}',
                idCustomer: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadCustomersKeepPage();
                }
            });
        }
    });
}

/*********************** Logos ************************/
/**
 * Open logos Modal
 */
function openLogos(customer, idCustomer){
    $('#formLogo').scrollintoview({ duration: 1, dirección : "vertical", viewPadding: { y: 100 } });
    // reset form logo
    document.querySelector('#formLogo').reset();
    $('#formLogo').validate().resetForm();
    $('#formLogo').find(".error").removeClass("error");

    // set data customer
    document.querySelector('#titleLogo').innerHTML = `Logos: "${customer}"`;
    document.querySelector('#idCustLogo').value = idCustomer;
    $('#logosModal').modal({backdrop:'static', keyboard: false});
    // get customer
    reloadCustomerLogos(idCustomer)
}

// Reload customer logos
function reloadCustomerLogos(idCustomer){
    $.get(`/customers/${idCustomer}`, { '_token':'{{ csrf_token() }}' },
    (data) => {
        if (data.length > 0) {
            let customer = data[0];
            let logo = customer.logo;
            document.querySelector('#setCustLogoPreview').src = `{{ asset('/assets/img/customers/${logo}') }}`;

            let ownCustomer = '{{ $idOwnCustomer }}';
            if(ownCustomer == idCustomer) {
                document.querySelector('#sidebarLogo').src = `{{ asset('/assets/img/customers/${logo}') }}`;
            }
        }
        else{
            reloadCustomersKeepPage();
        }
    });
}

function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);

    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }

    return new File([u8arr], filename, {type:mime});
}

window.addEventListener('DOMContentLoaded', function () {
    const inputImage = document.getElementById('setCustLogo');
    let img = document.getElementById('imgLogoCropper');
    let cropper;
    let currentFile = null;

    inputImage.addEventListener('change', function (e) {
        currentFile = e.target.files[0];
        img.src = URL.createObjectURL(currentFile);
        inputImage.value = null;
        cropperLogo();
    });

    function cropperLogo(){
        $('.inputlogo').addClass('d-none');
        $('#cropperLogo').removeClass('d-none');

        cropper = new Cropper(img, {
            aspectRatio: 2.75/1,
            data:{ width: 275,  height: 100, },
            preview: '.previewLogo'
        });
    }

    $("#cropCancel").on('click',function () {
        $('.inputlogo').removeClass('d-none');
        $('#cropperLogo').addClass('d-none');
        cropper.destroy();
        cropper = null;
    });

    $('#updateLogo').on('submit', function (e) {
        e.preventDefault();
        let idCostumer = document.querySelector('#idCustLogo').value;
        let canvas = cropper.getCroppedCanvas({ width: 275, height: 100});
        let form = new FormData();
        form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        form.append('imgLogo', dataURLtoFile(canvas.toDataURL(), currentFile.name));
        form.append('id', idCostumer);

        $.ajax({
            url: 'customers/logos/set',
            data: form,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            success:function(data){
                toastAlert(data.msg, data.status);
                $('.inputlogo').removeClass('d-none');
                $('#cropperLogo').addClass('d-none');

                if (data.status == 'error'){
                    $('.imgCropModal').modal('hide');
                } else {
                    if(cropper != null){
                        cropper.destroy();
                        cropper = null;
                    }

                    reloadCustomerLogos(idCostumer);
                    reloadCustomers();
                }
            }
        });
    });

    $("#logosModal").on('hidden.bs.modal', function () {
        $('.inputlogo').removeClass('d-none');
        $('#cropperLogo').addClass('d-none');

        if(cropper != null){
            cropper.destroy();
            cropper = null;
        }
    });
});
</script>
