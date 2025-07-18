<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RiskHelpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 1,
            "risk_help" => "Practicamente Imposible 1",
            "standard" => "Prácticamente imposible, No se a producido nunca pero es posible",
            "value" => 0.1,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 2,
            "risk_help" => "Casi imposible 1",
            "standard" => "Es muy improbable, casi imposible. Aún así, es concebible.",
            "value" => 0.5,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 3,
            "risk_help" => "Poco posible",
            "standard" => "El evento sería producto de la mala suerte, pero es posible ",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 4,
            "risk_help" => "Posible",
            "standard" => "Aunque no es muy probable, ha ocurrido o podría pasar",
            "value" => 3.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 5,
            "risk_help" => "Muy posible",
            "standard" => "El accidente es factible. 50% de probabilidad.",
            "value" => 6.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 6,
            "risk_help" => "Casi seguro",
            "standard" => "El evento es el resultado más probable al hacer la actividad",
            "value" => 10.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 7,
            "risk_help" => "Remotamente 1",
            "standard" => "Remotamente posible, no sabe si ya ha ocurrido, pero no se descarta la situacion",
            "value" => 0.5,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 8,
            "risk_help" => "Raramente 1",
            "standard" => "Rara vez posible, se sabe que ocurre, pero no con frecuencia",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 9,
            "risk_help" => "Irregularmente",
            "standard" => "Una vez al año o al mes",
            "value" => 2.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 10,
            "risk_help" => "Ocacionalmente",
            "standard" => "Una vez por semana o al mes",
            "value" => 3.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 11,
            "risk_help" => "Frecuentemente",
            "standard" => "Frecuentemente con periodicidad diaria de al menos una vez ",
            "value" => 5.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 12,
            "risk_help" => "Continuamente",
            "standard" => "De forma continua a lo largo del día (muchas veces)",
            "value" => 10.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 13,
            "risk_help" => "No hay fuga o derrame externo. Fugas o derrames internos de sustancias químicas controlables. Sin necesidad de remediación 1",
            "standard" => "
                •	La instalación no maneja sustancias químicas de igual o mayor cantidad al Apéndice A de la NOM-028-STPS.
                •	La instalación no maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT. (Nota: no se contempla cantidad en este punto). 
                •	La instalación no maneja, almacena, transporta sustancias o materiales clasificados como peligrosas por el país y acuerdos internacionales. 
                •	La instalación no se encuentra ubicada en Áreas Naturales Protegidas o de conservación o forestales, así como Categorías de protección de especies, Patrimonio histórico, zonas acuáticas, especies protegidas de fauna y flora, etc. 
                •	No se requieren acciones de remedición, el posible daño es recuperable en menos de 1 año.
            ",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 14,
            "risk_help" => "Fuga o derrame externo que se pueda controlar en menos de una hora (incluyendo el tiempo para detectar). Fugas o derrames de sustancias químicas de forma intern ▶",
            "standard" => "
                •	La instalación no maneja sustancias químicas de igual o mayor cantidad al Apéndice A de la NOM-028-STPS.
                •	La instalación no maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT. (Nota: no se contempla cantidad en este punto). 
                •	La instalación no maneja, almacena, transporta sustancias o materiales clasificados como peligrosas por el país y acuerdos internacionales. 
                •	La instalación no se encuentra ubicada en Áreas Naturales Protegidas o de conservación o forestales, así como Categorías de protección de especies, Patrimonio histórico, zonas acuáticas, especies protegidas de fauna y flora, etc. 
                •	Se requieren acciones de contención interna. La recuperación ambiental puede tardar menos de 1 año. 
                •	Extensión de fuga o derrame de menos de 1 km
            ",
            "value" => 5.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 15,
            "risk_help" => "Fuga o derrame externo que se pueda controlar en algunas horas. Existe posibilidad de remediación. ",
            "standard" => "
                •	La instalación no maneja sustancias químicas de igual o mayor cantidad al Apéndice A de la NOM-028-STPS, la instalación 
                •	La instalación maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT en cantidades menores y con capacidad de almacenamiento de un 10% de la cantidad reportada en los listados, o bien, almacena, transporta sustancias o materiales clasificados como peligrosas por el país y acuerdos internacionales. 
                •	La instalación no se encuentra ubicada en Áreas Naturales Protegidas o de conservación o forestales, así como Categorías de protección de especies, Patrimonio histórico…zonas acuáticas, especies protegidas de fauna y flora, etc. 
                •	Se requieren acciones de contención interna, activación de plan de emergencias. 
                •	El daño posiblemente requerirá de remediación, con una recuperabilidad de menos de 5 años. 
                •	Extensión de fuga o derrame entre 1 km y 5 km
            ",
            "value" => 15.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 16,
            "risk_help" => "Fuga o derrame externo que se pueda controlar en un día. La recuperación del factor ambiental afectado es reversible en 5 años",
            "standard" => "
                •	La instalación no maneja sustancias químicas de igual o mayor cantidad al Apéndice A de la NOM-028-STPS, la instalación 
                •	La instalación maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT en cantidades iguales o mayores, o bien cuenta con sustancias asfixiantes o catalogados como material peligroso en cantidades considerables. 
                •	La instalación no se encuentra ubicada en Áreas Naturales Protegidas o de conservación o forestales, así como Categorías de protección de especies, Patrimonio histórico, zonas acuáticas, especies protegidas de fauna y flora, etc. 
                •	Se requieren acciones de contención interna, activación de plan de emergencias. 
                •	Aplica remediación y la recuperación es en 5 años 
                •	Extensión de fuga o derrame mayor a 5 km
            ",
            "value" => 25.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 17,
            "risk_help" => "Fuga o derrame externo que se pueda controlar en una semana.",
            "standard" => "
                •	La instalación maneja sustancias químicas de igual o mayor cantidad al Apéndice A de la NOM-028-STPS, la instalación 
                •	La instalación maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT en cantidades iguales o mayores, o bien cuenta con sustancias asfixiantes o catalogados como material peligroso en cantidades considerables. 
                •	La instalación no se encuentra ubicada en Áreas Naturales Protegidas o de conservación o forestales, así como Categorías de protección de especies, Patrimonio histórico, zonas acuáticas, especies protegidas de fauna y flora, etc. 
                •	Se requieren acciones de contención interna, activación de plan de emergencias. 
                •	Aplica remediación y la recuperación es en menos de 10 años
                •	Extensión de fuga o derrame mayor a 5 km
            ",
            "value" => 50.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 18,
            "risk_help" => "Fuga o derrame externo que no se pueda controlar en una semana.",
            "standard" => "
                •	La instalación maneja sustancias químicas de igual o mayor cantidad al Apéndice A de la NOM-028-STPS, la instalación 
                •	La instalación maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT en cantidades iguales o mayores, o bien cuenta con sustancias asfixiantes o catalogados como material peligroso en cantidades considerables. 
                •	La instalación se encuentra ubicada en Áreas Naturales Protegidas o de conservación o forestales, así como Categorías de protección de especies, Patrimonio histórico, zonas acuáticas, especies protegidas de fauna y flora, etc. 
                •	Se requieren acciones de contención interna, activación de plan de emergencias. 
                •	Extensión de fuga o derrame mayor a 5 km
                •	La recuperación del sistema ambiental tomará más de 10 años.
            ",
            "value" => 100.0,
            "id_status" => 1,
            "id_risk_category" => 1,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 19,
            "risk_help" => "Practicamente Imposible 2",
            "standard" => "Prácticamente imposible, No se a producido nunca pero es posible",
            "value" => 0.1,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 20,
            "risk_help" => "Casi imposible 2",
            "standard" => "Es muy improbable, casi imposible. Aún así, es concebible.",
            "value" => 0.5,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 21,
            "risk_help" => "Poco posible",
            "standard" => "El evento sería producto de la mala suerte, pero es posible ",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 22,
            "risk_help" => "Posible",
            "standard" => "Aunque no es muy probable, ha ocurrido o podría pasar",
            "value" => 3.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 23,
            "risk_help" => "Muy posible",
            "standard" => "El accidente es factible. 50% de probabilidad.",
            "value" => 6.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 24,
            "risk_help" => "Casi seguro",
            "standard" => "El evento es el resultado más probable al hacer la actividad",
            "value" => 10.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 25,
            "risk_help" => "Remotamente 2",
            "standard" => "Remotamente posible, no sabe si ya ha ocurrido, pero no se descarta la situacion",
            "value" => 0.5,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 26,
            "risk_help" => "Raramente 2",
            "standard" => "Rara vez posible, se sabe que ocurre, pero no con frecuencia",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 27,
            "risk_help" => "Irregularmente",
            "standard" => "Una vez al año o al mes",
            "value" => 2.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 28,
            "risk_help" => "Ocacionalmente",
            "standard" => "Una vez por semana o al mes",
            "value" => 3.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 29,
            "risk_help" => "Frecuentemente",
            "standard" => "Frecuentemente con periodicidad diaria de al menos una vez ",
            "value" => 5.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 30,
            "risk_help" => "Continuamente",
            "standard" => "De forma continua a lo largo del día (muchas veces)",
            "value" => 10.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 31,
            "risk_help" => "Riesgo empresarial 1",
            "standard" => "Riesgo empresarial 1",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 32,
            "risk_help" => "Riesgo empresarial 2",
            "standard" => "Riesgo empresarial 2",
            "value" => 5.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 33,
            "risk_help" => "Riesgo empresarial 3",
            "standard" => "Riesgo empresarial 3",
            "value" => 15.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 34,
            "risk_help" => "Riesgo empresarial 4",
            "standard" => "Riesgo empresarial 4",
            "value" => 25.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 35,
            "risk_help" => "Riesgo empresarial 5",
            "standard" => "Riesgo empresarial 5",
            "value" => 50.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 36,
            "risk_help" => "Riesgo empresarial 6",
            "standard" => "Riesgo empresarial 6",
            "value" => 100.0,
            "id_status" => 1,
            "id_risk_category" => 2,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 37,
            "risk_help" => "Practicamente Imposible 3",
            "standard" => "Prácticamente imposible, No se a producido nunca pero es posible",
            "value" => 0.1,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 38,
            "risk_help" => "Casi imposible 3",
            "standard" => "Es muy improbable, casi imposible. Aún así, es concebible.",
            "value" => 0.5,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 39,
            "risk_help" => "Poco posible",
            "standard" => "El evento sería producto de la mala suerte, pero es posible ",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 40,
            "risk_help" => "Posible",
            "standard" => "Aunque no es muy probable, ha ocurrido o podría pasar",
            "value" => 3.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 41,
            "risk_help" => "Muy posible",
            "standard" => "El accidente es factible. 50% de probabilidad.",
            "value" => 6.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 42,
            "risk_help" => "Casi seguro",
            "standard" => "El evento es el resultado más probable al hacer la actividad",
            "value" => 10.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 1
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 43,
            "risk_help" => "Remotamente 3",
            "standard" => "Remotamente posible, no sabe si ya ha ocurrido, pero no se descarta la situacion",
            "value" => 0.5,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 44,
            "risk_help" => "Raramente 3",
            "standard" => "Rara vez posible, se sabe que ocurre, pero no con frecuencia",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 45,
            "risk_help" => "Irregularmente",
            "standard" => "Una vez al año o al mes",
            "value" => 2.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 46,
            "risk_help" => "Ocacionalmente",
            "standard" => "Una vez por semana o al mes",
            "value" => 3.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 47,
            "risk_help" => "Frecuentemente",
            "standard" => "Frecuentemente con periodicidad diaria de al menos una vez ",
            "value" => 5.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 48,
            "risk_help" => "Continuamente",
            "standard" => "De forma continua a lo largo del día (muchas veces)",
            "value" => 10.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 2
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 49,
            "risk_help" => "Riesgo salud 1",
            "standard" => "Riesgo salud 1",
            "value" => 1.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 50,
            "risk_help" => "Riesgo salud 2",
            "standard" => "Riesgo salud 2",
            "value" => 5.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 51,
            "risk_help" => "Riesgo salud 3",
            "standard" => "Riesgo salud 3",
            "value" => 15.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 52,
            "risk_help" => "Riesgo salud 4",
            "standard" => "Riesgo salud 4",
            "value" => 25.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 53,
            "risk_help" => "Riesgo salud 5",
            "standard" => "Riesgo salud 5",
            "value" => 50.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 3
        ]);
        \DB::table('t_risk_help')->insert([
            "id_risk_help" => 54,
            "risk_help" => "Riesgo salud 6",
            "standard" => "Riesgo salud 6",
            "value" => 100.0,
            "id_status" => 1,
            "id_risk_category" => 3,
            "id_risk_attribute" => 3
        ]);
    }
}