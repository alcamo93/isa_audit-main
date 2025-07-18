@php
    $create = (Session::get('create') == 1) ? true : false;
    $modify = (Session::get('modify') == 1) ? true : false;
    $delete = (Session::get('delete') == 1) ? true : false;
@endphp 
<script>
/**
 * Help on password modal
 */
function helpPassword(){
    var intro = introJs($('#passwordModal')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'En esta ventana podrás editar la contraseña de un usuario'
            },
            {
                intro: 'Ambos campos son obligatorios y deben contener la misma información'
            },
            {
                intro: 'Para guardar la información haz clic en el botón Guardar'
            },
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': false,
        'showBullets': false,
    }).start();
}
</script>