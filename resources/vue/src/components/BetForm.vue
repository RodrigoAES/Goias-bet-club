<script>
    export default {
        props:['card'],
        data() {
            return {
                bets:[],
                cardId: this.card.id,
                name:'',
                nameError:null,
                phone:null,
                phoneError:null,
                betsError:null
            }
        },
        methods:{
            bet: function(matchId, bet) {
                let betExists = this.localizeBet(matchId);

                if(betExists.length > 0) {
                    this.bets[this.bets.indexOf(betExists[0])].bet = bet
                    
                } else {
                    this.bets.push({
                        match_id:matchId,
                        bet:bet
                    });
                }
            }, 
            betDetailed: function (matchId, team) {
                if(team === 'home') {
                    let input = document.querySelector(`input.home_score[data-key="${matchId}"]`);

                    if(input.value.length > 2) {
                        input.value = input.value.substring(0,2);
                    }

                    let betExists = this.localizeBet(matchId);
                    if(betExists.length > 0) {
                        this.bets[this.bets.indexOf(betExists[0])].bet.home_score = parseInt(input.value);

                    } else {
                        this.bets.push({
                            match_id:matchId,
                            bet: {
                                home_score: parseInt(input.value),
                                away_score:null,
                            }
                        });
                    }
                    console.log(this.bets)
                }
            
                if(team === 'away') {
                    let input = document.querySelector(`input.away_score[data-key="${matchId}"]`);

                    if(input.value.length > 2) {
                        input.value = input.value.substring(0,2);
                    }

                    let betExists = this.localizeBet(matchId);
                    if(betExists.length > 0) {
                        this.bets[this.bets.indexOf(betExists[0])].bet.away_score = parseInt(input.value);

                    } else {
                        this.bets.push({
                            match_id:matchId,
                            bet: {
                                home_score:null,
                                away_score:parseInt(input.value),
                            }
                        });
                    }
                    console.log(this.bets)

                }
                
            },
            localizeBet: function(id) {
                return this.bets.filter(bet => bet.match_id === id);
            },
            phoneMask: function(e) {
                if(! Number.isInteger(parseInt(e.data))) {
                    e.target.value = e.target.value.replace(e.data, '');
                }    
                if(e.target.value.length === 2 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `(${e.target.value.charAt(0)}${e.target.value.charAt(1)}) `;

                }else if(e.target.value.length === 6 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value} `;

                } else if(e.target.value.length === 11 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value}-`;

                } else if(e.target.value.length === 17 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value.substring(0,16)}`;
                }
            },
            sendBet: async function() {
                let body = new FormData();
                body.append('name', this.name);
                body.append('phone', this.phone);
                body.append('card_id', this.cardId);
                for(let i=0; i<this.bets.length; i++){
                    body.append('bets[]', JSON.stringify(this.bets[i]));
                }

                async function request() {
                    let request = await fetch(`${import.meta.env.VITE_BASE_URL}bet`, {
                        method:'POST',
                        body:body
                    });

                    let json = await request.json();

                    return json;
                }

                let response = await request();

                if(response.status === 'success') {
                    this.$parent.success({
                        id: response.user_card.id,
                        name: response.user_card.name,
                        phone: response.user_card.phone,
                        code: response.user_card.code,
                        created_at: response.user_card.created_at,
                    });

                } else if(response.status === 'error') {
                    this.nameError = response.error.name ? response.error.name : null;
                    this.phoneError = response.error.phone ? response.error.phone : null;
                    this.betsError = response.error.bets ? response.error.bets : null;
                }
            }
        },
    }
</script>

<template>
    <div class="modal-area-bet">
        <div id="bet-form">
            <div class="card">
                <div class="name"><span>Nome: {{card.name}}</span></div>
                <div class="home-away"><span>Casa</span><span>Fora</span></div>
                <input type="hidden" :value="card.id" />

                <div v-for="(match, index) in card.matchs" class="match bet">
                    <div class="info">
                        <div class="team home">
                            <div class="team-name">{{match.home_name}}</div>
                            <div class="flag">
                                <img 
                                    v-if="card.championship === 'world-cup'" 
                                    :src="match.home_flag" 
                                    :alt="`${match.home_name} flag`" 
                                />

                                <img 
                                    v-else-if="card.championship === 'brasileirao'" 
                                    :src="`http://localhost:8000/api/brasileirao/flag/${match.home_flag}`" 
                                    :alt="match.home_name" 
                                    class="brasileirao"
                                />
    
                                <img 
                                    v-else
                                    :src="match.home_flag"
                                    :alt="match.home_name"
                                    class="custom" 
                                />
                            </div>
                            <div class="score home">
                                <span v-if="card.type === 'common'">{{match.home_score}}</span>

                                <input 
                                    v-if="card.type === 'detailed'"
                                    @input="betDetailed(match.id, 'home')"
                                    type="number" 
                                    :data-key="match.id"
                                    class="home_score"
                                />
                            </div>
                        </div>
                        
                        <div class="x">X</div>
        
                        <div class="team away">
                            <div class="score away">
                                <span v-if="card.type === 'common'">{{match.away_score}}</span>

                                <input 
                                    v-if="card.type === 'detailed'" 
                                    @input="betDetailed(match.id, 'away')"
                                    type="number" 
                                    :data-key="match.id"
                                    class="away_score"
                                />
                            </div>
                            <div class="flag">
                                <img 
                                    v-if="card.championship === 'world-cup'" 
                                    :src="match.away_flag" 
                                    :alt="`${match.away_name} flag`" 
                                />

                                <img 
                                    v-else-if="card.championship === 'brasileirao'" 
                                    :src="`http://localhost:8000/api/brasileirao/flag/${match.away_flag}`" 
                                    :alt="match.home_name" 
                                    class="brasileirao"
                                />

                                <img 
                                    v-else
                                    :src="match.away_flag"
                                    :alt="match.away_name"
                                    class="custom" 
                                />
                            </div>
                            <div class="team-name">{{match.away_name}}</div>
                        </div>
                    </div>
                    <div v-if="card.type === 'common'" class="bet-buttons">
                        <button
                            @click="bet(match.id, 'victory')"
                            :class="bets.filter(bet => {return bet.match_id == match.id && bet.bet == 'victory' ? true : false}).length > -0 ? 'active' : null"
                        >Casa</button>

                        <button
                            @click="bet(match.id, 'draw')"
                            :class="bets.filter(bet => {return bet.match_id == match.id && bet.bet == 'draw' ? true : false}).length > -0 ? 'active' : null"
                        >Empate</button>

                        <button
                            @click="bet(match.id, 'loss')"
                            :class="bets.filter(bet => {return bet.match_id == match.id && bet.bet == 'loss' ? true : false}).length > -0 ? 'active' : null"
                        >Visitante</button>
                    </div>
                    <div class="date-group-info">
                        <div class="group">Grupo <span>{{match.group}}</span></div>
                        <div class="finished">Terminada: <span>{{match.finished === 'FALSE' ? 'NÂO' : 'SIM'}}</span></div>
                        <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                    </div>  
                </div>
            </div>
            <div class="form bet">
                <div class="title">Preencha seus dados</div>

                <div 
                    v-if="betsError" 
                    v-for="error in betsError"
                    class="danger bet-form"
                >{{error}}</div>

                <label for="name">Seu Nome:</label>
                <div 
                    v-if="nameError" 
                    v-for="error in nameError"
                    class="danger bet-form"
                >{{error}}</div>
                <input 
                    v-model="name" 
                    :style="nameError ? 'border-color:rgb(221, 37, 37);' : null" 
                    type="text" 
                    name="name" 
                />

                <label for="phone">Wathsapp</label>
                <div 
                    v-if="phoneError != undefined" 
                    v-for="error in phoneError"
                    class="danger bet-form"
                >{{error}}</div>
                <input 
                    v-model="phone" 
                    @input="phoneMask" 
                    :style="phoneError ? 'border-color:rgb(221, 37, 37);' : null" 
                    type="tel" 
                    id="phone" 
                />
            </div>
            <div class="buttons-bet">
                <button @click="sendBet" class="bet-send">Enviar</button>
                <button @click="this.$parent.closeBetForm()" class="bet-cancel">Cancelar</button>
            </div>
            
        </div>
    </div>
</template>

<style>
    .modal-area-bet {
        position: fixed;
        z-index: 99;
        background-color: rgba(0,0,0,0.4);
        margin-top: -70px;
        width:100vw;
        height: 100%;
        display:flex;
        flex-direction: column;
        align-items: center;
        overflow-y: scroll;
        padding: 70px;
    }
    #bet-form {
        background-color: #fff;
        padding: 10px 40px 40px 40px;
        margin-bottom:200px;
        border-radius: 30px;
    }
    .form {
        height: 400px;
        width: auto;
        margin-top: 20px;
    }
    .bet {
        padding: 20px;
        border:2px solid #000;
        border-radius: 15px;
        height: auto;
    }
    .bet-buttons {
        margin:15px 0px;
        width:100%;
        display: flex;
        justify-content: center;
        margin-right: -20px;
    }
    .bet-buttons button {
        padding: 5px 20px;
        margin-right: 15px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .bet-buttons button.active {
        background-color: #069446;
        color:rgb(255, 238, 0);
        border:none;
        border-radius: 5px;
    }
    .form.bet input {
        width:80%
    }
    .buttons-bet {
        margin-top: 25px;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .bet-send {
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: #069446;
        color:rgb(255, 238, 0);
        border:none;
        border-radius: 5px;
    }
    .bet-send:hover {
        background-color: #09a750;
        transform: scale(1.1);
    }
    .bet-cancel {
        font-size: 15px;
        font-weight: 600;
        background-color: #ddd;
        border:1px solid #888;
        border-radius: 5px;
        cursor: pointer;
    }
    .danger.bet-form {
        width: 80%;
        display: flex;
        justify-content: flex-start;
    }
    .info .score span {
        color:#000;
    }
    .info .score input[type=number] {
        width:30px;
        outline: none;
        padding: 4px 1px;
        font-size: 20px;
        font-weight: 600;
    }
    .info .score input[type=number]::-webkit-inner-spin-button, 
    .info .score input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none; 
        display: none;
    }
</style>