<div id="carouselnews" class="carousel slide" data-ride="carousel" data-step="1" data-intro="Apartado para noticias" data-position='right' data-scrollTo='tooltip'>
    <!-- Images-->
    <div class="carousel-inner">
        <div class="opacity-background"></div>
        <?php  $flag = 1; ?>
            @foreach($dataNew as $new)
                <?php if($flag == 1):?>
                <div class="carousel-item active">
                <?php else: ?>
                <div class="carousel-item">
                <?php endif; ?>
                <img class="d-block w-100" src="assets/img/news/new_{{$new['id_new']}}.jpg">
                <div class="carousel-caption d-none d-md-block">
                    @if($new['show_title']==1)
                        <h3 class="text-white" >{{$new['title']}}</h3>
                    @endif
                    @if($new['show_description']==1)
                    <p><label class="text-white" >{{$new['description']}}</label></p>
                    @endif
                </div>
                </div>
                <?php $flag++; ?>
            @endforeach
    </div>
    <!--controls-->
    <a class="carousel-control-prev" href="#carouselnews" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#carouselnews" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Siguiente</span>
    </a>
</div>
