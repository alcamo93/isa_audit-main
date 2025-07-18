<script>
/**
 * Help on main view
 */
function helpPermissions() {
    var intro = introJs($('#view')[0]);

    intro.setOptions({
        steps: [
            {
                element: '#filterAreaPermissions',
                intro: `En el área de filtrado encontrarás todas las herramientas necesarias para obtener los registros que buscas, cada campo en este área agrega un nivel de filtro a tu búsqueda `,
                position: 'bottom'
            },
            {
                element: '#actionVisualize',
                intro: `En esta columna se encuentra el control del permiso Visualizar`,
                position: 'right'
            },
            {
                element: '#actionVisualize',
                intro: `Hace referencia al poder mostrar el menu dentro la sesíón del usuario según su Perfil`,
                position: 'right'
            },
            {
                element: '#actionCreate',
                intro: `En esta columna se encuentra el control del permiso Crear`,
                position: 'left'
            },
            {
                element: '#actionCreate',
                intro: `Hace referencia a las acciones de crear nuevos registros dentro del módulo`,
                position: 'left'
            },
            {
                element: '#actionModify',
                intro: `En esta columna se encuentra el control del permiso Modificar`,
                position: 'left'
            },
            {
                element: '#actionModify',
                intro: `Hace referencia a las acciones de poder modificar los registros dentro del módulo existentes`,
                position: 'left'
            },
            {
                element: '#actionDelete',
                intro: `En esta columna se encuentra el control del permiso Eliminar`,
                position: 'left'
            },
            {
                element: '#actionDelete',
                intro: `Hace referencia a las acciones de poder eliminar los registros dentro del módulo existentes`,
                position: 'left'
            },
            {
                element: '#actionSubmodule',
                intro: `En esta columna se encuentra el control para acceder a los permisos de cada submódulo`,
                position: 'left'
            },
            {
                element: '#actionSubmodule',
                intro: `Hace referencia a las acciones de poder eliminar los registros dentro del módulo existentes`,
                position: 'left'
            },
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
    }).start();
}
/**
 * Help on submodule view
 */
function helpSubPermissions() {
    var intro = introJs($('#view')[0]);

    intro.setOptions({
        steps: [
            {
                element: '#backToPermissions',
                intro: `Botón para regresar a Permisos de Módulos`,
                position: 'left'
            },
            {
                element: '#actionSubVisualize',
                intro: `En esta columna se encuentra el control del permiso Visualizar`,
                position: 'right'
            },
            {
                element: '#actionSubVisualize',
                intro: `Hace referencia al poder mostrar el menu dentro la sesíón del usuario según su Perfil`,
                position: 'right'
            },
            {
                element: '#actionSubCreate',
                intro: `En esta columna se encuentra el control del permiso Crear`,
                position: 'left'
            },
            {
                element: '#actionSubCreate',
                intro: `Hace referencia a las acciones de crear nuevos registros dentro del submódulo`,
                position: 'left'
            },
            {
                element: '#actionSubModify',
                intro: `En esta columna se encuentra el control del permiso Modificar`,
                position: 'left'
            },
            {
                element: '#actionSubModify',
                intro: `Hace referencia a las acciones de poder modificar los registros dentro del submódulo existentes`,
                position: 'left'
            },
            {
                element: '#actionSubDelete',
                intro: `En esta columna se encuentra el control del permiso Eliminar`,
                position: 'left'
            },
            {
                element: '#actionSubDelete',
                intro: `Hace referencia a las acciones de poder eliminar los registros dentro del submódulo existentes`,
                position: 'left'
            }
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
    }).start();
}
</script>