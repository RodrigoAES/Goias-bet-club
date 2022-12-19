<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
  * {
    font-family: 'Montserrat', sans-serif;
  }
    img {
        width:200px;
        margin:0px 38%
    }
    .match {
        width:100%;
        text-align:center;
	margin-bottom: 30px;
 	page-break-inside: avoid; 
    }
    .match .info {
        border:2px solid #000;
        border-radius:30px;
        text-align:center;
        display:table;
        margin:auto;
	height:20px;
    }
    .team {
        display:inline-block;
        vertical-align:middle;
        text-align:center;
        line-height:16px;
        height:10px;
        display:table-cell;
        padding:0px;
        padding-top:10px;

    }
    .team-name {
        display:inline-block;
        vertical-align:middle;
        line-height:16px;
        padding:5px;
        font-size:16px;
        font-weight:bold;
        width:120px;
    }
    .team.home .team-name {
        text-align:right;
    }
    .team.away .team-name {
        text-align:left;
    }
    .flag {
        display:inline-block;
        vertical-align:middle;
        text-align:center;
        line-height:16px;
        padding:10px;
    }
    .flag img {
        max-width:30px;
        max-height:30px;
        display:inline-block;
        vertical-align:middle;
    }
    .score {
        display:inline-block;
        vertical-align:middle;
        text-align:center;
        line-height:16px;
        padding:0px 10px;
        font-size:20px;
        font-weight:bold;
    }
    .x {
        display:inline-block;
        vertical-align:middle;
        text-align:center;
        line-height:16px;
        height:10px;
        display:table-cell;
        padding:0px;
    }
    .x span {
        display:inline-block;
        vertical-align:middle;
        font-size:20px;
        font-weight:600;
    }
    .bet-buttons {
        width:100%;
        text-align:center;
    }
    .bet-buttons button {
        padding:5px 10px;
        border:none;
        border-radius:8px;
        font-size: 16px;
        font-weight:bold;
    }
    .bet-buttons button.active {
        background-color:#4baf50 ;
        color:#f1de39;
    }
    .user-bet {
        text-align:center;
    }
    .user-bet span{
        display:block
    }
    .user-bet span:last-child {
        display:table;
        margin:auto;
    }
    .user-bet span:last-child div{
        height:35px;
        display:table-cell;
        vertical-align:middle;
        text-align:center;
        
    }
    .user-bet span:last-child div span {
        display:inline-block;
        vertical-align: middle;
        margin:5px;
        font-size: 20px;
        font-weight: bold;
        margin-top:15px
    }
    .user-bet span:last-child div img{
        max-height:25px;
        max-width:25px;
        margin:6px;
    }
    .date-group-info {
        margin-top: 10px;
        width:100%;
        text-align:center;
        display:table;
    }
    .group, 
    .finished,
    .datetime {
        display:inline-block;
        vertical-align:middle;
        text-align:center;
        margin-right:15px;
        font-weight:bold;
        line-height: 10px;
    }
    .group span, 
    .finished span,
    .datetime span{
        display:inline-block;
        vertical-align:middle;
        text-align:center;
        font-weight: normal;
    }
    .subtitle {
        font-size: 30px;
        font-weight: bold;
        width:100%;
        text-align: center;
        margin-top: 40px;
        margin-bottom: 30px;
    }

    hr {
        margin:10px 0px;
    }
    .signature {
        margin-top: 60px;
        width:90%;
    }
    .signature div {
        margin-bottom: 40px;
    }
    .signature span {
        font-weight:bold;
        width:400px;
        padding-top:40px;
        border:1px solid #000;
    }
    .signature a {
	word-wrap:break-word;
	word-break:break-all;
	font-size:10px;
    padding:10px;
    text-decoration:underline;
    color: #0000ff;
    }
    
  </style>
