<script>
    export default {
        props:['team', 'mode', 'championship'],
        data() {
            return {
                name: this.mode === 'edit' ? this.team.name : '',
                flag: this.mode === 'edit' ? this.team.flag : null,
                flagFile:null,
                errors: {
                    name:null,
                    flag:null,
                }
            }
        },
        methods: {
            selectFile:function() {
                document.querySelector('#file').click()
            },
            getFlag:function(e) {
                let file = e.target.files[0];
                this.flagFile = file;
                let reader = new FileReader();
                reader.onloadend = (event)=>{
                    this.flag = event.target.result;
                }
                reader.readAsDataURL(file);
            }, 
            createTeam:async function () {
                let body = new FormData();
                body.append('name', this.name);
                body.append('flag', this.flagFile);
                body.append('championship_id', this.championship);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/team`, {
                    method:'POST',
                    body:body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.teams.push(json.team);
                    this.$parent.teamFormOpened = false;

                } else if(json.status === 'error') {
                    if(json.error.name) {
                        this.errors.name = json.error.name;
                    }
                    if(json.error.flag) {
                        this.errors.flag = json.error.flag;
                    }
                }
            },
            updateTeam:async function () {
                let body = new FormData();
                body.append('name', this.name);
                if(this.flagFile){
                    body.append('flag', this.flagFile);
                }
                

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/team/${this.team.id}`, {
                    method:'POST',
                    body:body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.teams[this.$parent.teams.indexOf(this.team)] = json.team;
                    this.$parent.teamFormOpened = false;

                } else if(json.status === 'error') {
                    if(json.error.name) {
                        this.errors.name = json.error.name;
                    }
                    if(json.error.flag) {
                        this.errors.flag = json.error.flag;
                    }
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div id="team-form">
            <div class="close"><span @click="(this.$parent.teamFormOpened = false)">✖</span></div>
            <label for="name">Time:</label>
            <div v-if="errors.name" v-for="error in errors.name" class="danger">{{error}}</div>
            <input v-model="name" type="text" name="name">

            <input @change="getFlag" id="file" type="file" accept="image/png, image/jpeg">
            <div v-if="errors.flag" v-for="error in errors.flag" class="danger">{{error}}</div>
            <div @click="selectFile" class="flag">
                {{flag ? '' : 'Selecionar foto'}}
                <img v-if="flag" :src="flag" />
            </div>

            <button v-if="mode === 'create'" @click="createTeam">Criar</button>
            <button v-if="mode === 'edit'" @click="updateTeam">Salvar</button>
        </div>
    </div>
</template>

<style>
    #team-form{
        background-color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        border:4px solid var(--p-color);
        border-radius: 8px;
    }
    #team-form .close {
        position: relative;
        top:-15px;
        left:50%;
    }
    #team-form .flag {
        width:100px;
        height:100px;
        border-radius: 50%;
        text-align: center;
        font-weight: 600;
        color:#888;
        border:4px solid #aaa;
        display:flex;
        align-items: center;
        justify-content: center;
        cursor:pointer;
        margin-top: 20px;
        overflow: hidden;
    }
    #team-form .flag img {
        width:100%;
        height: 100%;
        object-fit: contain;
    }
    #team-form label {
        font-weight: 600;
    }
    #team-form input {
        font-size: 16px;
        padding: 10px;
        outline: none;
        border:1px solid #888;
        color:#555;
        border-radius: 4px;
    }
    #team-form #file {
        position:absolute;
        top:-400px;
    }
    #team-form button {
        padding: 7px 20px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 5px;
        margin-top:20px;
    }

    @media(max-width:420px) {
        #team-form {
            width: 75%;
        }
    }
</style>