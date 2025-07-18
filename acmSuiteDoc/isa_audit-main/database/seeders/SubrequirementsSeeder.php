<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubrequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'a',
            'subrequirement' => 'Autorización de impacto ambiental vigente ',
            'description' => 'La instalación no excede el tiempo propuesto o vigencia señalada en la autorización de impacto ambiental. ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_subrequirement' => 'Se debe revisar que las autorizaciones se encuentren vigentes para las actividades que realiza actualmente la empresa. Si esta proxima a vencer incluir la recomendación de realizar el tramite de extensión de vigencia. ',
            'acceptance' => 'Autorización vigente o prorroga',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_requirement' => 6,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'b',
            'subrequirement' => 'Aviso de inicio de actividades',
            'description' => 'Aviso de inicio de proyecto',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_subrequirement' => 'Se debe revisar el oficio de autorización para identificar las etapas que incluyó el proyecto tales como preparación del sitio, construcción, instalación, operación y mantenimiento asi como el tiempo establecido para dar el aviso. Si el proyecto tiene una antigüedad mayor a 5 años no es necesario que cuente con la evidencia de un aviso de mayor antigüedad. ',
            'acceptance' => 'Acuse de ingreso o constancia de recepción del aviso de inicio de proyecto. 
            Avisos que deberion ser notificados de 5 años en delante de antigüedad no es necesario contar con evidencia. ',
            'id_obtaining_period' => 2,
            'id_update_period' => 2,
            'id_condition' => 1,
            'id_requirement' => 6,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'c',
            'subrequirement' => 'Aviso de conclusión de proyecto',
            'description' => 'Aviso de conclusión de proyecto',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'Acuse de ingreso o constancia de recepción del aviso de conclusión de proyecto. 
            Avisos que deberion ser notificados de 5 años en delante de antigüedad no es necesario contar con evidencia.',
            'id_obtaining_period' => 2,
            'id_update_period' => 2,
            'id_condition' => 1,
            'id_requirement' => 6,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'd',
            'subrequirement' => 'Aviso de cambio de titularidad',
            'description' => 'Cambio de titularidad',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'Autorización de cambio de titularidad',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_requirement' => 6,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);


        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'a',
            'subrequirement' => 'Cumplimiento de medidas de seguridad, correctivas o de urgente aplicación',
            'description' => 'Derivado de una vista de inspección la instalación cumplió y notificó a la autoridad el cumplimiento de cada una de las  medidas de seguridad, correctivas o de urgente aplicación en un plazo máximo de 5 días apartir de la fecha de vencimiento del plazo concebido. ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'Acuse de ingresó de la presentación del informe de cumplimiento de medidas de seguridad, correctivas o de urgencia aplicación ante la autoridad dentro del período establecido. ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_requirement' => 8,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'b',
            'subrequirement' => 'Respueta a acta de inspeción ',
            'description' => 'En caso de visita de inspección por la autoridad,  instalación presentó dentro de los 5 días posteriores a la fecha en que la diligencia se hubiere practicado las pruebas convenientes a los hechos u omisiones asentados en el acta de inspección. ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'Acuse de ingreso de la presentación de pruebas convenientes a los hechos u omisiones asentados en el acta de inspección. ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_requirement' => 8,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'c',
            'subrequirement' => 'Respuesta a acta de inspección ',
            'description' => 'Una vez recibida la acta de inspección por la autoridad, la instalación recibió mediante notificación personal o por correo las medidas correctivas o de urge aplicación así como el plazo para su cumplimiento. Se cuenta con evidencia de su cumplimiento o bien la instalación en un termino de 15 días expuso lo que a su derecho convenga.',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'Acuse de ingreso de respuesta a acta de inspección ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_requirement' => 8,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
        \DB::table('t_subrequirements')->insert([
            'no_subrequirement' => 'd',
            'subrequirement' => 'Respuesta a disposición de actuaciones ',
            'description' => 'Si la instalación a admitido y desahogados las pruebas referente al acta de inspección habiendo transcurrido el tiempo establecido en un plazo de tres días habiles presentó sus alegatos derivada de la disposición de actuaciones ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'Acuse de ingreso de respuesta a disposción de actuaciones ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_requirement' => 8,
            'id_requirement_type' =>  6,
            'id_application_type' =>  1,
        ]);
    }
}