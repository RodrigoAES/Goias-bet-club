<script>
    export default {
        props:['code'],
        data() {
            return {
                userCardCode:this.$route.params.code ? this.$route.params.code : null,
                userCard:null,
                cardCreatedAt:null,
            }
        },
        methods: {
            consultCard:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}card/${this.userCardCode}`, {
                    method:'GET',
                });
                let json = await request.json();

                if(json.status === 'success') {
                    this.userCard = json.user_card;
                    this.cardCreatedAt = json.user_card.created_at;
                    console.log(this.userCard);
                }
            },
            mask: function(e) {
                if(e.target.value.length === 7 && e.inputType != 'deleteContentBackward'){
                    e.target.value = `${e.target.value.substring(0,6)}`;
                    this.userCardCode = e.target.value;
                }
            }
        },
        computed: {
            date: function () {
                let d = new Date(this.cardCreatedAt);
                return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()} ${d.getHours()}:${d.getMinutes()}`;
            }
        }
    }
</script>

<template>
    <div id="consult-area">
        <router-link to="/bolaodefutebol"><span>←</span> Voltar Para a pagina inical</router-link>
        <div class="form user-card">
            <div class="description">Consulte sua cartela através do código que te passamos durante o cadastro.</div>
            
            <div class="card-code">
                <label for="card-code">Código da cartela:</label>
                <input v-model="userCardCode" @input="mask" type="text" name="card-code">
            </div>
            
            <div class="buttons-succes card-code">
                <button @click="consultCard">Consultar</button>
            </div>

        </div>
        <div v-if="userCard" class="card">
            <div class="bet-card"><span>Cartela: {{userCard.card.name}}</span></div>
            <div class="name"><span>Nome: {{userCard.name}}</span></div>
            <div class="user-card phone"><span>Celular: {{userCard.phone}}</span></div>
            <div class="user-card created-at"><span>Data: {{date}}</span></div>
            <div class="validated user-card"><span>Validado: {{userCard.validated ? 'Sim' : 'Não'}}</span></div>
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
                        <div class="score home">{{bet.match.home_score ? bet.match.home_score : 0}}</div>
                    </div>
                    
                    <div class="x">X</div>
    
                    <div class="team away">
                        <div class="score away">{{bet.match.away_score ? bet.match.away_score : 0}}</div>
                        <div class="flag">
                            <img 
                                :src="bet.match.away_flag"
                                :alt="bet.match.away_name"
                            />
                        </div>
                        <div class="team-name">{{bet.match.away_name}}</div>
                    </div>
                </div>
                
                <div v-if="userCard.card.type === 'common'" class="bet-buttons">
                    <button
                        :class="bet.bet === 'victory' ? 'active' : null"
                    >Casa</button>

                    <button
                    :class="bet.bet === 'draw' ? 'active' : null"
                    >Empate</button>

                    <button
                    :class="bet.bet === 'loss' ? 'active' : null"
                    >Visitante</button>
                </div>
                <div v-if="userCard.card.type === 'detailed'" class="user-bet">
                    <span>Voce apostou:</span>
                    <span>
                        <img 
                            :src="bet.match.home_flag"
                            :alt="bet.match.home_name"
                        />

                        {{bet.home_score ? bet.home_score : 0}} X {{bet.away_score ? bet.away_score : 0}}

                        <img
                            :src="bet.match.away_flag"
                            :alt="bet.match.away_name"
                        />
                    </span>
                </div>

                <div class="date-group-info">
                    <div class="group">Grupo <span>{{bet.match.group}}</span></div>
                    <div class="finished">Terminada: <span>{{bet.match.finished === 'FALSE' ? 'NÂO' : 'SIM'}}</span></div>
                    <div class="datetime">Data: <span>{{bet.match.datetime}}</span></div>
                </div>  
            </div>
        </div>
    </div>
</template>

<style>
    #consult-area {
        padding: 30px;
    }
    #consult-area a {
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 5px;
        margin: auto;
        text-decoration: none;
    }
    #consult-area a span{
        font-size: 22px;
        font-weight: 700;
    }
    .user-card {
        height:auto
    }
    .card-code {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }
    .card-code button:hover {
        transform: none;
    }
    .user-card label {
        width:100%;
        display: flex;
        justify-content: flex-start;
        margin-bottom: 5px;
        margin-top: 20px;
    }
    .user-card input {
        font-weight: 600;
        border: 1px solid #888;
        color:#555;
        width:25%;

    }
    .phone.user-card {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        margin-left: 15px;
    }
    .created-at.user-card{
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        margin-left: 15px;
    }
    .validated.user-card {
        font-weight: 600;
        margin-left: 15px;
        margin-bottom: 25px;
    }
    .bet-card {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 25px;
        margin-left: 15px;
        font-size: 18px;
    }
    .user-bet {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .user-bet span {
        font-weight: 700;
    }
    .user-bet span:last-child {
        font-weight: 700;
        font-size: 22px;
        display: flex;
        align-items: center;
    }
    .user-bet span:last-child img {
        max-width:30px;
        max-height: 30px;
    }
    @media (max-width:420px) {
        .user-card input {
            width:60%;
        }
    }
</style>