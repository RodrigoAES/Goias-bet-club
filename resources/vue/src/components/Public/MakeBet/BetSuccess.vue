<script>
    import Loading from '../../Loading.vue';

    export default {
        props:['response'],
        data() {
            return {
                cardCode:null,

                qrcode:null,
                qrcodeImage:null,
                qrcodeError:null,
                qrcodeLoading:false,
                
            }
        },
        computed: {
            date: function () {
                let date = new Date(this.response.created_at);
                return `${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
                
            },
            paymentAttendants:function() {
                return this.$root.attendants.filter(attendant => attendant.payment_permission ? true : false);
            },
        },
        methods: {
            openPayment:function() {
                let payment = document.querySelector('#payment2');
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
                let att = document.querySelector('#attendants2');
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
                let autopay = document.querySelector('#autopay2');
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
            goWathsappPayment:async function(attendant) {
                let body = new FormData();
                body.append('attendant_id', attendant.id);
                body.append('attendant_name', attendant.name);
                body.append('client_name', this.name && this.name.trim() != '' ? this.name : '');
                body.append('client_phone', this.phone && this.phone.trim() != '' ? this.phone : '');
                body.append('user_card_code', this.code && this.code.trim() != '' ? this.code : '');
                body.append('type', 'payment');

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}attendance`, {
                    method:'POST',
                    body: body,
                });
                let json = await request.json();
                
                if(json.status === 'success') {
                    let url = `http://api.whatsapp.com/send?1=pt_BR&phone=55${attendant.phone}`;
                    url += `&text=Olá ${attendant.name} `;
                    url += this.response.name ? `meu nome é ${this.response.name}, ` : ''
                    url += this.response.code ? `o código da minha cartela é ${this.response.code} e ` : '';
                    url += 'e estou aqui para efetuar o pagamento.';

                    window.open(url, '_blank');
                }  
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
            },
            copyQrcode:function() {
                navigator.clipboard.writeText(this.qrcode);
                document.querySelector('#payment2 #autopay2 .qrcode-image').style.marginBottom = '0px';
                document.querySelector('#copy-message2').style.display = 'block';
            },
            copyCardCode:function() {
                navigator.clipboard.writeText(this.response.code);
            },
        },
        components: {
            Loading
        }
    }
</script>

<template>
    <div class="modal-area-bet">
        <div class="success-info">
            <div class="title-success">Sucesso!</div>
            <div class="message">Guarde o código da cartela para consultar!</div>
            <div class="close"><span @click="this.$parent.closeSuccess()">✖</span></div>
            <div class="info code">
                <span>Código da cartela:</span>
                {{response.code}}
                <button class='copy-code' @click="copyCardCode">
                    Copiar
                    <img src="/assets/icons/copy-black.png" >
                </button>
            </div>
            <div class="info name"><span>Nome:</span>{{response.name}}</div>
            <div class="info phone"><span>Celular:</span>{{response.phone}}</div>
            <div class="info created_at"><span>Data:</span>{{date}}</div>
            <div class="buttons-success">
                <button 
                    @click="this.$parent.toUserCard(response.code); 
                    this.$parent.betSuccessOpened = false"
                >
                    Ver Cartela
                </button>
                
                <div id="payment2" style="height:42px">
                    <div @click="openPayment" class="label">Efetuar pagamento</div>

                    <div v-if="paymentAttendants.length > 0" id="attendants2" style="height:44px">
                        <div @click="openAttendantPayment" class="label-attendants">Pagar com atendente</div>

                        <button
                            v-for="attendant in paymentAttendants"
                            @click="goWathsappPayment(attendant)"
                        >
                            {{ `${attendant.name.split(' ')[0]} ${attendant.name.split(' ')[1] ? attendant.name.split(' ')[1].charAt(0)+'.' : ''}` }}
                            <img :src="`${this.$root.asset}assets/icons/whatsapp.png`" />
                        </button>
                    </div>

                    <div id="autopay2" style="height:44px">
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
                                <span id="copy-message2" style="display:none;">Copiado!</span>
                                <button @click="copyQrcode">
                                    Copiar
                                    <img src="/assets/icons/copy-black.png" >
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .success-info {
        background-color: #fff;
        padding: 30px;
        border: 4px solid #069446;
        border-radius: 20px;
        position: relative;
        margin-bottom: 200px;
    }
    .info {
        margin-bottom: 20px;
        font-weight: 700;
    }
    .info span {
        color: #069446;
        margin-right: 10px;
    }
    .title-success {
        color: #069446;
        font-size: 26px;
        font-weight: 700;
        width: 100%;    
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    .close {
        position: absolute;
        left:90%;
        top:4%;
        font-size: 22px;
        font-weight: 700;
        cursor: pointer;
    }
    .buttons-success {
        width:100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        margin-top: 15px;
    }
    .buttons-success button{
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: #069446;
        color:rgb(255, 238, 0);
        border:none;
        border-radius: 5px;
        margin: auto;
        margin-bottom: 10px;
    }
    .message {
        margin-bottom: 6px;
    }
    .code {
        display: flex;
        align-items: center;
    }
    .code button {
        display: flex;
        align-items: center;
        background-color: #fff;
        border:1px solid #000;
        border-radius: 3px;
        margin-left: 10px;
        padding: 4px 5px;
        cursor: pointer;
    }
    .code button img {
        width: 20px;
    }
    .buttons-success a {
        display: flex;
        align-items: center;
        padding: 4px 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: #069446;
        color:rgb(255, 238, 0);
        border:none;
        border-radius: 5px;
        margin: auto;
        text-decoration: none;
    }
    .buttons-success a img {
        width:30px;
        margin-left: 5px;
    }

    #payment2 {
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
    #payment2 .label {
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
    #payment2 #attendants2 {
        display:flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        overflow-y: hidden;
        transition: all ease 1s;
        height: 42px;
    }
    #payment2 #autopay2 {
        display:flex;
        flex-direction: column;
        align-items: center;
        overflow-y: hidden;
        transition: all ease 1s;
    }
    #payment2 #autopay2 .code {
        display:flex;
        flex-direction: column;
        align-items: center;
    }
    #payment2 #autopay2 label {
        font-weight: 600;
    }
    #payment2 #autopay2 input {
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
    #payment2 #autopay2 button {
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
    #payment2 #autopay2 button img {
        width:30px;
        margin-left: 10px;
    }
    #payment2 #autopay2 #qrcode {
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all ease 1s;
    }
    #payment2 #autopay2 .qrcode-image {
        margin:20px 0px
    }
    #payment2 #autopay2 .qrcode-code {
        height:100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    #payment2 #autopay2 .qrcode-code button {
        width:auto;
        padding: 10px 20px;
        background-color: transparent;
        color:#000;
        border: 2px solid #000;
        margin-top: 0;
    }
    #payment2 .label-attendants,
    #payment2 .label-autopay {
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
    #payment2 #attendants2 button {
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
    #payment2 #attendants2 button:hover {
        background-color: var(--p-color-h);
    }
    #payment2 #attendants2 button img {
        width:20px
    }
</style>