@php
    $create = (Session::get('create') == 1) ? true : false;
    $modify = (Session::get('modify') == 1) ? true : false;
    $delete = (Session::get('delete') == 1) ? true : false;
@endphp 
<script>
/**
 * Help on main view
 */
function helpLicense() {
    var intro = introJs($('#view')[0]);

    intro.setOptions({
        steps: [
            {
                element: '#filterAreaLicenses',
                intro: `Área para el filtrado y busqueda de registros de licencias. <br/>
                        El filtrado se mostrará en el listado de licencias`,
                position: 'bottom'
            },
            {
                element: '#filterLicense',
                intro: 'Campo para busqueda por Nombre de la licencia',
                position: 'bottom'
            },
            {
                element: '#filterIdStatus',
                intro: 'Campo de opción para busqueda por Estatus de la licencia',
                position: 'bottom'
            },
            @if ($create)
                {
                    element: '#buttonAddLicense',
                    intro: 'Clic sobre este botón para registrar una nueva licencia',
                    position: 'left'
                },
            @endif
            @if ($modify OR $delete)
                {
                    element: '#actionsLicenses',
                    intro: `En esta columna se encuntran las diferenctes acciones para cada licencia`,
                    position: 'left'
                },
                {
                    element: '#actionsLicenses',
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
                intro: 'Formulario para el registro de licencias'
            },
            {
                element: '#s-license',
                intro: 'Campo para especificar el nombre de la Licencia',
                position: 'bottom'
            },
            {
                element: '#s-usrGlobals',
                intro: 'Campo para especificar el número de usuarios de nivel global <br/> que estarán disponibles para el corporativo <br/> Este nivel cuenta con acceso a multiples corporativos',
                position: 'bottom'
            },
            {
                element: '#s-usrCorporates',
                intro: 'Campo para especificar el número de usuarios de nivel corporativo <br/> que estarán disponibles para el corporativo <br/> Este nivel es administrativo de un solo corporativo',
                position: 'bottom'
            },
            {
                element: '#s-usrOperatives',
                intro: 'Campo para especificar el número de usuarios de nivel operativo <br/> que estarán disponibles para el corporativo <br> Este nivel se limita a tareas dentro de su cuenta',
                position: 'bottom'
            },
            {
                element: '#s-idStatus',
                intro: 'Campo de opción para especificar el Estatus de la licencia',
                position: 'bottom'
            },
            {
                element: '#s-idPeriod',
                intro: 'Campo para especificar el periodo como unidad ',
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
                intro: 'Formulario para la actualización del registro de la licencia'
            },
            {
                element: '#u-license',
                intro: 'Campo para especificar el nombre de la Licencia',
                position: 'bottom'
            },
            {
                element: '#u-usrGlobals',
                intro: 'Campo para especificar el número de usuarios de nivel global <br/> que estarán disponibles para el corporativo <br/> Este nivel cuenta con acceso a multiples corporativos',
                position: 'bottom'
            },
            {
                element: '#u-usrCorporates',
                intro: 'Campo para especificar el número de usuarios de nivel corporativo <br/> que estarán disponibles para el corporativo <br/> Este nivel es administrativo de un solo corporativo',
                position: 'bottom'
            },
            {
                element: '#u-usrOperatives',
                intro: 'Campo para especificar el número de usuarios de nivel operativo <br/> que estarán disponibles para el corporativo <br> Este nivel se limita a tareas dentro de su cuenta',
                position: 'bottom'
            },
            {
                element: '#u-idStatus',
                intro: 'Campo de opción para especificar el Estatus de la licencia',
                position: 'bottom'
            },
            {
                element: '#u-idPeriod',
                intro: 'Campo para especificar el periodo como unidad ',
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