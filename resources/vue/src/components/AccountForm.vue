<script>
    export default {
        props:['user', 'method'],
        data() {
            return {
                name:this.method === 'edit' ? this.user.name : null,
                email:this.method === 'edit' ? this.user.email : null,
                password:'',
                password_confirmation:'',
                current_password:'',
                errors: {
                    auth:null,
                    name:null,
                    email:null,
                    password:null,
                    current_password:null,
                },
            }
        },
        methods:{
            createUser: async function(){
                let body = new FormData();
                
                body.append('name', this.name);
                body.append('email', this.email);
                body.append('password', this.password);
                body.append('password_confirmation', this.password_confirmation);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user`, {
                    method:'POST',
                    body: body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();
                
                if(json.status === 'success') {
                    this.errors = {
                        name:null,
                        email:null,
                        password:null,
                    }

                    this.$parent.users.push(json.user);
                    
                    this.$parent.accountSuccessData = json.user;
                    this.$parent.accoutSuccessMessage = 'Novo usuário criado.'
                    this.$parent.accountFormOpened = false;
                    this.$parent.accountSuccessOpened = true;

                } else if(json.status === 'error') {
                    if(json.error.name) {
                        this.errors.name = json.error.name;
                    }
                    if(json.error.email) {
                        this.errors.email = json.error.email;
                    }
                    if(json.error.password) {
                        this.errors.password = json.error.password
                    }
                    if(json.error.current_password) {
                        this.errors.current_password = json.error.current_password;
                    }
                }
                
               
            },
            updateUser:async function(id) {
                let body = new FormData();
                if(this.name) {
                    body.append('name', this.name);
                }
                if(this.email) {
                    body.append('email', this.email);
                }
                if(this.password) {
                    body.append('password', this.password);
                    body.append('password_confirmation', this.password_confirmation);
                }
                body.append('current_password', this.current_password);

                body = new  URLSearchParams(body);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/account/update/${id}`, {
                    method:'PUT', //PUT
                    body: body,
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await  request.json();

                if(json.status === 'success') {
                    this.errors = {
                        auth:null,
                        name:null,
                        email:null,
                        password:null,
                        current_password:null,
                    }

                    let user = this.$parent.users.find(user => user.id == json.user.id);
                    this.$parent.users[this.$parent.users.indexOf(user)] = json.user;

                    this.$parent.accountSuccessData = json.user;
                    this.$parent.accoutSuccessMessage = 'Os dados foram atualizados.'
                    this.$parent.accountFormOpened = false;
                    this.$parent.accountSuccessOpened = true;

                } else if(json.status === 'error') {
                    if(typeof json.error === 'string') {
                        this.errors.auth = json.error;
                    }
                    if(json.error.name) {
                        this.errors.name = json.error.name;
                    }
                    if(json.error.email) {
                        this.errors.email = json.error.email;
                    }
                    if(json.error.password) {
                        this.errors.password = json.error.password
                    }
                    if(json.error.current_password) {
                        this.errors.current_password = json.error.current_password;
                    }
                    
                }
            }
        },
    }
</script>

<template>
    <div class="modal-area-users">
        <div class="form-account">
            <div 
                @click="this.$parent.accountFormOpened = false"
                class="close"
            >
                <span>✖</span>
            </div>

            <div class="title">Dados da conta</div>
            <div v-if="errors.auth" class="danger">{{errors.auth}}</div>
            <label for="name">Nome:</label>
            <div v-if="errors.name" v-for="error in errors.name" class="danger">{{error}}</div>
            <input 
                v-model="name" 
                :style="errors.name ? 'border-color:rgb(221, 37, 37)' : null"
                type="text" 
                name="name" 
            />
    
            <label for="email">Email:</label>
            <div v-if="errors.email" v-for="error in errors.email" class="danger">{{error}}</div>
            <input 
                v-model="email" 
                :style="errors.email ? 'border-color:rgb(221, 37, 37)' : null"
                type="email" 
                name="email" 
            />
    
            <label for="password">Nova senha:</label>
            <div v-if="errors.password" v-for="error in errors.password" class="danger">{{error}}</div>
            <input 
                v-model="password" 
                :style="errors.password ? 'border-color:rgb(221, 37, 37)' : null"
                type="password" 
                name="password" 
            />
    
            <label for="password_confirmation">Confirmação da nova senha:</label>
            <input 
                v-model="password_confirmation" 
                :style="errors.password ? 'border-color:rgb(221, 37, 37)' : null"
                type="password" 
                name="password_confirmation" 
            />
    
            <label v-if="method === 'edit' " for="current_password">Senha atual:</label>
            <div v-if="errors.current_password" v-for="error in errors.current_password" class="danger">{{error}}</div>
            <input 
                v-if="method === 'edit'"
                v-model="current_password" 
                :style="errors.current_password ? 'border-color:rgb(221, 37, 37)' : null"
                type="password" 
                name="current_password" 
            />
    
            <div class="buttons">
                <button v-if="method === 'edit'" @click="updateUser(user.id)" >Salvar</button>
                <button  v-if="method ==='create'" @click="createUser" >Registrar</button>
            </div>
        </div>
    </div>
    
</template>

<style>
    .danger {
        width:100%;
        display:flex;
        justify-content: flex-start;
        align-items: flex-start;
    } 
    .modal-area-users {
        position: fixed;
        width:100vw;
        height: 100vh;
        top: 0;
        left: 0%;
        background-color: rgba(0,0,0,0.4);
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .form-account {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border:4px solid #069446;
        padding: 30px 40px;
        width:400px;
        background-color: #fff;
    }
    .form-account .close {
        position: relative;
        top: 5%;
        left: 50%;
    }
    .form-account .title {
        margin-bottom: 25px;
    }
    .form-account label {
        width:100%;
        margin-bottom: 5px;
        margin-top: 15px;
        font-size: 16px;
        font-weight: 600;
    }
    .form-account input {
        width:100%;
        font-size: 15px;
        font-weight: 500;
        padding: 10px 10px;
        outline: none;
        border:1px solid #888;
        border-radius: 6px;
        color:#777
    }
    .form-account .buttons button {
        margin-top: 40px;
        background-color: #069446;
        color:rgb(255, 238, 0);
        cursor: pointer;
    }
    .form-account .buttons button:hover {
        background-color: #09a750;
        transform: scale(1.1);
    }

    @media (max-width:420px) {
        .form-account {
            padding: 20px 30px;
            width:280px;
            margin-left: -10px;
            margin-top: 20px;
        }
    }
</style>