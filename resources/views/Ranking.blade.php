<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
            font-weight:bold
        }
        .ranking-users {
            margin-top:30px;
        }
        .title {
            font-size: 22px;
            font-weight:bold;
            margin-bottom:10px
        }
        table{
            border:1px solid #888;
            border-collapse:collapse;
            width:100%;
        }
        table thead {
            background-color:#00763c;
            color:#f9db03;
        }
        table tr {
            border-bottom:1px solid #888;
        }
        table th,
        table td {
            padding:10px 20px;
            text-align: center;
        }

        .info {
            margin-bottom: 30px;
        }

        .info .card-name {
            font-size: 22px;
        }

        .info .link label{
            font-size: 22px;

        }
        .info .link .url {
            color:#0000ff;
        }
    </style>
</head>
<body>
    <div class='info'>
        <div class="card-name">Cartela: {{$card->name}}</div>
        <div class="link">
            <div class="label">Veja as apostas no site:</div>
            <div class="url">https://www.bolaotrevodasorte.com/bolaodefutebol/ranking/{{$card->id}}</div>
        </div>
    </div>

    <div class="winners">
        <div class="title">Vencedores</div>
        @if($winners && count($winners) > 0)
            <div class="users">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Código</th>
                            <th>pontos</th>
                            <th>Premio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($winners as $key => $card)
                            <tr>
                                <td class="ranking-name">{{$card->name}}</td>
                                <td class="ranking-code">{{$card->code}}</td>
                                <td class="ranking-points">{{$card->points}}</td>
                                <td class="ranking-award">R${{number_format($card->award, 2)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="ranking-users">
        <div class="title">Ranking</div>
        @if($ranked_user_cards && count($ranked_user_cards) > 0)
            <div class="users">
                <table>
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Nome</th>
                            <th>Código</th>
                            <th>Celular</th>
                            <th>pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ranked_user_cards as $key => $card)
                            <tr >
                                <td class="ranking-pos">{{$key+1}}º</td>
                                <td class="ranking-name">{{$card->name}}</td>
                                <td class="ranking-code">{{$card->code}}</td>
                                <td class="ranking-phone">{{$card->phone}}</td>
                                <td class="ranking-points">{{$card->points}}</td>
                            </tr>
                        @endforeach   
                    </tbody>
                </table>
            </div>
        @endif
</body>
</html>