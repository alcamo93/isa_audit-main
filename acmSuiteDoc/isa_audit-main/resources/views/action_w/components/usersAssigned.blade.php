<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Responsable de {{ $typeTitle }}<span class="star">*</span></label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <button class="btn btn-danger btn-xs" id="{{ $idBtn }}Primary" 
                        data-toggle="tooltip" title="Remover Usuario" 
                        onclick="{{ $function }}(0)" type="button">
                        <i class="fa fa-trash la-lg"></i>
                    </button>
                </div>
                <select id="{{ $idUser }}-primary" name="{{ $idUser }}-primary" class="form-control user-list {{ $classDisabled }}"
                    style="flex: 1 1 45% !important;"
                    onchange="{{ $object }}[0].id_user = this.value"
                    title="Selecciona una opción" required>
                    <option value=""></option>
                </select>
                <input 
                    id="{{ $days }}-primary" name="{{ $days }}-primary" type="number" class="form-control inputs-forms {{ $classDisabled }}" 
                    onkeyup="{{ $object }}[0].days = this.value"
                    data-rule-required="true" 
                    data-msg-required="Defina número de días"
                    placeholder="# Días"
                />
                <div class="input-group-prepend">
                    <label for="{{ $days }}-primary" class="input-group-text {{ $classDisabled }}" style="margin-bottom: 3px !important;">
                        Días antes del cierre Notificar
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Responsable secundario</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <button class="btn btn-danger btn-xs {{ $classDisabled }}" 
                        data-toggle="tooltip" title="Remover Usuario" 
                        onclick="{{ $function }}(1)" type="button">
                        <i class="fa fa-trash la-lg"></i>
                    </button>
                </div>
                <select id="{{ $idUser }}-secondary" name="{{ $idUser }}-secondary" class="form-control user-list {{ $classDisabled }}"
                    style="flex: 1 1 45% !important;"
                    onchange="{{ $object }}[1].id_user = this.value"
                    title="Selecciona una opción">
                    <option value=""></option>
                </select>
                <input 
                    id="{{ $days }}-secondary" name="{{ $days }}-secondary" type="number" class="form-control inputs-forms {{ $classDisabled }}" 
                    onkeyup="{{ $object }}[1].days = this.value"
                    placeholder="# Días"
                />
                <div class="input-group-prepend">
                    <label class="input-group-text" style="margin-bottom: 3px !important;">
                        Días antes del cierre Notificar
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Responsable terciario</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <button class="btn btn-danger btn-xs {{ $classDisabled }}" 
                        data-toggle="tooltip" title="Remover Usuario" 
                        onclick="{{ $function }}(2)" type="button">
                        <i class="fa fa-trash la-lg"></i>
                    </button>
                </div>
                <select id="{{ $idUser }}-terceary" name="{{ $idUser }}-terceary" class="form-control user-list {{ $classDisabled }}"
                    style="flex: 1 1 45% !important;"
                    onchange="{{ $object }}[2].id_user = this.value"
                    title="Selecciona una opción" >
                    <option value=""></option>
                </select>
                <input 
                    id="{{ $days }}-terceary" name="{{ $days }}-terceary" type="number" class="form-control inputs-forms {{ $classDisabled }}" 
                    onkeyup="{{ $object }}[2].days = this.value"
                    placeholder="# Días"
                />
                <div class="input-group-prepend">
                    <label class="input-group-text" style="margin-bottom: 3px !important;">
                        Días antes del cierre Notificar
                    </label>
                </div>
            </div>        
        </div>
    </div>
</div>