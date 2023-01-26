<script>
    export default {
        props:['attendant', 'mode'],
        data() {
            return {
                name:null,
                phone:null,
                code:null,
            }
        },
        methods:{
            goWathsapp:async function() {
                let body = new FormData();
                body.append('attendant_id', this.attendant.id);
                body.append('attendant_name', this.attendant.name);
                body.append('client_name', this.name && this.name.trim() != '' ? this.name : '');
                body.append('client_phone', this.phone && this.phone.trim() != '' ? this.phone : '');
                body.append('user_card_code', this.code && this.code.trim() != '' ? this.code : '');
                body.append('type', 'doubt');

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}attendance`, {
                    method:'POST',
                    body: body,
                });
                let json = await request.json();
                
                if(json.status === 'success') {
                    let url = `http://api.whatsapp.com/send?1=pt_BR&phone=55${this.attendant.phone}`;
                    url += `&text=Olá ${this.attendant.name} `;
                    url += this.name ? `meu nome é ${this.name}, ` : ''
                    url += this.code ? `o código da minha cartela é ${this.code} e ` : '';
                    url += 'tenho uma dúvida sobre o goiasbetclub.com.';

                    window.open(url, '_blank');
                }  
            },
            goWathsappPayment:async function() {
                let body = new FormData();
                body.append('attendant_id', this.attendant.id);
                body.append('attendant_name', this.attendant.name);
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
                    let url = `http://api.whatsapp.com/send?1=pt_BR&phone=55${this.attendant.phone}`;
                    url += `&text=Olá ${this.attendant.name} `;
                    url += this.name ? `meu nome é ${this.name}, ` : ''
                    url += this.code ? `o código da minha cartela é ${this.code} e ` : '';
                    url += 'e estou aqui para efetuar o pagamento.';

                    window.open(url, '_blank');
                }  
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div id="doubt-form">
            <div class="close"><span @click="this.$parent.doubtContactFormOpened = false">✖</span></div>
            <div class="title">Para um melhor atendimento:</div>

            <label for="name">Nome (opcional):</label>
            <input v-model="name" name="name" id="name" type="text" />

            <label for="phone">Seu celular (opcional):</label>
            <input v-model="phone" name="phone" id="phone" type="tel">

            <label for="code">Código da sua cartela (opcional):</label>
            <input v-model="code" name="code" id="code" type="text" maxlength="6" >

            <button v-if="mode === 'doubt'" @click="goWathsapp">Prosseguir</button>
            <button v-if="mode === 'payment'" @click="goWathsappPayment">Prosseguir</button>
        </div>
    </div>
</template>

<style>
    #doubt-form {
        display: flex;
        flex-direction: column;
        background-color: #fff;
        padding:40px;
        border:4px solid var(--p-color);
        border-radius: 20px;
    }
    #doubt-form .close {
        position:relative;
        top:0;
        left:100%;
        margin-top: -30px;
        margin-bottom: 10px;
    }
    #doubt-form .title {
        margin-bottom: 20px;
    }
    #doubt-form label {
        font-size: 16px;
        font-weight: 600;
    }
    #doubt-form input {
        font-size: 16px;
        color:#555;
        padding: 8px 10px;
        margin-top: 5px;
        margin-bottom: 15px;
        outline: none;
        border: 1px solid #888;
        border-radius: 6px;
    }
    #doubt-form #phone {
        width:100px
    }
    #doubt-form #code {
        width:60px;
    }
    #doubt-form button {
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 5px;
        width:100%;
        margin-top: 15px;
    }
    #doubt-form button:hover {
        background-color: var(--p-color-h);
    }
</style>