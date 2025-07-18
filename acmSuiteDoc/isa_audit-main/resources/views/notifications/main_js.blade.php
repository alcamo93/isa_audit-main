<script>
document.querySelector('#titlePage').innerHTML = 'Notificaciones';
// $('body').addClass('sidebar-mini');
/**
 * Get aplicability aspects by matter
 */
var unreadNotifications = $('#unreadNotifications').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage.json"
    },
    ajax: { 
        url: '/notifications/unread',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idUser = '{{ $idUser }}',
            data.status = 'unread'
        }
    },
    columns: [
        { data: 'data', className: 'text-justify', orderable : false },
        { data: 'id', className: 'text-center td-actions', orderable : false, visible: true }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                let dataNotification = JSON.parse(data);
                let notification = `<span class="font-weight-bold">${dataNotification.title}</span>
                                <a href="{{ asset('${dataNotification.link}') }}" target="_blank">
                                <i class="fa fa-external-link" aria-hidden="true"></i></a>
                                <br><span>${dataNotification.body}</span>`;
                return notification; 
            },
            targets: 0
        },
        {
            render: ( data, type, row ) => {
                let btnRead = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Marcar como leida" 
                                href="javascript:markRead('${data}')">
                                <i class="fa fa-check-square-o fa-lg"></i>
                            </a>`;
                return btnRead;
            },
            targets: 1
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
 * Marked like read notification 
 */
function markRead(idNotification){
    //handler notificaction
    $.post('{{ asset('/notifications/marked/read') }}', {
        '_token': '{{ csrf_token() }}',
        idNotification: idNotification
    },
    (data) => {
        if(data.status == 'success'){
            reloadTables();
        }
        else {
            toastAlert(data.msg, data.status);
        }
    });
}
/**
 * Get aplicability aspects by matter
 */
var readNotifications = $('#readNotifications').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage.json"
    },
    ajax: { 
        url: '/notifications/read',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idUser = '{{ $idUser }}',
            data.status = 'read'
        }
    },
    columns: [
        { data: 'data', className: 'text-justify', orderable : false },
        { data: 'id', className: 'text-center td-actions', orderable : false, visible: true }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                let dataNotification = JSON.parse(data);
                let notification = `<span class="font-weight-bold">${dataNotification.title}</span>
                                <a href="{{ asset('${dataNotification.link}') }}" target="_blank">
                                <i class="fa fa-external-link" aria-hidden="true"></i></a>
                                <br><span>${dataNotification.body}</span>`;
                return notification; 
            },
            targets: 0
        },
        {
            render: ( data, type, row ) => {
                let btnRead = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar Notificación" 
                                href="javascript:deleteNotification('${data}')">
                                <i class="fa fa-times fa-lg"></i>
                            </a>`;
                return btnRead;
            },
            targets: 1
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
 * Reload tables
 */
function reloadTables(){ 
    readNotifications.ajax.reload(null, false);
    unreadNotifications.ajax.reload(null, false);
    totalNotifications();
}
/**
 * Delete notification
 */
 function deleteNotification(idNotification){
    $.post('{{ asset('/notifications/destroy') }}', {
        '_token': '{{ csrf_token() }}',
        idNotification: idNotification
    },
    (data) => {
        if(data.status == 'success'){
            reloadTables();
        }
        else {
            toastAlert(data.msg, data.status);
        }
    });
 }
 /**
 * Get total notification
 */
function totalNotifications(){
    $.get('{{ asset('/notifications/total') }}', {
        '_token': '{{ csrf_token() }}',
    },
    (data) => {
        if (data > 0) {
            document.querySelector('#totalNotifications').innerHTML = data;
            $('#totalNotifications').removeClass('d-none');
        }else{
            $('#totalNotifications').addClass('d-none');
        }
    });
 }
</script>