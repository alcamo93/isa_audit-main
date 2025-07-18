<table>
    <thead>
        <tr>
            <th></th>
        </tr>
        <tr >
            <th colspan="1"></th>
            <th colspan="2"></th>
            <th colspan="15">{{ $title }}</th>
            <th colspan="1"></th>
        </tr>
        <tr>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="1"></th>
            <th colspan="2">Cliente</th>
            <th colspan="4">{{ $data['headers']['corp_tradename'] }}</th>
            <th colspan="2">Calle</th>
            <th colspan="4">{{ $data['headers']['street'] }}</th>
            <th colspan="2">Fecha</th>
            <th colspan="2">{{ $data['headers']['date'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2">Razón social</th>
            <th colspan="4">{{ $data['headers']['corp_trademark'] }}</th>
            <th colspan="2">Colonia</th>
            <th colspan="4">{{ $data['headers']['suburb'] }}</th>
            <th colspan="2">Estatus</th>
            <th colspan="2">{{ $data['headers']['status'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2">RFC</th>
            <th colspan="4">{{ $data['headers']['rfc'] }}</th>
            <th colspan="2">Ciudad</th>
            <th colspan="4">{{ $data['headers']['city'] }}</th>
            <th colspan="2">Alcance</th>
            <th colspan="2">{{ $data['headers']['scope'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2">Responsable</th>
            <th colspan="4">{{ $data['headers']['users'] }}</th>
            <th colspan="2">Estado</th>
            <th colspan="4">{{ $data['headers']['state'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2">Giro</th>
            <th colspan="4">{{ $data['headers']['industry'] }}</th>
            <th colspan="2">País</th>
            <th colspan="4">{{ $data['headers']['country'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2">Aspectos evaluados</th>
            <th colspan="14">{{ $data['headers']['aspects_evaluated'] }}</th>
        </tr>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="16">
            A continuación, se enlistan las preguntas mostradas en la aplicabilidad así como sus respectivas respuestas:
            </th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="4">Materia</th>
            <th colspan="4">Aspecto</th>
            <th colspan="4">Pregunta</th>
            <th colspan="4">Respuesta</th>
            <th colspan="4">Comentarios</th>
        </tr>
        @foreach($data['questions'] as $row)
            <tr>
                <th colspan="1"></th>
                <th colspan="4">{{ $row['matter']  }}</th>
                <th colspan="4">{{ $row['aspect']  }}</th>
                <th colspan="4">{{ $row['question']  }}</th>
                <th colspan="4">
                    <ul>
                    @foreach( $row['answers'] as $answer )
                        <li>{{ $answer }}</li>
                    @endforeach
                    </ul>
                </th>
                <th colspan="4">{{ $row['comments']  }}</th>
            </tr>
        @endforeach
    </tbody>
</table>
