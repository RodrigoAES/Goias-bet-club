<script>
    export default {
        props:['data' ,'message', 'action'],
        data() {
            return {
                error:null,
            }
        },
        methods:{
            deleteUser:async function(id) {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user/delete/${id}`, {
                    method:'DELETE', //DELETE
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.users.splice(this.$parent.users.indexOf(this.data), 1);

                    this.$parent.accountSuccessData = this.data;
                    this.$parent.accoutSuccessMessage = 'Conta excluida.';

                    this.$parent.accountConfirmOpened = false;   
                    this.$parent.accountSuccessOpened = true;                 

                } else if(json.status === 'error') {
                    this.error = json.error;
                }
            }, 
            switchStatus:async function(id) {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/account/status/${id}`, {
                    method:'PUT', //PUT
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    console.log('oi')
                    this.$parent.users[this.$parent.users.indexOf(this.data)].active = json.active;

                    this.$parent.accountSuccessData = this.data;
                    this.$parent.accoutSuccessMessage = `Conta ${json.active ? 'ativada' : 'deasativada'}.`;

                    this.$parent.accountConfirmOpened = false;   
                    this.$parent.accountSuccessOpened = true;

                } else if(json.status === 'error') {
                    console.log(json);
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area-users">
        <div class="confirm">
            <div class="message">{{message}}</div>

            <div class="info name"><span>Nome:</span>{{data.name}}</div>
            <div class="info phone"><span>Email:</span>{{data.email}}</div>

            <div
                v-if="error" 
                class="danger"
            >{{error}}</div>

            <div class="buttons">
                <button 
                    v-if="action === 'delete'"
                    @click="deleteUser(data.id)"
                >Excluir</button>

                <button 
                    v-if="action === 'status'"
                    @click="switchStatus(data.id)"
                    id="status"
                >{{data.active ? 'Desativar' : 'Ativar'}}</button>

                <button @click="this.$parent.accountConfirmOpened = false">Cancelar</button>
            </div>
            
        </div>
    </div>
</template>

<style>
    #status {
        background-color: #006ed4;
        color:#fff;
    }
    @media(max-width:420px) {
        .confirm {
            margin:auto;
        }
    }
</style>