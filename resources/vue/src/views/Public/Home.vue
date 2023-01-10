<script>
    import BetForm from '../../components/Public/MakeBet/BetForm.vue';
    import BetSuccess from '../../components/Public/MakeBet/BetSuccess.vue';
    import DoubtContactForm from '../../components/Public/Contact/DoubtContactForm.vue';

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

                doubtContactFormOpened:false,
                doubtContactAttendant:null,
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
            },
            redirectWathsapp:function(attendant) {
                this.doubtContactAttendant = attendant;
                this.doubtContactFormOpened = true;
            },
            openPayment:function() {
                let payment = document.querySelector('#payment');
                if(payment.style.height === '42px') {
                    payment.style.height = 'auto';
                    let height = payment.offsetHeight;
                    payment.style.height = '42px';
                    setTimeout(()=>{
                        payment.style.height = `${height - 26}px`;
                        setTimeout(() => {
                            payment.style.height = 'auto';
                        }, 1000)
                    }, 1);
                } else {
                    let height = payment.offsetHeight;
                    payment.style.height = `${height - 26}px`;
                    setTimeout(()=>{
                        payment.style.height = '42px';
                    }, 1);
                    
                }
            },
            openAttendantPayment:function() {
                let att = document.querySelector('#attendants');
                if(att.style.height === '42px') {
                    att.style.height = 'auto';
                    let height = att.offsetHeight;
                    att.style.height = '42px';
                    setTimeout(()=>{
                        att.style.height = `${height}px`;
                    }, 1);
                } else {
                    att.style.height = '42px';
                }
            },
            openAutopay:function() {
                let autopay = document.querySelector('#autopay');
                if(autopay.style.height === '42px') {
                    autopay.style.height = 'auto';
                    let height = autopay.offsetHeight;
                    autopay.style.height = '42px';
                    setTimeout(()=>{
                        autopay.style.height = `${height}px`;
                    }, 1);
                } else {
                    autopay.style.height = '42px';
                }
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
            endtimesExpired:function() {
                let expireds = [];

                this.cards.forEach(card => {
                    let endtime = new Date(card.endtime);
                    expireds.push(endtime < new Date() ? true : false);
                });
                
                return expireds;
            },
            paymentAttendants:function() {
                return this.$root.attendants.filter(attendant => attendant.payment_permission ? true : false);
            },
            doubtAttendants:function() {
                return this.$root.attendants.filter(attendant => attendant.doubt_permission ? true : false);
            }
            
        },
        components:{
            BetForm,
            BetSuccess,
            DoubtContactForm,
        },  
        async mounted() {
            async function requestCards() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}cards`, {
                method:'GET',
                });

                let json = await request.json();

                return {
                    status: json.status,
                    cards: json.cards,
                }
            }

            let data = await requestCards();
            this.cards = data.cards; 

            let body = new FormData();
            body.append('attendant_slug', this.$route.params.attendant ? this.$route.params.attendant : '');

            let request = await fetch(`${import.meta.env.VITE_BASE_URL}entry`, {
                method:'POST',
                body: body,
            });

            let json = await request.json();
            if(json.status === 'success') {
                sessionStorage.setItem('entry', entry);
            }
            

            
        },
    }
</script>

<template>
    <BetForm v-if="betFormOpened" :card="this.betFormCard"/>
    <BetSuccess v-if="betSuccessOpened" :response="betResponse" />
    <DoubtContactForm v-if="doubtContactFormOpened" :attendant="doubtContactAttendant" />

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
            <div id="payment" style="height:42px">
                <div @click="openPayment" class="label">Efetuar pagamento</div>

                <div id="attendants" style="height:42px">
                    <div @click="openAttendantPayment" class="label-attendants">Pagar com atendente</div>

                    <button
                        v-for="attendant in paymentAttendants"
                        @click="redirectToPaymentContactWhatsapp(attendant)"
                    >
                        {{ `${attendant.name.split(' ')[0]} ${attendant.name.split(' ')[1] ? attendant.name.split(' ')[1].charAt(0)+'.' : ''}` }}
                        <img :src="`${this.$root.asset}assets/icons/whatsapp.png`" />
                    </button>
                </div>

                <div id="autopay" style="height:42px">
                    <div @click="openAutopay" class="label-autopay">Pagar Com Pix</div>

                </div>
            </div>

            <div class="contact">
                <div class="message">
                    Alguma duvida? Fale conosco:
                </div>

                <div class="phones">
                    <button 
                        v-for="attendant in doubtAttendants"
                        @click="redirectWathsapp(attendant)"
                    >
                        <img :src="`${this.$root.asset}assets/icons/whatsapp.png`" />
                        <span>{{`${attendant.name.split(' ')[0]} ${attendant.name.split(' ')[1] ? attendant.name.split(' ')[1].charAt(0)+'.' : ''}`}}</span>
                    </button>
                </div>
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
                <div v-for="(card, index) in cards" class="card-slot">

                    <div class="endtime-message">
                        <span
                            :style="`${endtimesExpired[index] ? 'color: rgb(221, 37, 37)' : '#006ed4'}`"
                        >O prazo para envio de apostas para está cartela se {{endtimesExpired[index] ? 'encerrou' : 'encerra'}} em {{dates[index]}}</span>
                    </div>

                    <div 
                        :style="`
                            background-image:url(${this.$root.asset}core/public/bonus_bg_image);
                        `" 
                        class="bonus"
                    >
                        <div class="text">
                            Estimativa de prêmio R${{card.valuation}} {{ `${card.bonus ? '+ R$'+ card.bonus + ' de Bônus!!!' : ''}` }}
                        </div>

                    </div>

                    <div  class="card">
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
                                            :src="match.home_flag"
                                            :alt="match.home_name" 
                                        />
                                    </div>
                                    <div class="score home">{{match.home_score ? match.home_score : 0}}</div>
                                </div>
                                
                                
                                <div class="x">X</div>
                
                                <div class="team away">
                                    <div class="score away">{{match.away_score ? match.away_score : 0}}</div>
                                    <div class="flag">
                                        <img 
                                            :src="match.away_flag"
                                            :alt="match.away_name"
                                        />
                                    </div>
                                    <div class="team-name">{{match.away_name}}</div>
                                </div>
                            </div>
                            <div class="date-group-info">
                                <div class="group">Grupo <span>{{match.group}}</span></div>
                                <div class="finished">Terminada: <span>{{match.finished ? 'SIM' : 'NÃO'}}</span></div>
                                <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                            </div>  
                        </div>
                        <div class="price">R$ <span>{{card.price.toFixed(2)}}</span></div>
                        <div class="buttons-card">
                            <button @click="makeBets(card)">Fazer Aposta</button>
                        </div>
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
    #payment {
        margin-top: 20px;
        border:3px solid var(--p-color);
        padding: 20px;
        padding-top: 0;
        width: 256px;
        border-radius: 10px;
        overflow: hidden;
        transition:all ease 1s;
    }
    #payment .label {
        font-size: 18px;
        font-weight: 600;
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 20px 0px;
        margin-bottom: 20px;
        color: var(--s-color);
        background-color: var(--p-color);
        width:116%;
        margin-left: -8%;
        cursor: pointer;
    }
    #payment #attendants {
        display:flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        overflow-y: hidden;
        transition: all ease 1s;
        height: 42px;
    }
    #payment #autopay {
        display:flex;
        flex-direction: column;
        align-items: center;
        overflow-y: hidden;
        transition: all ease 1s;
    }
    #payment .label-attendants,
    #payment .label-autopay {
        background-color: var(--p-color);
        color: var(--s-color);
        margin-bottom: 10px;
        padding: 10px 20px;
        display: flex;
        justify-content: center;
        font-size: 18px;
        font-weight: 600;
        border-radius: 4px;
        cursor: pointer;
    }
    #payment #attendants button {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 16px;
        font-weight: 600;
        font-family: Montserrat;
        padding: 8px 15px;
        background-color: #ddd;
        margin-bottom: 5px;
        border:none;
        border-radius: 6px;
        color:var(--s-color);
        background-color: var(--p-color);
        cursor: pointer;
        width:90%;
    }
    #payment #attendants button:hover {
        background-color: var(--p-color-h);
    }
    #payment #attendants button img {
        width:20px
    }
    .contact {
        margin-top: 60px;
        margin-bottom: 20px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        
    }
    .contact .phones {
        width:400px;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    .contact button {
        background-color: transparent;
        display:flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        border:none;
        cursor: pointer;
        padding: 0;
        font-weight: 600;
        font-family: Montserrat;
        border-radius: 10px;
        padding:5px;
        margin-right: 15px;
        width:85px;
    }
    .contact button:hover {
        background-color: #ddd;
        transform: scale(1.1);
    }
    .contact img {
        width:40px;
        border-radius: 50%;
        overflow: hidden;
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
    .home-page .card-slot {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .home-page .endtime-message {
        font-size: 18px;
        font-weight: 600;
        margin-top: 40px;
        width:350px;
        line-height: 30px;
        text-align: center;
    }

    .home-page .bonus {
        width: 400px;
        height: 100px;
        margin-top: 20px;
        border-radius: 10px;
        display: flex;
        padding:10px;
        background-size:cover;
        background-position: center;
        
    }

    .home-page .bonus .text {
        color: #fff;
        width:280px;
        font-size: 18px;
        font-weight: 700;
        background: -webkit-linear-gradient(var(--bonus-text-color-1), var(--bonus-text-color-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
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
        .home-page .bonus {
            width: 330px;
        }
        .home-page .bonus .text {
            width:200px;
        }
    }
</style>