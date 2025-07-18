<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_modules')->insert([
            'name_module'=> 'Tablero de control',
            'pseud_module'=> 'Tablero de control',
            'path' => 'dashboard',
            'color_module' => 'orange',
            'icon_module' => 'chart-pie-35',
            'id_status' => 1,
            'has_submodule' => 0,
            'sequence' => 1,
            'owner' => 2,
            'description' => 'En este módulo podrás encontrar el porcentaje de cumplimiento legal global y por materia. Además de encontrar una sección de noticias ambientales.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Clientes',
            'pseud_module'=> 'Clientes',
            'path' => 'customers' ,
            'color_module' => 'purple',
            'icon_module' => 'circle-09',
            'id_status' => 1,
            'has_submodule' => 0,
            'sequence' => 2,
            'owner' => 1,
            'description' => 'En este módulo se podrá dar de alta o de baja clientes para el uso del software, también aquí se puede encontrar toda la información general de cada uno.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Licenciamiento',
            'pseud_module'=> 'Licenciamiento',
            'path' => 'licenses',
            'color_module' => 'fluorescent',
            'icon_module' => 'cart-simple',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 3,
            'owner' => 1,
            'description' => 'En este módulo se podrá dar de alta o de baja licencias para el uso del software'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Contratos',
            'pseud_module'=> 'Contratos',
            'path' => 'contracts',
            'color_module' => 'yellow',
            'icon_module' => 'bag',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 4,
            'owner' => 1,
            'description' => 'En este módulo se podrá dar de alta y consultar los contratos del cliente según su licenciamiento para el uso del software'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Perfiles',
            'pseud_module'=> 'Perfiles',
            'path' => 'profiles',
            'color_module' => 'aqua',
            'icon_module' => 'single-02',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 5,
            'owner' => 0,
            'description' => 'Este módulo tiene como propósito el habilitar o deshabilitar restricciones para los usuarios de acuerdo con su perfil.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Permisos',
            'pseud_module'=> 'Permisos',
            'path' => 'permissions',
            'color_module' => 'red',
            'icon_module' => 'lock-circle-open',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 6,
            'owner' => 0,
            'description' => 'En este módulo se puede agregar o eliminar permisos de acuerdo con el tipo de perfil.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Usuarios',
            'pseud_module'=> 'Usuarios',
            'path' => 'users',
            'color_module' => 'green',
            'icon_module' => 'badge',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 7,
            'owner' => 0,
            'description' => 'En este módulo se puede agregar o eliminar usuarios, aquí se podrá encontrar todos los usuarios registrados indicando información general, así como el perfil de cada uno de ellos.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Catálogos',
            'pseud_module'=> 'Catálogos',
            'color_module' => 'azure',
            'icon_module' => 'layers-3',
            'id_status' => 1, 
            'has_submodule' => 1,
            'sequence' => 9,
            'owner' => 1,
            'description' => 'Este módulo es para los administradores del sistema, debido a que se gestionará los requerimientos, leyes, reglamentos, normas para la conformidad de requisitos legales.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Noticias',
            'pseud_module'=> 'Noticias',
            'path' => 'news',
            'color_module' => 'rose',
            'icon_module' => 'paper-2',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 10,
            'owner' => 1,
            'description' => 'Aquí se podrá cargar o eliminar las noticias más actualizadas para información del usuario.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Aplicabilidad',
            'pseud_module'=> 'Aplicabilidad',
            'path' => 'aplicability',
            'color_module' => 'blue',
            'icon_module' => 'bullet-list-67',
            'id_status' => 2, 
            'has_submodule' => 0,
            'sequence' => 11,
            'owner' => 0,
            'description' => 'En este módulo se encuentra el listado de preguntas a responder para conocer la legislación aplicable y a auditar.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Auditoría',
            'pseud_module'=> 'Auditoría',
            'path' => 'audit',
            'color_module' => 'yellow',
            'icon_module' => 'notes',
            'id_status' => 2, 
            'has_submodule' => 0,
            'sequence' => 12,
            'owner' => 0,
            'description' => 'En este módulo se encuentra los requisitos legales que se deben cumplir en base a la legislación aplicable y donde se obtiene como resultado un reporte del nivel de cumplimiento legal.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Auditoría',
            'pseud_module'=> 'Procesos de Auditoría',
            'path' => 'processes',
            'color_module' => 'yellow',
            'icon_module' => 'notes',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 13,
            'owner' => 0,
            'description' => 'En este módulo se encuentra los requisitos legales que se deben cumplir en base a la legislación aplicable y donde se obtiene como resultado un reporte del nivel de cumplimiento legal.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Plan de acción',
            'pseud_module'=> 'Plan de acción',
            'path' => 'action',
            'color_module' => 'red',
            'icon_module' => 'attach-87',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 14,
            'owner' => 0,
            'description' => 'En este módulo se encontrará las recomendaciones resultantes de la auditoría donde se deberá indicar la fecha compromiso de cierre de hallazgos.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Obligaciones',
            'pseud_module'=> 'Obligaciones',
            'path' => 'obligations',
            'color_module' => 'fluorescent',
            'icon_module' => 'single-copy-04',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 15,
            'owner' => 0,
            'description' => 'En este módulo se encuentra las obligaciones reglamentales.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Requerimientos',
            'pseud_module'=> 'Requerimientos Específicos',
            'path' => 'requirements',
            'color_module' => 'purple',
            'icon_module' => 'grid-45',
            'id_status' => 1, 
            'has_submodule' => 0,
            'sequence' => 8,
            'owner' => 0,
            'description' => 'Registra los requerimientos de la planta que hayan sido establecidos mediante los resolutivos  de los tramites (condicionantes), corporativo de la empresa, etc.'
        ]);
        \DB::table('c_modules')->insert([
            'name_module'=> 'Biblioteca interna',
            'pseud_module'=> 'Biblioteca interna',
            'path' => 'library',
            'color_module' => 'purple',
            'icon_module' => 'single-copy-04',
            'id_status' => 1,
            'has_submodule' => 0,
            'sequence' => 14,
            'owner' => 0,
            'description' => 'Consulta los documentos que fueron cargados como evidencia de cumplimiento durante la auditoria. Agrega documentos y almacénalos para crear una biblioteca personal en el software.'
        ]);
    }
}
