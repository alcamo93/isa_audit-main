@php
    $modify = (Session::pull('modify') == 1) ? true : false;   
@endphp 
<script>
/**
 * Help on main view
 */
function helpNews() {
    var intro = introJs($('#view')[0]);
    $('#areaNews').scrollintoview({ duration: 1, dirección : "vertical", viewPadding: { y: 100 } });
    intro.setOptions({
        steps: [
            @if ($modify)
            {
                element: '#buttonAddNews',
                intro: 'Da clic sobre este botón para generar un nuevo registro',
                position: 'left'
            },
            @endif
            {
                element: '#actionsNews',
                intro: `En esta columna se encuentran las diferentes acciones para interactuar con los registros existentes `,
                position: 'left'
            },
            {
                element: '#actionsNews',
                intro: `Haz clic sobre cada una de las acciones, dentro de cada ventana emergente encontrarás Ayuda`,
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
 * Help on add modal
 */
function helpAdd(main){
    var intro = introJs($(main)[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'Los campos marcados con un asterisco son obligatorios'
            },
            {
                intro: 'Los demás campos son opcionales'
            },
            {
                intro: 'Selecciona qué campos se mostrarán en el área de noticias del dashboard'
            },
            {
                intro: 'Para guardar la información haz clic en el botón Registrar/Actualizar'
            }
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': false,
        'showBullets': false,
        'scrollToElement': true,
    }).start();
}

</script>