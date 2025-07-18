<script>
/**
 * Help on logos modal
 */
function helpLogos(){
    var intro = introJs($('#logosModalIntro')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'Se cuenta con tres formularios para cada imagen ',
            },
            {
                intro: 'Formulario "Logo", para la gestión de imágenes del corporativo <br/> Logo para lista de clientes',
            },
            {
                intro: 'Formulario "Logo pequeño", para la gestión de imágenes del corporativo <br/> Logo para área de menu cerrado',
            },
            {
                intro: 'Formulario "Logo largo", para la gestión de imágenes del corporativo <br/> Logo para área de menu abierto',
            },
            {
                element: '#setCustLogo',
                intro: 'Haga clic sobre este botón para buscar una imagen',
                position: 'bottom'
            },
            {
                element: '#setCustLogoPreview',
                intro: 'Aquí se muestra la imagen seleccionada',
                position: 'bottom'
            },
            {
                intro: 'Para guardar la imagen haga clic en el botón Guardar'
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