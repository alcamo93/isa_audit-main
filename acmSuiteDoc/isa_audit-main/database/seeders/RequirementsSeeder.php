<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-01',
            'requirement' => 'Autorización de Impacto Ambiental ',
            'description' => 'La instalación cuenta con la autorización en materia de impacto ambiental previo al inicio de las actividades. ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_requirement' => 'La instalación debe contar con la autorización en materia de impacto ambiental mediante un oficio  expedido por la SEMARNAT para las actividades que se realizan actualmente dentro del establecimiento. 
            En caso de informes preventivos estos podrán no contar con un oficio de autorización si se cumple con lo dispuesto en el articulo 33 del RLGEEPAMEIA (revisar) y solo será necesario contar con el acuse.',
            'acceptance' => 'La autorización en materia de impacto ambiental debe ser  vigente e incluir las actividades actuales que se realizan dentro de la instalación.',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-02',
            'requirement' => 'Manifestación de Impacto ambiental y Riesgo Ambiental ',
            'description' => 'Si la instalación realiza actividades altamente riesgosas presentó junto con la manifestación de impacto ambiental un estudio de riesgo ambiental. ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_requirement' => 'El cliente debe presentar el acuse de ingreso de la Manifestación de Impacto Ambiental en la modalidad aplicable y del Estudio de Riesgo Ambiental ',
            'acceptance' => 'Acuse de ingreso o constancia de recepción de Manifestación de Impacto Ambiental y Estudio de Riesgo Ambiental ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 1,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-03',
            'requirement' => 'Formular y Presentar el Estudio de Riesgo Ambiental',
            'description' => 'Si la instalación lleva acabo actividades altamente riesgosas presentó un Estudio de Riesgo Ambiental a SEMARNAT ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_requirement' => 'Solicitar el acuse de ingreso del Estudio de Riesgo Ambiental asi como el estudio. 
            Ojo: de acuerdo a las guías federales solo aplica autorización previa de riesgo ambiental cuando implica impacto ambiental con actividad altamente riesgosa.  Si la operación no aplica impacto ambiental federal, el estudio de riesgo ambiental se presenta una vez estando en operación. ',
            'acceptance' => 'Contar con acuse de ingresó o  constancia de recepción que contenga el número de bitácora así como el estudio de riesgo ambiental el cual debe incluir todas las sustancias identificadas como actividades altamente riesgosas de acuerdo a los listados publicados por la SEMARNAT. ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-04',
            'requirement' => 'Aviso previo a SEMARNAT de obras no autorizadas- oficio de exención o  autorización en materia de impacto ambiental por modificaciones',
            'description' => 'Si la instalación realiza actividades clasificadas como actividades altamente riesgosas cuenta con un seguro de riesgo ambiental del Sistema Nacional de Seguros de Riesgo Ambiental. ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_requirement' => 'El cliente debe presentar poliza de seguro de riesgo ambiental vigente. Si esta a 24 horas de vencer solicitar evidencia de su renovación.  ',
            'acceptance' => 'Póliza de seguro ambiental vigente al día de la auditoría. ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-05',
            'requirement' => 'Cumplimiento a términos y condiciones de la Autorización de Impacto Ambiental',
            'description' => 'La instalación cumple con los términos y condiciones establecidos en la  autorización de impacto ambiental',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_requirement' => 'Se debe solicitar al cliente el oficio de autorización de impacto ambiental expedido por la autoridad. El cumplimiento a los términos y condicionantes debe revisarse de acuerdo a los periodos establecidos en el oficio tomando como evidencia los acuses de ingreso o constancia de recepción de entrega a la autoridad. ',
            'acceptance' => 'Acuse de ingreso o constancia de recepción del cumplimiento a los términos y condicionantes para cada una de las etapas del proyecto aplicables y de acuerdo a los tiempos establecidos en el oficio de autorización de impacto ambiental expedido por la autoridad. ',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => '01-AMB-IRA',
            'requirement' => 'NADA',
            'description' => 'Si la instalación cuenta con las autorizaciones que expida la Secretaría las cuales sólo podrán referirse a los aspectos ambientales de las obras o actividades de que se trate y deberá',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 5,
            'has_subrequirement' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => '01-AMB-IRA-AGS',
            'requirement' => 'NADA 2',
            'description' => 'Si la instalación cuenta con las autorizaciones que expida la Secretaría las cuales sólo podrán referirse a los aspectos ambientales de las obras o actividades de que se trate y deberá',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 2,
            'id_application_type' => 2,
            'id_requirement_type' => 5,
            'has_subrequirement' => 1,
            'id_state' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-07',
            'requirement' => 'Renovación de seguros y/o garantía por daños ambientales ',
            'description' => 'La instalación renueva o actualiza anualmente los montos de los seguros o garantías relacionadas al  cumplimiento de las condicionantes establecidas en las autorizaciones o en su caso cuenta con el documento de cancelación por parte de la SEMARNAT',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'help_requirement' => 'De lo contrario presentar el oficio de cancelación del seguro o garantía. 
            Los seguros aplican para aquellos que manejan actividades altamente riesgos y pueden ser solicitados por la SECRETARIA dentro de las condicionantes de permisos federales en base al articulo 51 del RMIA para obras que puedan producir daños graves a los ecosistemas. ',
            'acceptance' => 'El cliente debe presentar el seguro y/o garantía vigente o en su caso el documento de cancelación expedido por la SEMARNAT',
            'id_obtaining_period' => 3,
            'id_update_period' => 3,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 1,

        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-08',
            'requirement' => 'Respuesta a resolución administrativa ',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'id_obtaining_period' => 4,
            'id_update_period' => 4,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
        \DB::table('t_requirements')->insert([
            'no_requirement' => 'AMB-IRA-09',
            'requirement' => 'Pago de derechos',
            'description' => 'La instalación ha pagado las cuotas correspondientes a cada una de las concesiones, permisos, autorizaciones y/o actividades llevadas a cabo de acuerdo con la Ley Federal de Derechos.',
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_audit_type' => 1,
            'order' => 1,
            'acceptance' => 'comprobante de pago ( si el tramite se realizó hace mas de 5 años no es obligatorio mostrarlo y esta en cumplimiento). ',
            'id_obtaining_period' => 1,
            'id_update_period' => 1,
            'id_condition' => 2,
            'id_application_type' => 1,
            'id_requirement_type' => 1
        ]);
    }
}