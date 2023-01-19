<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atendimentos</title>
    <style>
        table {
            border:1px solid #aaa;
            border-collapse: collapse;
            width:100%;
            
        }
        table thead {
            background-color: #016b00;
            color:#ffd51e;
            font-weight: 700;
        }
        table tbody tr:hover{
            background-color: #ddd;
        }

        table tr {
            border-bottom: 1px solid #aaa;
        }
        table th,
        table td {
            padding:12px 8px;
            border-right: 1px solid #aaa;
            text-align: center;
            font-weight: 600;
        }
        table th:last-child,
        table td:last-child{
            border-right: none;
        }
        table th {
            font-weight: 700;
        }
        .attendances-number{
            font-size:18px;
            font-weight:700;
            margin-bottom:30px
        }
    </style>
</head>
<body>
    <div class="info">
        <div class="attendances-number">
            Relatório de atendimentos desde: {{date('d/m/Y H:i:s', strtotime($date))}} <br>
            Quantidade de atendimentos: {{count($attendances)}} 
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Atendente</th>
                <th>Cliente</th>
                <th>Cel. cliente</th>
                <th>Código</th>
                <th>Tipo</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr >
                <td>{{$attendance->attendant_name}}</td>
                <td>{{$attendance->client_name}}</td>
                <td>{{$attendance->client_phone}}</td>
                <td>{{$attendance->user_card_code}}</td>
                <td>{{$attendance->type === 'payment' ? 'Pagemento' : ''}}{{$attendance->type === 'doubt' ? 'Dúvida' : ''}}</td>
                <td>{{$attendance->date}}</td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
</body>
</html>