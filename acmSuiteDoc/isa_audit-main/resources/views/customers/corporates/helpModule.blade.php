<script>
/**
 * Help on address modal
 */
function helpAddress(){
    var intro = introJs($('#addressModal')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'En esta ventana podrás editar todos los datos relacionados a las direcciones Física y fiscal de una planta'
            },
            {
                intro: 'Los campos marcados con un asterisco son obligatorios'
            },
            {
                intro: 'Los demás campos son opcionales'
            },
            {
                intro: 'Para guardar la información haz clic en el botón Guardar'
            }
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
        'scrollToElement':false
    }).start();
}
/**
 * Help on add modal
 */
function helpContact(){
    var intro = introJs($('#contactModal')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'En esta ventana podrás editar todos los datos referentes al contacto de una planta'
            },
            {
                intro: 'Los campos marcados con un asterisco son obligatorios'
            },
            {
                intro: 'Los demás campos son opcionales'
            },
            {
                intro: 'Para guardar la información haz clic en el botón Guardar'
            }
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
        'scrollToElement':false
    }).start();
}
</script>