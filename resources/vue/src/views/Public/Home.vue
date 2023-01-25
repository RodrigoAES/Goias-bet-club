<script>
    import BetForm from '../../components/Public/MakeBet/BetForm.vue';
    import BetSuccess from '../../components/Public/MakeBet/BetSuccess.vue';
    import DoubtContactForm from '../../components/Public/Contact/DoubtContactForm.vue';
    import Loading from '../../components/Loading.vue';

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

                cardCode:null,
                qrcode:null,
                qrcodeImage:null,

                qrcodeLoading:false,

                qrcodeError:null,

                whatsappGroup: this.$root.whatsappGroup ?? null,
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
                this.$router.push({path:`/user-card/${code}`});
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
                        }, 1000);
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
                if(att.style.height === '44px') {
                    att.style.height = 'auto';
                    let height = att.offsetHeight;
                    att.style.height = '44px';
                    setTimeout(()=>{
                        att.style.height = `${height}px`;
                    }, 1);
                } else {
                    att.style.height = '44px';
                }
            },
            openAutopay:function() {
                let autopay = document.querySelector('#autopay');
                if(autopay.style.height === '44px') {
                    autopay.style.height = 'auto';
                    let height = autopay.offsetHeight;
                    autopay.style.height = '44px';
                    setTimeout(()=>{
                        autopay.style.height = `${height}px`;
                        setTimeout(()=>{
                            autopay.style.height = 'auto';
                        }, 1000);
                    }, 1);
                } else {
                    autopay.style.height = `${autopay.offsetHeight}px`;
                    setTimeout(()=>{
                        autopay.style.height = '44px';
                    }, 1);
                    
                }
            },
            copyQrcode:function() {
                navigator.clipboard.writeText(this.qrcode);
                document.querySelector('#copy-message').style.display = 'block';
            },
            generateCharge:async function() {
                this.qrcode = null;
                this.qrcodeImage = null;
                this.qrcodeError = null;

                let qrcodeArea = document.querySelector('#qrcode');
                if(qrcodeArea.style.height === '0px') {
                    qrcodeArea.style.height = '0px';
                    setTimeout(()=>{
                        document.querySelector('#qrcode').style.height = '335px';
                    }, 1);
                }
                
                this.qrcodeLoading = true;

                let url = `${import.meta.env.VITE_BASE_URL}charge/${this.cardCode}`;
                url += sessionStorage.entry ? `?entry=${sessionStorage.getItem('entry')}` : '';
                let request = await fetch(url);
                let json = await request.json();

                if(json.status === 'success') {
                    this.qrcodeLoading = false;
                    this.qrcode = json.qrcode.qrcode;
                    this.qrcodeImage = json.qrcode.imagemQrcode;

                } else if(json.status === 'error') {
                    this.qrcodeLoading = false;
                    this.qrcodeError = json.error;
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
            
            Loading,
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
                sessionStorage.setItem('entry', json.entry.id);
            }
        },
    }
</script>

