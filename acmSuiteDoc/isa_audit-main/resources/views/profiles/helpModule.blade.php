@php
    $create = (Session::get('create') == 1) ? true : false;
    $modify = (Session::get('modify') == 1) ? true : false;
    $delete = (Session::get('delete') == 1) ? true : false;
@endphp 
<script>
/**
 * Help on main view
 */
function helpProfiles() {
    var intro = introJs($('#view')[0]);

    intro.setOptions({
        steps: [
            {
                element: '#filterAreaProfiles',
                intro: `Área para el filtrado y busqueda de registros de perfiles. <br/>
                        El filtrado se mostrará en el listado de perfiles`,
                position: 'bottom'
            },
            {
                element: '#filterIdCustomer',
                intro: 'Campo para filtar permisos por Cliente',
                position: 'bottom'
            },
            {
                element: '#filterIdCorporate',
                intro: 'Campo para filtar permisos por Planta',
                position: 'bottom'
            },
            @if ($create)
                {
                    element: '#buttonAddProfile',
                    intro: 'Clic sobre este botón para registrar un nuevo perfil',
                    position: 'left'
                },    
            @endif
            @if ($modify OR $delete)
                {
                    element: '#actionsProfiles',
                    intro: `En esta columna se encuntran las diferenctes acciones para cada perfil`,
                    position: 'left'
                },
                {
                    element: '#actionsProfiles',
                    intro: 'Haga clic sobre cada una de las acciones <br> Dentro cada ventana emergente encontrará Ayuda',
                    position: 'left'
                },
            @endif
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': false,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
    }).start();
}
/**
 * Help on add modal
 */
function helpAdd(){
    var intro = introJs($('#addModalIntro')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'Formulario para el registro de perfiles',
            },
            {
                element: '#s-idCustomer',
                intro: 'Campo para especificar el cliente al que pertenece el perfil',
                position: 'bottom'
            },
            {
                element: '#s-idCorporate',
                intro: 'Campo para especificar la planta al que pertenece el perfil',
                position: 'bottom'
            },
            {
                element: '#s-profile',
                intro: 'Campo para especificar el nombre del perfil con el que se identificará',
                position: 'bottom'
            },
            {
                element: '#s-idStatus',
                intro: 'Campo para especificar el estatus',
                position: 'bottom'
            },
            {
                element: '#s-idStatus',
                intro: 'Si el estatus es "Inactivo" no podra ser asignado a un usuario',
                position: 'bottom'
            },
            {
                element: '#s-typeProfile',
                intro: 'Nivel de operación que tendra el perfil',
                position: 'bottom'
            },
            {
                intro: 'Para guardar la información haga clic en el boton Registrar'
            },
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': false,
        'showStepNumbers': false,
        'showButtons': false,
        'showBullets': false,
    }).start();
}
/**
 * Help on add modal
 */
function helpEdit(){
    var intro = introJs($('#editModalIntro')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'Formulario para la actualización de perfiles',
            },
            {
                element: '#u-idCustomer',
                intro: 'Campo para especificar el cliente al que pertenece el perfil',
                position: 'bottom'
            },
            {
                element: '#u-idCorporate',
                intro: 'Campo para especificar la planta al que pertenece el perfil',
                position: 'bottom'
            },
            {
                element: '#u-profile',
                intro: 'Campo para especificar el nombre del perfil con el que se identificará',
                position: 'bottom'
            },
            {
                element: '#u-idStatus',
                intro: 'Campo para especificar el estatus',
                position: 'bottom'
            },
            {
                element: '#u-idStatus',
                intro: 'Si el estatus es "Inactivo" no podra ser asignado a un usuario',
                position: 'bottom'
            },
            {
                element: '#u-typeProfile',
                intro: 'Nivel de operación que tendra el perfil',
                position: 'bottom'
            },
            {
                intro: 'Para actualizar la información haga clic en el boton Actualizar'
            },
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': false,
        'showStepNumbers': false,
        'showButtons': false,
        'showBullets': false,
    }).start();
}
</script>