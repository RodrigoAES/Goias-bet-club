<script>
    import Confirm from './CardsConfirm.vue';

    export default {
        data() {
            return {
                cards:null,
                confirmOpened:false,
                cardToDelete:null,
            }
        },
        computed: {
            endtimes: function() {
                let endtimes = [];
                this.cards.forEach(card => {
                    let endtime = card.endtime.split(' ');
                    let date = endtime[0].split('-');
                    date = `${date[2]}/${date[1]}/${date[0]}`;
                    let time = endtime[1].split(':');
                    time = `${time[0]}:${time[1]}`;

                    endtime = `${date} ${time}`;

                    endtimes.push(endtime);
                });
                return endtimes;
            }
        },
        methods:{
            deleteCard: async function(card, id) {
                async function request() {
                    let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/card/${id}`, {
                    method:'DELETE',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }

                    });
                    let json = await request.json();
                    return {
                        status:request.status,
                        response:json
                    }
                }

                if((await request()).response.deleted) {
                    console.log('entrou no if');
                    this.cards.splice(this.cards.indexOf(card), 1);
                    console.log(this.cards);
                }
            },
            confirm:function(card, id) {
                this.cardToDelete = card;
                this.cardIdToDelete = id;
                this.confirmOpened = true;
            }
        },  
        components:{
            Confirm
        },  
        async beforeCreate() {
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}cards`, {
            method:'GET',
            });

            let json = await request.json();

            this.cards = json.cards;
            
        }
    }
</script>

<template>
    <Confirm v-if="confirmOpened" :card="this.cardToDelete" />
    <div id="cards">
        <div class="title">Cartelas</div>

        <div v-for="(card, index) in cards" class="card">
            <div class="name"><span>Nº: {{card.id}}</span><span>Nome: {{card.name}}</span></div>
            <div class="endtime"><span>Encerramento: {{endtimes[index]}}</span></div>
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
                        <div class="score home">{{match.home_score ? match.home_score : 0}}</div>
                    </div>
                    
                    
                    <div class="x">X</div>
    
                    <div class="team away">
                        <div class="score away">{{match.away_score ? match.away_score : 0}}</div>
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
                    <div class="finished">Terminada: <span>{{match.finished ? 'SIM' : 'NÃO' }}</span></div>
                    <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                </div>  
            </div>
    
            <div class="buttons">
                <button @click="confirm(card, card.id)">Excluir</button>
            </div>
        </div>
    </div>
   
</template>

<style>
    #cards{
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .title {
        font-size: 25px;
        font-weight: 700;
    }
    .none {
        font-size: 16px;
        font-weight: 600;
        margin-top: 20px;
    }
    .card {
        border: 2px solid #000;
        padding: 30px;
        margin-top: 30px;
        border-radius: 20px;
    }
    .card .name {
        font-size: 18px;
        font-weight: 600;
        width: 100%;
        display: flex;
    }
    .card .home-away {
        display: flex;
        justify-content: space-around;
        width:100%;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .card .name span{
        margin-left: 15px;
        margin-bottom: 20px;
    }
    .card .award {
        font-weight: 600;
        margin-left: 15px;
        margin-bottom: 20px;
    }
    .buttons {
        width:100%;
        display: flex;
        justify-content: center;
        margin-bottom: 15px;
    }
    .buttons button {
        border:none;
        border-radius: 6px;
        padding: 10px 20px;
        background-color: rgb(221, 37, 37);
        color:#fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
    .endtime {
        font-weight: 600;
        margin-bottom: 20px;
        margin-left: 14px;
    }

    @media (max-width: 420px) {
        .card {
            border: 2px solid #000;
            padding: 5px;
            margin-top: 30px;
            border-radius: 20px;
        }
    }
</style>