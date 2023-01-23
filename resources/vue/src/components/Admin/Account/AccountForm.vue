<script>
    export default {
        props:['user', 'method'],
        data() {
            return {
                name:this.method === 'edit' ? this.user.name : null,
                email:this.method === 'edit' ? this.user.email : null,
                phone:this.method === 'edit' ? this.user.phone : null,
                level:this.method === 'edit' ? this.user.level : 'attendant',
                slug:this.method === 'edit' ? this.user.slug : null,

                paymentPermission:false,
                doubtPermission:false,
                validatePermission:false,

                password:'',
                password_confirmation:'',
                current_password:'',
                errors: {
                    auth:null,
                    name:null,
                    email:null,
                    phone:null,
                    password:null,
                    current_password:null,
                },
            }
        },
        computed:{
            slugModel:function() {
                return this.slug ? this.slug.split(' ').join('-') : '';
            }
        },
        methods:{
            phoneMask:function(e) {
                console.log(e);
                if(! Number.isInteger(parseInt(e.data))) {
                    e.target.value = e.target.value.replace(e.data, '');
                    this.phone = e.target.value;
                }
                if(e.target.value.length > 11) {
                    e.target.value = e.target.value.substring(0, 11);
                    this.phone = e.target.value;
                }
            },
            createUser: async function(){
                let body = new FormData();
                
                body.append('name', this.name);
                body.append('email', this.email);
                body.append('phone', this.phone);
                body.append('level', this.level);
                body.append('slug', this.slug);

                body.append('payment_permission', this.paymentPermission ? 1 : 0);
                body.append('doubt_permission', this.doubtPermission ? 1 : 0);
                body.append('validate_permission', this.validatePermission ? 1 : 0);

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
                    if(json.error.phone) {
                        this.errors.phone = json.error.phone;
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
                if(this.phone) {
                    body.append('phone', this.phone);
                }
                if(this.$root.loggedUser.level === 'admin') {
                    body.append('level', this.level);
                }
                if(this.slug) {
                    body.append('slug', this.slug);
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

            <div class="level">
                <label for="level">Nivel da conta:</label>
                <select v-model="level" name="level" id="level">
                    <option value="sub-admin">Co-Administrador</option>
                    <option value="attendant">Atendente/Vendedor</option>
                </select>
            </div>

            <label for="name">Nome:</label>
            <div v-if="errors.name" v-for="error in errors.name" class="danger">{{error}}</div>
            <input 
                v-model="name" 
                :style="errors.name ? 'border-color:rgb(221, 37, 37)' : null"
                type="text" 
                name="name" 
            />
            
            <label for="phone">Celular:</label>
            <div v-if="errors.phone" v-for="error in errors.phone" class="danger">{{error}}</div>
            <input 
                v-model="phone" 
                @input="phoneMask"
                :style="errors.phone ? 'border-color:rgb(221, 37, 37)' : null"
                type="tel" 
                name="phone"

            />

            <label for="email">Email:</label>
            <div v-if="errors.email" v-for="error in errors.email" class="danger">{{error}}</div>
            <input 
                v-model="email" 
                :style="errors.email ? 'border-color:rgb(221, 37, 37)' : null"
                type="email" 
                name="email" 
            />

            <label for="url-signature">Assinatura da URL:</label>
            <input v-model="slug" id="url-siganture" type="text">
            <div class="url">www.goiasbetclub.com/{{slugModel}}</div>

            
            <div v-if="method === 'create'" class="permissions">
                <label>Permissões:</label>

                <div class="payment">
                    <label class="tiny-label" for="payment">Receber pagamentos:</label>
                    <input v-model="paymentPermission" type="checkbox" name="payment_permission" id="payment_permission" />
                </div>

                <div class="doubt">
                    <label class="tiny-label" for="doubt">Tirar dúvidas:</label>
                    <input v-model="doubtPermission" type="checkbox" name="doubt_permission" id="doubt_permission" />
                </div>   
               
                <div class="validate">
                    <label class="tiny-label" for="validate">Válidar cartelas:</label>
                    <input v-model="validatePermission" type="checkbox" name="validate_permission" id="validate_permission" />
                </div> 
            </div>
    
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
        align-items: flex-start;
        overflow-y: scroll;
    }
    .form-account {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border:4px solid var(--p-color);
        padding: 30px 40px;
        width:400px;
        background-color: #fff;
        margin: 30px 0px;
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
        background-color: var(--p-color);
        color: var(--s-color);
        cursor: pointer;
    }
    .form-account .buttons button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
    .form-account .level {
        display:flex;
        align-items: center;
        justify-content: flex-start;
        width:100%;
    }
    .form-account .level label {
        width:auto;
        margin: 0;
        margin-right: 10px;

    }
    .form-account .level select {
        padding:4px;
        outline: none;
        border:1px solid #888;
        border-radius: 4px;
    }

    .form-account .permissions {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width:100%
    }
    .form-account .permissions .payment,
    .form-account .permissions .doubt,
    .form-account .permissions .validate {
        display: flex;
        align-items: center;
        width:100%;
    }
    .form-account .tiny-label {
        width:auto;
        font-weight: 500;
        margin: 0;
        height: 25px;
        display: flex;
        align-items: center;
    }
    .form-account input[type=checkbox] {
        width:16px;
        height:16px;
        margin-left: 10px;
    }

    .form-account .url {
        font-size: 12px;
        width:100%
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