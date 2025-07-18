<!-- Show aspects questions by matter -->
<div class="row question-card d-none">
    <div class="col">
        <div class="card card-wizard">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button type="button" class="close my-close" data-toggle="tooltip" 
                            title="Ayuda" onclick="helpQuiz();">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right ml-2" data-toggle="tooltip" 
                            title="Regresar" onclick="closeQuiz();">
                            Regresar
                        </button>
                        <button type="button" class="btn btn-primary float-right" data-toggle="tooltip" 
                            title="Mostrar Faltantes" onclick="evaluateWizard();">
                            Mostrar Faltantes
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">Nombre de evaluación</label>
                            <div class="auditTitle"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">Alcance</label>
                            <div class="scopeAudit"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">Materia</label>
                            <div id="quizMatterTitle"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">Aspecto</label>
                            <div id="quizAspectTitle"></div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="wizardForm" action="">
                
            </form>
        </div>
    </div>
</div>