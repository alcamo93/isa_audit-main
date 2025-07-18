<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada es una obra hidráulica?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada es una vía de comunicación?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada es una construcción de oleductos, gasoductos, carboductos y poliductos? (excepto los que se realicen en derechos de vía existentes en zonas agrícolas, ganaderas o eriales)',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra la construcción y operación de instalaciones de productos petroquímicos? ',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada corresponde a la industria química como lo es: construcción de parques o plantas industriales para la fabricación de sustancias químicas básicas; de productos químicos orgánicos; de derivados del petróleo, carbón, hule y plásticos; de colorantes y pigmentos sintéticos; de gases industriales, de explosivos y fuegos artificiales; de materias primas para fabricar plaguicidas, así como de productos químicos inorgánicos que manejen materiales considerados peligrosos?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada corresponde a la industria siderurgica  (Plantas para la fabricación, fundición, aleación, laminado y desbaste de hierro y acero, excepto cuando el proceso de fundición no esté integrado al de siderúrgica básica)?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada corresponde a la industria papelera, es decir, Construcción de plantas para la fabricación de papel y otros productos a base de pasta de celulosa primaria o secundaria, con excepción de la fabricación de productos de papel, cartón y sus derivados cuando ésta no esté integrada a la producción de materias primas.?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada corresponde a la industria azucarera, es decir, a la construcción de plantas para la producción de azúcares y productos residuales de la caña, con excepción de las plantas que no estén integradas al proceso de producción de la materia prima. ?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada corresponde a la industrial del cemento, es decir, la construcción de plantas para la fabricación de cemento, así como la producción de cal y yeso, cuando el proceso de producción esté integrado al de la fabricación de cemento?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada es una construcción de la industria eléctrica ubicada fuera de áreas urbanas, suburbanas, de equipamiento urbano o de servicios, rurales, agropecuarias, industriales o turísticas?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra la exploración, explotación y beneficio de minerales y sustancias reservadas a la federación?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra la construcción de instalaciones de tratamiento, confinamiento o eliminación de residuos peligrosos, así como residuos radiactivos?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra aprovechamientos forestales en selvas tropicales de recursos maderables y no maderables, especies de díficil regeneración, especies sujetas a protección o en áreas naturales protegidas?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra  cambios de uso de suelo en áreas forestales, así como en selvas y zonas áridas?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra la contrucción e instalación de parques industriales donde se prevea la realización de actividades altamente riesgosas?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra la construcción y operación de desarrollos inmobiliarios que afecten los ecosistemas costeros (hoteles, condominios, villas, desarrollos habitacionales y urbanos, restaurantes, instalaciones de comercio y servicios en general, marinas, muelles, rompeolas, campos de golf, infraestructura turística o urbana, vías generales de comunicación, obras de restitución o recuperación de playas, o arrecifes artificiales)?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra obras y actividades en humedales, manglares, lagunas, ríos, lagos y esteros conectados con el mar, así como en sus litorales o zonas federales?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra obras en áreas naturales protegidas?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra actividades pesqueras que puedan poner en peligro la preservación de una o más especies o causar daños a los ecosistemas (Actividades pesqueras de altamar, ribereñas o estuarinas, con fines comerciales e industriales que utilicen artes de pesca fijas o que impliquen la captura, extracción o colecta de especies amenazadas, sujetas a protección especial, en extinción o en veda permanente)?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra actividades acuícolas que puedan poner en peligro la preservación de una o mas actividades acuícolas puedan poner en peligro la preservación de una o más especies o causar daños a los ecosistemas (Construcción y operación de granjas, estanques o parques de producción acuícola; producción de postlarvas, semilla o simiente; siembra de especies exóticas, híbridos y variedades transgénicas: construcción o instalación de arrecifes articiales u otros medios de modificaicón del hábitat?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La obra o actividad realizada involucra actividades agropecuarias puedan poner en peligro la preservación de una o más especies o causar daños a los ecosistemas ( (implicando cambio de uso de suelo con excepción de autoconsumo familiar, y la utilización de las técnicas y metodologías de la agricultura orgánica)?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿Su instalación maneja sustancias que rebasan la cantidad de reporte de los 2 listados publicados por SEMARNAT, que son el 1er. listado de Actividades Altamente Riesgosas (Manejo de sustancias tóxicas) y 2do. listado (Manejo de Sustancias inflamables y explosivas)?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
            'help_question' => 'Solicitar un listado de sustancias quimicas para comparar las cantidades señaladas en el primer o segundo listado de actividades altamente riesgosas.Los listados solo lista sustancias explosivas, inflamables y tóxicas. '
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La instalación ha realizado modificaciones, ampliaciones,sustituciones de infraestructura, rehabilitación y el mantenimiento al proyecto originial inicial?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La instalación está llevando a cabo o tiene previsto llevar a cabo un cambio de titularidad o el cierre de alguno de sus centros?',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La instalación dejó de ejecutar o planea dejar de ejecutar una obra o actividad sujeta a autorización en materia de impacto ambiental? (proyecto autorizado? ',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
        \DB::table('t_questions')->insert([
            'question' => '¿La instalación ha recibido visitas de inspección por parte de la Procuraduría Federal de Protección al Ambiente? ',
            'order' => 1,
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 1,
            'id_question_type' => 1,
        ]);
    }
}