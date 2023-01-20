<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atendimentos</title>
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
            font-weight:bold
        }
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
            Relatório de vendas desde: {{date('d/m/Y H:i:s', strtotime($date))}} <br>
            Quantidade de vendas: {{count($sales)}} 
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Código</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{$sale->name}}</td>
                    <td>{{$sale->phone}}</td>
                    <td>{{$sale->code}}</td>
                    <td>{{$sale->value}}</td>
                </tr>
            @endforeach
            
        </tbody>
    </table>
</body>
</html>
