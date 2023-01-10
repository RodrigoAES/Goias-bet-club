<script>
    export default {
        data() {
            return {
                name:null,
                errors:{
                    name:null,
                },
            }
        }, 
        methods:{
            createChamp:async function() {
                let body = new FormData();
                body.append('name', this.name ? this.name : '');

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championship`, {
                    method:'POST',
                    body:body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.championships.push(json.championship);
                    this.$parent.champFormOpened = false;

                } else if(json.status === 'error') {
                    if(json.error.name) {
                        this.errors.name = json.error.name
                    }
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div id="championship-form">
            <div class="close"><span @click="(this.$parent.champFormOpened = false)">✖</span></div>
            <label for="name">Nome do campeonato:</label>
            <div v-if="errors.name" v-for="error in errors.name" class="danger">{{error}}</div>
            <input v-model="name" type="text" name="name">
            <button @click="createChamp">Criar</button>
        </div>
        
    </div>
</template>

<style>
    .modal-area {
        top:0;
        left:0;
        width:100vw;
        height: 100vh;
        margin-top: 0;
        margin-left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #championship-form {
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
        border:2px solid var(--p-color);
        border-radius: 10px;
    }
    #championship-form .close {
        position: relative;
        top:-15px;
        left:50%;
    }
    #championship-form label {
        font-weight: 600;
        margin-bottom: 5px;
        width:100%;
        display: flex;
        justify-content: flex-start;
    }
    #championship-form input {
        width:300px;
        font-size: 16px;
        font-weight: 600;
        padding: 10px;
        color: #555;
        border:1px solid #888;
        border-radius: 4px;
        outline: none;
        margin-bottom: 20px;
    }
    #championship-form button {
        padding: 10px 20px;
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
    #championship-form button:hover {
        background-color: var(--p-color-h);
    }
</style>