<script>
    export default {
        props:['userCard'],
        methods:{
            switchBet:function(index, bet) {
                this.userCard.bets[index].bet = bet;
            },
            switchBetDetailed:function(matchId, team, index) {
                if(team === 'home') {
                    let input = document.querySelector(`input.home_score[data-key="${matchId}"]`);
                    if(input.value.length > 2) {
                        input.value = input.value.substring(0,2);
                    }

                    this.userCard.bets[index].home_score = parseInt(input.value);
                }

                if(team === 'away') {
                    let input = document.querySelector(`input.away_score[data-key="${matchId}"]`);
                    if(input.value.length > 2) {
                        input.value = input.value.substring(0,2);
                    }

                    this.userCard.bets[index].away_score = parseInt(input.value);

                }
            },
            updateCard: async function() {
                let body = new FormData();

                body.append('name', this.userCard.name);
                body.append('phone', this.userCard.phone);
                for(let i=0; i<this.userCard.bets.length; i++){
                    body.append('bets[]', JSON.stringify(this.userCard.bets[i]));
                }

                body = new URLSearchParams(body);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user-card/${this.userCard.id}`, {
                    method:'PUT', //PUT
                    body: body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.userCardEditorOpened = false;
                    this.$parent.userCardEditorSuccessCard = json.user_card;
                    this.$parent.UserCardEditorSuccessOpened = true;
                }
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
        }
    }
</script>

<template>
    <div class="modal-area admin-card-viewer">
        <div class="editor" id="bet-form">
            <div class="card">
                <div class="close"><span @click="this.$parent.closeCard()">✖</span></div>
                <div class="name"><span>Cartela: {{userCard.card.name}}</span></div>
                <div class="home-away"><span>Casa</span><span>Visitante</span></div>

                <div v-for="(bet, index) in userCard.bets" class="match bet">
                    <div class="info">
                        <div class="team home">
                            <div class="team-name">{{bet.match.home_name}}</div>
                            <div class="flag">
                                <img 
                                    :src="bet.match.home_flag"
                                    :alt="bet.match.home_name"
                                />
                            </div>
                            <div class="score home">
                                <span v-if="userCard.card.type === 'common'">{{bet.match.home_score ? bet.match.home_score : 0}}</span>

                                <input 
                                    v-if="userCard.card.type === 'detailed'"
                                    @input="switchBetDetailed(bet.match.id, 'home', index)"
                                    type="number" 
                                    :data-key="bet.match.id"
                                    :value="bet.home_score"
                                    class="home_score"
                                />
                            </div>
                        </div>
                        
                        <div class="x">X</div>
        
                        <div class="team away">
                            <div class="score away">
                                <span v-if="userCard.card.type === 'common'">{{bet.match.away_score ? bet.match.away_score : 0}}</span>

                                <input 
                                    v-if="userCard.card.type === 'detailed'" 
                                    @input="switchBetDetailed(bet.match.id, 'away', index)"
                                    type="number" 
                                    :data-key="bet.match.id"
                                    :value="bet.away_score"
                                    class="away_score"
                                />
                            </div>
                            <div class="flag">
                                <img 
                                    :src="bet.match.away_flag"
                                    :alt="bet.match.away_name"
                                />
                            </div>
                            <div class="team-name">{{bet.match.away_name ? bet.match.away_name : 0}}</div>
                        </div>
                    </div>
                    <div v-if="userCard.card.type === 'common'" class="bet-buttons">
                        <button
                            @click="switchBet(index, 'victory')"
                            :class="bet.bet === 'victory' ? 'active' : null"
                        >Casa</button>

                        <button
                            @click="switchBet(index, 'draw')"
                            :class="bet.bet === 'draw' ? 'active' : null"
                        >Empate</button>

                        <button
                            @click="switchBet(index, 'loss')"
                            :class="bet.bet === 'loss' ? 'active' : null"
                        >Visitante</button>
                    </div>
                    <div class="date-group-info">
                        <div class="group">Grupo <span>{{bet.match.group}}</span></div>
                        <div class="finished">Terminada: <span>{{bet.match.finished ? 'SIM' : 'NÃO'}}</span></div>
                        <div class="datetime">Data: <span>{{bet.match.datetime}}</span></div>
                    </div>  
                </div>
            </div>
            <div class="form bet">
                <div class="title">Preencha seus dados</div>

                <label for="name">Seu Nome:</label>
                <input v-model="userCard.name" type="text" name="name">

                <label for="phone">Wathsapp</label>
                <input  v-model="userCard.phone" @input="phoneMask()" type="tel" id="phone">
            </div>
            <div class="buttons-bet">
                <button @click="updateCard()" class="bet-send">Salvar</button>
            </div>
            
        </div>
    </div>
</template>

<style>
    #bet-form.editor {
        margin-top: 40px;
    }

    @media (max-width:420px) {
        .form.bet {
            margin-left: -25px;
        }
    }
</style>