<template>
    <BetForm v-if="betFormOpened" :card="this.betFormCard"/>
    <BetSuccess v-if="betSuccessOpened" :response="betResponse" />
    <DoubtContactForm v-if="doubtContactFormOpened" :attendant="doubtContactAttendant" />

    

    <div class="home-page" >
        <div class="banner">
            <video src="/assets/videos/banner-goiasbetclub-video_loop.mp4" loop autoplay muted></video>
        </div>
        <div class="logo">
            <img :src="`${asset}core/public/logo`" alt="logo">
        </div>
        <div class="content" :style="`background-image: url(${this.$root.asset}core/public/home_bg)`">
            <div class="background" :style="`background-image:url(${this.$root.asset}core/public/home_content_bg)`">
                <div class="consult">
                    <router-link to="/user-card">Consultar minhas cartelas</router-link>
                </div>

                <div style="margin-top:20px" class="consult">
                    <router-link to="/ranking">Consultar Ranking</router-link>
                </div>

                <div class="whatsapp-group">
                    <a :href="this.$root.whatsappGroup ?? null">
                        Grupo do Whatsapp
                        <img :src="`${this.$root.asset}assets/icons/whatsapp.png`">
                    </a>
                </div>

                <div id="payment" style="height:42px">
                    <div @click="openPayment" class="label">Efetuar pagamento</div>

                    <div v-if="paymentAttendants.length > 0" id="attendants" style="height:44px">
                        <div @click="openAttendantPayment" class="label-attendants">Pagar com atendente</div>

                        <button
                            v-for="attendant in paymentAttendants"
                            @click="redirectToPaymentContactWhatsapp(attendant)"
                        >
                            {{ `${attendant.name.split(' ')[0]} ${attendant.name.split(' ')[1] ? attendant.name.split(' ')[1].charAt(0)+'.' : ''}` }}
                            <img :src="`${this.$root.asset}assets/icons/whatsapp.png`" />
                        </button>
                    </div>

                    <div id="autopay" style="height:44px">
                        <div @click="openAutopay" class="label-autopay">Pagar Com Pix</div>
                        <div class="code">
                            <label for="code">Insirá o código da sua cartela:</label>
                            <input v-model="cardCode" type="text" maxlength="6">
                            <button @click="generateCharge">Gerar QRcode</button>
                        </div>

                        <div id="qrcode" style="height:0px">
                            <Loading 
                                v-if="qrcodeLoading"
                                :size="30"
                            />

                            <div v-if="qrcodeError" class=danger style="background-color:#fff; padding:10px; border-radius:5px">
                                {{ qrcodeError }}
                            </div>

                            <div v-if="qrcodeImage" class="qrcode-image">
                                <img :src="qrcodeImage" />
                            </div>

                            <div v-if="qrcode" class="qrcode-code">
                                <span id="copy-message" style="display:none;">Copiado!</span>
                                <button @click="copyQrcode">
                                    Copiar
                                    <img src="/assets/icons/copy.png" alt="">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact">
                    <div class="message">
                        Duvidas e sujestões? Fale com nossa equipe:
                    </div>

                    <div class="phones">
                        <button 
                            v-for="attendant in doubtAttendants"
                            @click="redirectWathsapp(attendant)"
                        >
                            <div class="icon">
                                <img :src="`${this.$root.asset}assets/icons/whatsapp.png`" />
                            </div>
                            <span>{{`${attendant.name.split(' ')[0]} ${attendant.name.split(' ')[1] ? attendant.name.split(' ')[1].charAt(0)+'.' : ''}`}}</span>
                        </button>
                    </div>
                </div>

                <div class="anoucement">
                    É fácil participar, em cartelas do tipo comum decida quem ganha ou se empata e em cartelas do tipo detalhada decida qual será o placar, foi o que mais acertou? <span>GANHOU!</span> 
                </div>

                <div class="instruction">
                    Para inserir uma ou mais cartelas, clique no botão <span>Fazer Aposta</span> localizado abaixo da cartela e depois é só efetuar o pagamento via pix com QRcode na janela que irá abrir ou no botão <span>Efetuar Pagamento</span> logo acima aqui na pagina principal. 
                </div>

                <div class="rules-title">Regulamentação</div>
                <div class="rules" :style="`background-image: url(${this.$root.asset}core/public/rules_bg)`">
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
    </div>
</template>

