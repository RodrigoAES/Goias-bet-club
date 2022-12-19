<script>
    import BetForm from '../../components/Public/MakeBet/BetForm.vue';
    import BetSuccess from '../../components/Public/MakeBet/BetSuccess.vue';

    export default {
        data() {
            return {
                asset:import.meta.env.VITE_ASSET,
                base:import.meta.env.VITE_BASE_URL,
                cards:[],
                betFormOpened:false,
                betFormCard:null,
                betSuccessOpened:false,
                betResponse:null,
            }
        },
        methods:{
            makeBets: function(card) {
                this.betFormCard = card;
                this.betFormOpened = true;
            },
            closeBetForm: function () {
                this.betFormOpened = false;
            },
            success: function (response) {
                this.betFormOpened = false;
                this.betResponse = response;
                this.betSuccessOpened = true;
            },
            closeSuccess: function() {
                this.betSuccessOpened = false;
            },
            toUserCard: function(code) {
                this.$router.push({path:`bolaodefutebol/user-card/${code}`});
            }

        },  
        computed: {
            dates:function() {
                let formatedDates = []
                this.cards.forEach(card => {
                    let endtime = card.endtime.split(' ');
                    let date = endtime[0].split('-');
                    date = `${date[2]}/${date[1]}/${date[0]}`;
                    let time = endtime[1].split(':');
                    time = `${time[0]}:${time[1]}`;

                    endtime = `${date} ${time}`;

                    formatedDates.push(endtime);
                });

                return formatedDates;
            },
        },
        components:{
            BetForm,
            BetSuccess,
        },  
        async mounted() {
            async function request() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}cards`, {
                method:'GET',
                });

                let json = await request.json();

                return {
                    status: json.status,
                    cards: json.cards,
                }
            }

            let data = await request();
            if(data.status != 200) {

            }
            this.cards = data.cards; 
        },
    }
</script>

<template>
    <BetForm v-if="betFormOpened" :card="this.betFormCard"/>
    <BetSuccess v-if="betSuccessOpened" :response="betResponse" />

    <div class="home-page" :style="`background-image: url(${this.$root.asset}core/public/home_bg)`">
        <div class="content">
            <div class="logo">
                <img :src="`${this.$root.asset}core/public/logo`" alt="logo">
            </div>
            <div class="consult">
                <router-link to="/bolaodefutebol/user-card">Consultar minhas cartelas</router-link>
            </div>
            <div style="margin-top:20px" class="consult">
                <router-link to="/bolaodefutebol/ranking">Consultar Ranking</router-link>
            </div>
            <div class="anoucement">
                É fácil participar, decida quem ganha ou se empata, foi o que mais acertou? <span>GANHOU!</span> 
            </div>

            <div class="instruction">
                Para inserir uma ou mais cartelas, clique no botão <span>Fazer Aposta</span> localizado abaixo da cartela. 
            </div>

            <div class="rules-title">Regulamentação</div>
            <div class="rules">
                <div 
                    v-if="this.$root.rules"
                    v-for="(rule, index) in this.$root.rules"
                    class="rule"
                >
                    <span>{{index+1}}-</span>
                    {{rule}}
                </div>
            </div>

            <div class="cards">
                <div class="cards-title">Cartelas disponiveis para jogar:</div>

                <div v-for="(card, index) in cards" class="card">
                    <div class="name"><span>Nome: {{card.name}}</span></div>
                    <div class="endtime"><span>Encerramento: {{dates[index]}}</span></div>
                    <div class="type">
                        <span>
                            Tipo: {{
                                card.type === 'common' ? 'Comum' : 
                                card.type === 'detailed' ? 'Datalhada' :
                                null
                            }}
                        </span>
                    </div>
                    <div class="award"><span>Premio acumulado: R${{card.award.toFixed(2)}}</span></div>
                    <div class="home-away"><span>Casa</span><span>Visitante</span></div>
                    <input type="hidden" :value="card.id" />

                    <div v-for="match in card.matchs" class="match">
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
                                        :src="`${this.$root.base}brasileirao/flag/${match.home_flag}`" 
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
                                <div class="score home">{{match.home_score}}</div>
                            </div>
                            
                            
                            <div class="x">X</div>
            
                            <div class="team away">
                                <div class="score away">{{match.away_score}}</div>
                                <div class="flag">
                                    <img 
                                        v-if="card.championship === 'world-cup'" 
                                        :src="match.away_flag" 
                                        :alt="`${match.away_name} flag`" 
                                    />

                                    <img 
                                        v-else-if="card.championship === 'brasileirao'" 
                                        :src="`${this.$root.base}brasileirao/flag/${match.away_flag}`" 
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
                        <div class="date-group-info">
                            <div class="group">Grupo <span>{{match.group}}</span></div>
                            <div class="finished">Terminada: <span>{{match.finished === 'FALSE' ? 'NÂO' : 'SIM'}}</span></div>
                            <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                        </div>  
                    </div>
                    <div class="price">R$ <span>{{card.price}},00</span></div>
                    <div class="buttons-card">
                        <button @click="makeBets(card)">Fazer Aposta</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style>
    .home-page {
        min-width:100vw;
        min-height: 100vh;
        background-size: cover;
        background-position: center;
        display:flex;
        justify-content: center;
    }
    .home-page .logo img{
        margin-top: -110px;
        left: calc(50% - 60px);
        position: absolute;
        width:130px;
    }
    .content {
        width:calc(100vw - 400px);
        min-height: 100vh;
        background-color: #fff;
        display:flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 20px;
    }
    .consult {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 10px;
        height: 40px;
        margin-top: 40px;
    }
    .consult a {
        padding: 15px 20px;
        margin-right: 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 5px;
        margin: auto;
        text-decoration: none;
    }
    .consult a:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
        
    }
    .anoucement {
        font-size: 18px;
        font-weight: 600;
        margin-top: 20px;

    }
    .anoucement span {
        color:var(--p-color);
        font-weight: 700;
    }
    .instruction {
        font-size: 16px;
        font-weight: 600;
        color:#999;
        margin-top: 20px;
    }
    .instruction span {
        font-weight: 700;
        color: var(--p-color);
        font-size: 18px;
    }
    .cards {
        margin-top: 25px;
    }
    .cards-title {
        width:100%;
        display: flex;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        color: var(--p-color);
    }
    .buttons-card {
        width:100%;
        display: flex;
        justify-content: center;
    }
    .buttons-card button{
        padding: 10px 20px;
        font-size: 20px;
        font-weight: 600;
        border:none;
        background-color: var(--p-color);
        color: var(--s-color);
        border-radius: 6px;
        cursor: pointer;
    }
    .buttons-card button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
    .price {
        width: 100%;
        display: flex;
        justify-content: center;
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 30px;
    }
    .price span {
        margin-left: 6px;
    }

    .card .type {
        font-weight: 600;
        margin-left: 15px;
        margin-bottom: 20px;
    }
    .rules-title {
        margin-top: 40px;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .home-page .rules {
        border: 2px solid #888;
        padding: 20px;
        width:400px;
        max-height: 250px;
        overflow-y: scroll;
    }
    .home-page .rules::-webkit-scrollbar {
        -webkit-appearance: none;
        width:5px
    }
    .home-page .rules::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 50px;
    }
    .home-page .rules::-webkit-scrollbar-thumb:hover {
        background-color: #777;
    }
    .home-page .rules .rule {
        margin-bottom: 30px;
    }
    .home-page .rules .rule span {
        font-size: 18px;
        font-weight: 700;
    }

    @media (max-width: 420px) {
        .content {
            width:90vw;
        }
        .instruction span {
            font-size: 16px;
        }
        .cards {
            width:95vw;
            padding: 10px;
            margin-right: -1px;
        }
        .cards-title {
            width: 360px;
            margin-left: -4px;
        }
        .anoucement,  
        .instruction {
            margin-left: 25px;
        }
        .buttons-card{
            margin-bottom: 20px;
        }
         
        .home-page .rules {
            width:300px;
        }
        .home-page .logo img{
            top:180px;
            
        }
        .consult {
            margin-top: 100px;
        }
    }
</style>