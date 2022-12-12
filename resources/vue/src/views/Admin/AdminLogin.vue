<script>
    export default {
        data() {
            return {
                name:'',
                user:'',
                password:'',
                errors: {
                    email:null,
                    phone:null,
                    password:null,
                    auth:null,
                }
            }
        },
        methods:{
            login: async function() {
                this.errors = {
                    user:null,
                    password:null,
                    auth:null,
                }

                let body = new FormData();
                body.append('user', this.user);
                body.append('password', this.password);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/auth/login`, {
                    method:'POST',
                    body: body
                });
                let response = await request.json();

                if(response.status === 'success') {
                    this.$root.loggedUser = response.user;
                    localStorage.setItem('auth', response.token)

                    this.$router.push({name:'AdminPanel'});

                } else if(response.status === 'error') {
                    if(response.error.user) {
                        this.errors.user = response.error.user;
                    }
                    if(response.error.password) {
                        this.errors.password
                    }
                    if(typeof response.error === 'string') {
                        this.errors.auth = response.error;
                    }
                }

                
            }
        }
    }
</script>

<template>
    <div id="admin-login">
        <div class="form login">
            <h1>Login</h1>

            <div v-if="errors.auth" class="danger">{{errors.auth}}</div>

            <label >
                Celular ou Email
                <div v-if="(errors.user)" v-for="error in errors.user" class="danger">{{error}}</div>
                <input 
                    v-model="user" 
                    :style="errors.user ? 'border-color:rgb(221, 37, 37)' : null"
                    type="text" 
                />
            </label>
            
            <label>
                Senha
                <div v-if="(errors.password)" v-for="error in errors.password" class="danger">{{error}}</div>
                <input 
                    v-model="password" 
                    :style="errors.email ? 'border-color:rgb(221, 37, 37)' : null"
                    type="password" 
                />
            </label>

            <button @click="login" >Entrar</button>
            
        </div>
    </div>
</template>

<style>
    #admin-login {
        height: calc(100vh - 70px);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .form {
        
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border: 3px solid #069446;
        border-radius: 6px;
        padding: 20px;
    }
    .form.login {
        width: 450px;
        height:350px;
    }
    .form h1 {
        margin: 0;
        margin-bottom: 20px;
    }
    .form label{
        display: flex;
        flex-direction: column;
        font-weight: 600;
        width:80%;
    }
    .form input {
        width: 100%;
        margin-bottom: 20px;
        outline: none;
        border: 1px solid #555;
        border-radius: 5px;
        padding: 10px 10px;
        font-size: 16px;
        color:#333;
    }
    .form button {
        border:none;
        padding:15px 25px;
        font-size: 16px;
        font-weight: 600;
        margin-top: 20px;
        background-color: #069446;
        color:rgb(255, 238, 0);
        border-radius: 6px;
        cursor: pointer;
    }
    .form button:hover {
        background-color: #09a750;
        transform: scale(1.1);
    }

    @media (max-width:420px) {
        .form.login {
            width:calc(100vw - 80px);
            height: 300px;
            margin-left: -10px;
            padding: 20px 10px;
        }
        .form.login label {
            width:90%;
        }
        .form.login input {
            width:inherit
        }
    }
</style>