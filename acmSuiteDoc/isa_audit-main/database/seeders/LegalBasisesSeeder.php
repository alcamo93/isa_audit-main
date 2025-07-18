<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LegalBasisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_legal_basises')->insert([ //1
            'legal_basis' => 'Art. 5 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Quienes pretendan llevar a cabo alguna de las siguientes obras o actividades,requerirán previamente la autorización de la Secretaría en materia de impacto ambiental:….',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //2
            'legal_basis' => 'Art. 147 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'La realización de actividades industriales, comerciales o de servicios altamente riesgosas, se llevarán a cabo con apego a lo dispuesto por esta Ley, las disposiciones reglamentarias que de ella emanen y las normas oficiales mexicanas a que se refiere el artículo anterior.
            Quienes realicen actividades altamente riesgosas, en los términos del Reglamento correspondiente, deberán formular y presentar a la Secretaría un estudio de riesgo ambiental, así como someter a la aprobación de dicha dependencia y de las Secretarías de Gobernación, de Energía, de Comercio y Fomento Industrial, de Salud, y del Trabajo y Previsión Social, los programas para la prevención de accidentes en la realización de tales actividades, que puedan causar graves desequilibrios ecológicos.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //3
            'legal_basis' => 'Art. 6 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Las ampliaciones, modificaciones, sustituciones de infraestructura, rehabilitación y el mantenimiento de instalaciones relacionado con las obras y actividades señaladas en el artículo anterior, así como con las que se encuentren en operación, no requerirán de la autorización en materia de impacto ambiental siempre y cuando cumplan con todos los requisitos siguientes:
                I. Las obras y actividades cuenten previamente con la autorización respectiva o cuando no hubieren requerido de ésta;
                II. Las acciones por realizar no tengan relación alguna con el proceso de producción que generó dicha autorización, y
                III. Dichas acciones no impliquen incremento alguno en el nivel de impacto o riesgo ambiental, en virtud de su ubicación, dimensiones, características o alcances, tales como conservación, reparación y mantenimiento de bienes inmuebles; construcción, instalación y demolición de bienes inmuebles en áreas urbanas, o modificación de bienes inmuebles cuando se pretenda llevar a cabo en la superficie del terreno ocupada por la construcción o instalación de que se trate. En estos casos, los interesados deberán dar aviso a la Secretaría previamente a la realización de dichas acciones.
                Las ampliaciones, modificaciones, sustitución de infraestructura, rehabilitación y el mantenimiento de instalaciones relacionadas con las obras y actividades señaladas en el artículo 5o., así como con las que se encuentren en operación y que sean distintas a las que se refiere el primer párrafo de este artículo, podrán ser exentadas de la presentación de la manifestación de impacto ambiental cuando se demuestre que su ejecución no causará desequilibrios ecológicos ni rebasará los límites y condiciones establecidos en las disposiciones jurídicas relativas a la protección al ambiente y a la preservación y restauración de los ecosistemas.
                Para efectos del párrafo anterior, los promoventes deberán dar aviso a la Secretaría de las acciones que pretendan realizar para que ésta, dentro del plazo de diez días, determine si es necesaria la presentación de una manifestación de impacto ambiental, o si las acciones no requieren ser evaluadas y, por lo tanto, pueden realizarse sin contar con autorización.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //4
            'legal_basis' => 'Art. 28 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Si el promovente pretende realizar modificaciones al proyecto después de emitida la autorización en materia de impacto ambiental, deberá someterlas a la consideración de la Secretaría, la que, en un plazo no mayor a diez días, determinará:
                I. Si es necesaria la presentación de una nueva manifestación de impacto ambiental;
                II. Si las modificaciones propuestas no afectan el contenido de la autorización otorgada, o
                III. Si la autorización otorgada requiere ser modificada con objeto de imponer nuevas condiciones a la realización de la obra o actividad de que se trata.
                En este último caso, las modificaciones a la autorización deberán ser dadas a conocer al promovente en un plazo máximo de veinte días.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //5
            'legal_basis' => 'Art. 49 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Las autorizaciones que expida la Secretaría sólo podrán referirse a los aspectos ambientales de las obras o actividades de que se trate y su vigencia no podrá exceder del tiempo propuesto para la ejecución de éstas.
            Asimismo, los promoventes deberán dar aviso a la Secretaría del inicio y la conclusión de los proyectos, así como del cambio en su titular',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //6
            'legal_basis' => 'Art. 164 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'Recibida el acta de inspección por la autoridad ordenadora, requerirá al interesado, cuando proceda, mediante notificación personal o por correo certificado con acuse de recibo, para que adopte de inmediato las medidas correctivas o de urgente aplicación que, en su caso, resulten necesarias para cumplir con las disposiciones jurídicas aplicables, así como con los permisos, licencias,  autorizaciones o concesiones respectivas, señalando el plazo que corresponda para su cumplimiento, fundando y motivando el requerimiento. Asimismo, deberá señalarse al interesado que cuenta con un término de quince días para que exponga lo que a su derecho convenga y, en su caso, aporte las pruebas que considere procedentes en relación con la actuación de la Secretaría.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //7
            'legal_basis' => 'Art. 167 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'Recibida el acta de inspección por la autoridad ordenadora, requerirá al interesado, cuando proceda, mediante notificación personal o por correo certificado con acuse de recibo, para que adopte de inmediato las medidas correctivas o de urgente aplicación que, en su caso, resulten necesarias para cumplir con las disposiciones jurídicas aplicables, así como con los permisos, licencias,  autorizaciones o concesiones respectivas, señalando el plazo que corresponda para su cumplimiento, fundando y motivando el requerimiento. Asimismo, deberá señalarse al interesado que cuenta con un término de quince días para que exponga lo que a su derecho convenga y, en su caso, aporte las pruebas que considere procedentes en relación con la actuación de la Secretaría.',
            'id_application_type' => 1,
        ]);

        \DB::table('t_legal_basises')->insert([ //8
            'legal_basis' => 'Art. 9 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'Los promoventes deberán presentar ante la Secretaría una manifestación de impacto ambiental, en la modalidad que corresponda, para que ésta realice la evaluación del proyecto de la obra o actividad respecto de la que se solicita autorización.
            La Información que contenga la manifestación de impacto ambiental deberá referirse a circunstancias ambientales relevantes vinculadas con la realización del proyecto.
            La Secretaría proporcionará a los promoventes guías para facilitar la presentación y entrega de la manifestación de impacto ambiental de acuerdo al tipo de obra o actividad que se pretenda llevar a cabo. La Secretaría publicará dichas guías en el Diario Oficial de la Federación y en la Gaceta Ecológica.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //9
            'legal_basis' => 'Art. 29 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'La evaluación del impacto ambiental es el procedimiento a través del cual la Secretaría establece las condiciones a que se sujetará la realización de obras y actividades que puedan causar desequilibrio ecológico o rebasar los límites y condiciones establecidos en las disposiciones aplicables para proteger el ambiente y preservar y restaurar los ecosistemas, a fin de evitar o reducir al mínimo sus efectos negativos sobre el medio ambiente. Para ello, en los casos en que determine el Reglamento que al efecto se expida, quienes pretendan llevar a cabo alguna de las siguientes obras o actividades, requerirán previamente la autorización en materia de impacto ambiental de la Secretaría.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //10
            'legal_basis' => 'Art. 33 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'La Secretaría analizará el informe preventivo y, en un plazo no mayor a veinte días, notificará al promovente:
                I. Que se encuentra en los supuestos previstos en el artículo 28 de este reglamento y que, por lo tanto, puede realizar la obra o actividad en los términos propuestos, o
                II. Que se requiere la presentación de una manifestación de impacto ambiental, en alguna de sus modalidades. 
                Tratándose de informes preventivos en los que los impactos de las obras o actividades a que se refieren se encuentren totalmente regulados por las normas oficiales mexicanas, transcurrido el plazo a que se refiere este artículo sin que la Secretaría haga la notificación correspondiente, se entenderá que dichas obras o actividades podrán llevarse a cabo en la forma en la que fueron proyectadas y de acuerdo con las mismas normas.',
            'id_application_type' => 1,
        ]);

        \DB::table('t_legal_basises')->insert([ //11
            'legal_basis' => 'Art. 31 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'La realización de las obras y actividades a que se refieren las fracciones I a XII del artículo 28, requerirán la presentación de un informe preventivo y no una manifestación de impacto ambiental, cuando:
                I.- Existan normas oficiales mexicanas u otras disposiciones que regulen las emisiones, las descargas, el aprovechamiento de recursos naturales y, en general, todos los impactos ambientales relevantes que puedan producir las obras o actividades;
                II.- Las obras o actividades de que se trate estén expresamente previstas por un plan parcial de desarrollo urbano o de ordenamiento ecológico que haya sido evaluado por la Secretaría en los términos del artículo siguiente, o
                III.- Se trate de instalaciones ubicadas en parques industriales autorizados en los términos de la presente sección.
                En los casos anteriores, la Secretaría, una vez analizado el informe preventivo, determinará, en un plazo no mayor de veinte días, si se requiere la presentación de una manifestación de impacto ambiental en alguna de las modalidades previstas en el reglamento de la presente Ley, o si se está en alguno de los supuestos señalados. La Secretaría publicará en su Gaceta Ecológica, el listado de los informes preventivos que le sean presentados en los términos de este artículo, los cuales estarán a disposición del público.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //12
            'legal_basis' => 'Art.30 LGEEPA',
            'id_guideline' => 2,
            'legal_quote' => 'Para obtener la autorización a que se refiere el artículo 28 de esta Ley, los interesados deberán presentar a la Secretaría una manifestación de impacto ambiental, la cual deberá contener, por lo menos, una descripción de los posibles efectos en el o los ecosistemas que pudieran ser afectados por la obra o actividad de que se trate, considerando el conjunto de los elementos que conforman dichos ecosistemas, así como las medidas preventivas, de mitigación y las demás necesarias para evitar y reducir al mínimo los efectos negativos sobre el ambiente.
            Cuando se trate de actividades consideradas altamente riesgosas en los términos de la presente Ley, la manifestación deberá incluir el estudio de riesgo correspondiente.
            Si después de la presentación de una manifestación de impacto ambiental se realizan modificaciones al proyecto de la obra o actividad respectiva, los interesados deberán hacerlas del conocimiento de la Secretaría, a fin de que ésta, en un plazo no mayor de 10 días les notifique si es necesaria la presentación de información adicional para evaluar los efectos al ambiente, que pudiesen ocasionar tales modificaciones, en términos de lo dispuesto en esta Ley.
            Los contenidos del informe preventivo, así como las características y las modalidades de las manifestaciones de impacto ambiental y los estudios de riesgo serán establecidos por el Reglamento de la presente Ley.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //13
            'legal_basis' => 'Art. 17 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'El promovente deberá presentar a la Secretaría la solicitud de autorización en materia de impacto ambiental, anexando:
            I. La manifestación de impacto ambiental;
            II. Un resumen del contenido de la manifestación de impacto ambiental, presentado en disquete, y
            III. Una copia sellada de la constancia del pago de derechos correspondientes.
            Cuando se trate de actividades altamente riesgosas en los términos de la Ley, deberá incluirse un
            estudio de riesgo.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //14
            'legal_basis' => '51 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'La Secretaría podrá exigir el otorgamiento de seguros o garantías respecto del cumplimiento de las condiciones establecidas en las autorizaciones, cuando durante la realización de las obras puedan producirse daños graves a los ecosistemas.
            Se considerará que pueden producirse daños graves a los ecosistemas, cuando:
            I. Puedan liberarse sustancias que al contacto con el ambiente se transformen en tóxicas, persistentes y bioacumulables;
            II. En los lugares en los que se pretenda realizar la obra o actividad existan cuerpos de agua, especies de flora y fauna silvestre o especies endémicas, amenazadas, en peligro de extinción o sujetas a protección especial;
            III. Los proyectos impliquen la realización de actividades consideradas altamente riesgosas conforme a la Ley, el reglamento respectivo y demás disposiciones aplicables, y
            IV. Las obras o actividades se lleven a cabo en Áreas Naturales Protegidas.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //15
            'legal_basis' => '53 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'El promovente deberá, en su caso, renovar o actualizar anualmente los montos de los seguros o garantías que haya otorgado.
            La Secretaría, dentro de un plazo de diez días, ordenará la cancelación de los seguros o garantías cuando el promovente acredite que ha cumplido con todas las condiciones que les dieron origen y haga la solicitud correspondiente.',
            'id_application_type' => 1,
        ]);

        \DB::table('t_legal_basises')->insert([ //16
            'legal_basis' => '58 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Para los efectos del presente capítulo, las medidas correctivas o de urgente aplicación tendrán por objeto evitar que se sigan ocasionando afectaciones al ambiente, los ecosistemas o sus elementos; restablecer las condiciones de los recursos naturales que hubieren resultado afectados por
            obras o actividades; así como generar un efecto positivo alternativo y equivalente a los efectos adversos en el ambiente, los ecosistemas y sus elementos que se hubieren identificado en los procedimientos de inspección. En la determinación de las medidas señaladas, la autoridad deberá considerar el orden de prelación a que se refiere este precepto.
            El interesado, dentro del plazo de cinco días contados a partir de la notificación de la resolución mediante la cual se impongan medidas correctivas, podrá presentar ante la autoridad competente una propuesta para la realización de medidas alternativas a las ordenadas por aquélla, siempre que dicha propuesta se justifique debidamente y busque cumplir con los mismos propósitos de las medidas ordenadas por la Secretaría. En caso de que la autoridad no emita una resolución respecto a la propuesta antes referida dentro del plazo de diez días siguientes a su recepción, se entenderá contestada en sentido afirmativo.
            Los plazos ordenados para la realización de las medidas correctivas referidas en el párrafo que antecede, se suspenderán en tanto la autoridad resuelva sobre la procedencia o no de las medidas alternativas propuestas respecto de ellas. Dicha suspensión procederá cuando lo solicite expresamente el promovente, y no se ocasionen daños y perjuicio a terceros, a menos que se garanticen éstos para el caso de no obtener resolución favorable.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //17
            'legal_basis' => '60 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Cuando la autoridad emplace al presunto infractor en términos del artículo 167 de la Ley, y éste comparezca mediante escrito aceptando las irregularidades circunstanciadas en el acta de inspección, la Secretaría procederá, dentro de los veinte días siguientes, a dictar la resolución respectiva.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //18
            'legal_basis' => '61 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Si como resultado de una visita de inspección se ordena la imposición de medidas de seguridad, correctivas o de urgente aplicación, el inspeccionado deberá notificar a la autoridad del cumplimiento de cada una, en un plazo máximo de cinco días contados a partir de la fecha de vencimiento del plazo concedido por aquélla para su realización.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //19
            'legal_basis' => '168 LGEEPA',
            'id_guideline' => 1,
            'legal_quote' => 'Una vez recibidos los alegatos o transcurrido el término para presentarlos, la Secretaría procederá, dentro de los veinte días siguientes, a dictar por escrito la resolución respectiva, misma que se notificará al interesado, personalmente o por correo certificado con acuse de recibo.
            Durante el procedimiento, y antes de que se dicte resolución administrativa, el interesado y la Procuraduría Federal de Protección al Ambiente, a petición del primero, podrán convenir la realización de acciones para la reparación y compensación de los daños que se hayan ocasionado al ambiente.
            En los convenios administrativos referidos en el párrafo anterior, podrán intervenir quienes sean parte en el procedimiento judicial previsto en la Ley Federal de Responsabilidad Ambiental, siempre que se trate de la misma infracción, hechos y daños.
            En la formulación y ejecución de los convenios se observará lo dispuesto por el artículo 169 de esta Ley, así como lo previsto por la Ley Federal de Responsabilidad Ambiental, en ellos podrá también acordarse la realización del examen metodológico de las operaciones del interesado a las que hace referencia el artículo 38 Bis, así como la atenuación y conmutación de las multas que resulten procedentes. En todo caso, deberá garantizarse el cumplimiento de las obligaciones del infractor, en cualquiera de las formas previstas en el Código Fiscal de la Federación.
            La celebración del convenio suspenderá el procedimiento administrativo y el término para la caducidad, a partir de la presentación de la solicitud a la autoridad, y hasta por un plazo de cuarenta y cinco días hábiles.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //20
            'legal_basis' => '67 LFPA',
            'id_guideline' => 3,
            'legal_quote' => 'NADA',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //21
            'legal_basis' => 'Art. 45 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'Una vez concluida la evaluación de la manifestación de impacto ambiental, la Secretaría deberá emitir, fundada y motivada, la resolución correspondiente en la que podrá:
                I. Autorizar la realización de la obra o actividad en los términos y condiciones manifestados;
                II. Autorizar total o parcialmente la realización de la obra o actividad de manera condicionada.
                En este caso la Secretaría podrá sujetar la realización de la obra o actividad a la modificación del proyecto o al establecimiento de medidas adicionales de prevención y mitigación que tengan por objeto evitar, atenuar o compensar los impactos ambientales adversos susceptibles de ser producidos en la construcción, operación normal, etapa de abandono, término de vida útil del proyecto, o en caso de accidente, o
                III. Negar la autorización en los términos de la fracción III del Artículo 35 de la Ley.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //22
            'legal_basis' => 'Art. 47 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'La ejecución de la obra o la realización de la actividad de que se trate deberá sujetarse a lo previsto en la resolución respectiva, en las normas oficiales mexicanas que al efecto se expidan y en  las demás disposiciones legales y reglamentarias aplicables.
            En todo caso, el promovente podrá solicitar que se integren a la resolución los demás permisos, licencias y autorizaciones que sean necesarios para llevar a cabo la obra o actividad proyectada y cuyo otorgamiento corresponda a la Secretaría.',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //23
            'legal_basis' => 'Art. 48 RLGEEPAMEIA',
            'id_guideline' => 1,
            'legal_quote' => 'En los casos de autorizaciones condicionadas, la Secretaría señalará las condiciones y requerimientos que deban observarse tanto en la etapa previa al inicio de la obra o actividad, como en sus etapas de construcción, operación y abandono',
            'id_application_type' => 1,
        ]);
        \DB::table('t_legal_basises')->insert([ //24
            'legal_basis' => 'Artículo 194-H',
            'id_guideline' => 4,
            'legal_quote' => 'Por los servicios que a continuación se señalan, se pagará el derecho de impacto ambiental de obras o actividades cuya evaluación corresponda al Gobierno Federal, conforme a las siguientes cuotas: ',
            'id_application_type' => 1,
        ]);
    }
}