<style>
    .banner {
        width:100%;
    }
    .banner video {
        width: 100%;
    }
    .home-page {
        min-width:100vw;
        min-height: 100vh;
        display:flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .content {
        width:calc(100vw - 400px);
        min-height: 100vh;
        width: 100%;
        padding: 80px 20px;
        background-size: cover;
        background-position: center;
        display:flex;
        justify-content: center;
    }
    .home-page .logo {
        margin-top: -5px;
        height: 140px;
        width:100%;
        background-color: var(--t-color);
        display: flex;
        justify-content: center;
    }
    .home-page .logo img {
        height: 100%;
    }
    .background {
        background-color: #30b306;
        display:flex;
        flex-direction: column;
        align-items: center;
        width:1000px;
        color:#fff;
        background-size: 50%;
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
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        width:300px;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 8% / 50%;
        margin: auto;
        text-decoration: none;
        box-shadow: 8px 5px 0px var(--s-color);
        display: flex;
        justify-content: center;
    }
    .consult a:hover {
        transform: scale(1.1);
        
    }
    .whatsapp-group {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 10px;
        height: 40px;
        margin-top: 20px;
    }
    .whatsapp-group a {
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        width:300px;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 8% / 50%;
        margin: auto;
        text-decoration: none;
        box-shadow: 8px 5px 0px var(--s-color);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .whatsapp-group a img {
        width: 28px;
        height: 28px;
        margin-left: 10px;
    }
    .whatsapp-group a:hover {
        transform: scale(1.1);
    }
    #payment {
        margin-top: 20px;
        border:3px solid var(--p-color);
        padding: 20px;
        padding-top: 0;
        width: 300px;
        border-radius: 10px;
        overflow: hidden;
        transition:all ease 1s;
        border-radius: 8% / 50%;
        box-shadow: 8px 5px 0px var(--s-color);
    }
    #payment .label {
        font-size: 20px;
        font-weight: 700;
        width: 200%;
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
    #payment #autopay .code {
        display:flex;
        flex-direction: column;
        align-items: center;
    }
    #payment #autopay label {
        font-weight: 600;
    }
    #payment #autopay input {
        text-align: center;
        width:100px;
        margin-top: 10px;
        padding:6px 10px;
        font-size: 16px;
        border:1px solid #888;
        color:#555;
        outline:none;
        border-radius: 4px;
    }
    #payment #autopay button {
        margin-top:10px;
        font-size: 16px;
        font-weight: 600;
        padding:5px 10px;
        border:none;
        border-radius: 6px;
        background-color: var(--p-color);
        color:var(--s-color);
        cursor:pointer;

        display: flex;
        align-items: center;
    }
    #payment #autopay button img {
        width:30px;
        margin-left: 10px;
    }
    #payment #autopay #qrcode {
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all ease 1s;
    }
    #payment #autopay .qrcode-image {
        margin:20px 0px
    }
    #payment #autopay .qrcode-code {
        height:100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    #payment #autopay .qrcode-code button {
        width:auto;
        padding: 10px 20px;
        background-color: transparent;
        color:#fff;
        border: 2px solid #fff;
        margin-top: 0;
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
    .contact .message {
        background-color: var(--s-color);
        padding: 10px 20px;
        border-radius: 5% / 50%;
        font-size: 20px;
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
        border-radius: 50%;
        cursor: pointer;
        padding: 0;
        font-weight: 600;
        font-family: open Sans;
        border-radius: 10px;
        padding:5px;
        margin-right: 15px;
        width:85px;
        color:#fff
    }
    .contact button:hover {
        transform: scale(1.1);
    }
    .contact .icon {
        border-radius: 50%;
        width: 60px;
        height: 60px;
        background-color: #fff;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .contact img {
        width:40px;
        padding: 20px;
    }
    .rules-title {
        margin-top: 40px;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .home-page .rules {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border: none;
        border-radius: 5% / 50%;
        background-color: var(--p-color);
        box-shadow: 20px 20px 0px var(--s-color);
        font-weight: 700;
        color:#000;
        padding: 20px 40px;
        width:400px;
        max-height: 250px;
        overflow-y: scroll;
    }
    .home-page .rules::-webkit-scrollbar {
        -webkit-appearance: none;
        display:none;
    }
    .home-page .rules .rule {
        margin-bottom: 30px;
    }
    .home-page .rules .rule span {
        font-size: 18px;
        font-weight: 700;
    }
    .anoucement {
        font-size: 20px;
        font-weight: 700;
        margin-top: 20px;
        padding:0px 20px;
        
    }
    .anoucement span{
        font-weight: 700;
        color: var(--p-color);
        font-size: 18px;
    }
    .instruction {
        font-size: 16px;
        font-weight: 700;
        margin-top: 20px;
        padding: 0px 20px;
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
        color: #fff;
        margin-top: 40px;
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
        background-color: var(--s-color);
        color: var(--p-color);
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
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 30px;
        color:#000;
    }
    .price span {
        margin-left: 6px;
    }

    .card .type {
        font-weight: 600;
        margin-left: 15px;
        margin-bottom: 20px;
        color:var(--s-color);
        font-size: 20px;
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
        height: 120px;
        margin-top: 20px;
        border-radius: 5% /50%;
        display: flex;
        padding:10px;
        background-size:cover;
        background-position: center;
        box-shadow: 20px 15px 0px var(--p-color);
    }

    .home-page .bonus .text {
        color: #fff;
        padding: 10px;
        width:280px;
        font-size: 22px;
        font-weight: 700;
        background: -webkit-linear-gradient(var(--bonus-text-color-1), var(--bonus-text-color-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    @media (max-width: 420px) {
        .banner {
            width:100vw;
        }
        .banner video {
            width: 100%;
        }
        .home-page .logo {
            margin-top: -5px;
            height: 50px;
            width:100%;
            background-color: var(--t-color);
            display: flex;
            justify-content: center;
        }
        .home-page .logo img {
            height: 100%;
        }
        .content {
            width:100vw;
            padding:20px 
        }
        .background {
            width: 360px;
        }
        .consult {
            margin-top: 20px;
        }
        .consult a {
            font-size: 18px;
            width:260px;
        }
        #payment {
            width: 260px;
        }
        .contact .message {
            font-size:16px;
            width: 260px;
        }
        .anoucement {
            font-size: 16px;
            font-weight: 700;
            margin-top: 20px;
            
        }
        .instruction {
            width:260px
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
            width: 350px;
            font-size: 18px;;
        }
        .anoucement,  
        .instruction {
            margin-left: 25px;
        }
        .buttons-card{
            margin-bottom: 20px;
        }
         
        .home-page .rules {
            width:220px;
            margin-left: -10px;
            font-size: 16px;
            text-align: center;
            
        }
        
        .home-page .endtime-message {
            width:260px;
        }
        .home-page .bonus {
            width: 280px;
            height: 80px;
            margin-left: -20px;
        }
        .home-page .bonus .text {
            color: #fff;
            width:220px;
            font-size: 18px;
            font-weight: 700;
            background: -webkit-linear-gradient(var(--bonus-text-color-1), var(--bonus-text-color-2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .home-page .logo img{
            top:180px;
            
        }
    }
</style>