</head>
<body> 
    <img src="https://www.bolaotrevodasorte.com/bolaodefutebol/core/public/logo" />
    <h1>Comprovante de pagamento</h1>
    <div class='info'>
      <span><strong>Comprador: </strong>{{$user_card->name}}</span><br>
      <span><strong>Código: </strong> {{$user_card->code}}</span><br>
      <span><strong>Telefone:</strong> {{$user_card->phone}}</span><br>
      <span><strong>Validada</strong> {{$user_card->validated ? 'SIM' : 'NÃO'}}</span><br>
      <span><strong>Nome do bolão:</strong> {{$user_card->card->name}}</span><br>
      <span><strong>Data:</strong> {{$date}}</span>
    </div><br/>

    <hr />

    <div class="subtitle">Cartela</div>

    @foreach ($user_card->bets as $index => $bet)
    <div class="match bet">
        <div class="info">
            <div class="team home">
                <div class="team-name">{{$bet->match->home_name}}</div>
                <div class="flag">
                    @if($user_card->card->championship === 'world-cup')
                        <img src="{{$bet->match->home_flag}}" />

                    @elseif ($user_card->card->championship === 'brasileirao')
                        <img 
                            src="{{url('api/brasileirao/flag/'.$bet->match->home_flag)}}" 
                            class="brasileirao"
                        />
                    @else
                        <img 
                            src="{{$bet->match->home_flag}}"
                            class="custom" 
                        />
                    @endif
                </div>
               
                <div class="score home">{{$bet->match->home_score}}</div>
            </div>
            
            <div class="x"><span>X</span></div>

            <div class="team away">
                <div class="score away">{{$bet->match->away_score}}</div>
                <div class="flag">
                   @if($user_card->card->championship === 'world-cup')
                        <img src="{{$bet->match->away_flag}}" />

                    @elseif ($user_card->card->championship === 'brasileirao')
                        <img 
                            src="{{url('api/brasileirao/flag/'.$bet->match->away_flag)}}" 
                            class="brasileirao"
                        />
                    @else
                        <img 
                            src="{{$bet->match->away_flag}}"
                            class="custom" 
                        />
                    @endif
                </div>
                <div class="team-name">{{$bet->match->away_name}}</div>
            </div>
        </div><br/>
        @if ($user_card->card->type === 'common')
            <div class="bet-buttons">
                <button
                    class="{{$bet->bet === 'victory' ? 'active' : '' }}"
                >Casa</button>

                <button
                    class="{{$bet->bet === 'draw' ? 'active' : '' }}"
                >Empate</button>

                <button
                    class="{{$bet->bet === 'loss' ? 'active' : '' }}"
                >Visitante</button>
            </div>
        @endif
        
        @if($user_card->card->type === 'detailed')
            <div class="user-bet">
                <span>Apostado:</span>

                <span>
                    <div>
                        <img src="https://www.bolaotrevodasorte.com/bolaodefutebol/core/public/team/flag/Yu0CUPOmn2SDZGrRggjWxyqbCkpdWdHy5vS8IKvF.jpg" />
                    </div>
                   
                    <div><span>{{$bet->home_score}}</span><span>X</span><span>{{$bet->away_score}}</span></div>
                    
                    <div>
                        <img src="https://www.bolaotrevodasorte.com/bolaodefutebol/core/public/team/flag/pZfrz0lOl9LSsgeSsqRMEorCjyjXLMcfYkEdx3gm.png" />
                    </div>
                </span>
            </div>
        @endif
        <div class="date-group-info">
            <div class="group">Grupo <span>{{$bet->match->group}}</span></div>
            <div class="finished">Terminada: <span>{{$bet->match->finished === false ? 'NÃƒÆ’Ã¢â‚¬Å¡O' : 'SIM'}}</span></div>
            <div class="datetime">Data: <span>{{$bet->match->datetime}}</span></div>
        </div>  
    </div>
  @endforeach

  <div class="signature">
    <div>
        <span>Segunda via do comprovante:</span><br/><a>{{$signature}}</a><br>
    </div>

    <div>
        <span>Consulta da cartela:</span><br><a>https://www.bolaotrevodasorte.com/bolaodefutebol/user-card/{{$user_card->code}}</a><br>
    </div>

    <div>
        <span>Ranking de resultados:</span><br><a>https://www.bolaotrevodasorte.com/bolaodefutebol/ranking</a>
    </div>
        
  </div>

</body>
</html>