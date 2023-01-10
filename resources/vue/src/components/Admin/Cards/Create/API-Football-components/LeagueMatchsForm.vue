<script>
    export default  {
        props:['league', 'filter', 'season'],
        data() {
            return {
                method:'between-dates',

                fromDate:null,
                toDate:null,

                round:null,

                error:null,
            }
        },
        watch: {
            method(value) {
                if(value === 'between-dates') {
                    this.round = null;

                } else if(value === 'round') {
                    this.fromDate = null;
                    this.toDate = null;
                }
            }
        },
        methods: {
            requestLeagueMatchs:async function() {
                if(
                    this.fromDate && this.toDate 
                    || this.round 
                    || this.method ===  'next:20'
                    || this.method === 'next:30'
                    || this.method === 'next:50'
                ) {
                    let url = `admin/api-football/league-matchs/${this.league}/`;

                    if(!this.fromDate && !this.toDate) {
                        url += `${this.filter === 'current' ? '1' : this.filter === 'all' ? '0' : null}/`;
                    } else {
                        url += '0/';
                    }
                    
                    url += `${this.season ? this.season : '0'}/`;
                    url += `${this.fromDate && this.toDate ? `${this.fromDate}|${this.toDate}` : '0'}/`;
                    url += `${this.method === 'next:20' ? '20' : this.method === 'next:30' ? '30' : this.method === 'next:50' ? '50' : '0'}/`;
                    url += `${this.round ? this.round : '0'}`

                    let request = await fetch(`${import.meta.env.VITE_BASE_URL}${url}`, {
                        method:'GET',
                        headers:{
                            'Authorization': `Bearer ${localStorage.getItem('auth')}`
                        }
                    });

                    let json = await request.json();

                    if(json.status === 'success') {
                        this.$parent.matchs = json.matchs;
                        this.$parent.LeagueMatchsFormOpened = false;
                    }
                } else {
                    this.error = 'É necessario escolher um mode de pesquisa (Entre datas, por rodada ou próximas X partidas) e preencher seus devidos campos.';
                }
                
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div id="league-season-matchs-form">
            <div @click="this.$parent.LeagueMatchsFormOpened = false" class="close">✖</div>
            <div class="title">Pesquisar partidas</div>

            <div class="method-select">
                Modo de pesquisa:
                <select v-model="method">
                    <option value="between-dates">Entre datas</option>
                    <option value="round">Por rodada</option>
                    <option value="next:20">Próximas 20 partidas</option>
                    <option value="next:30">Próximas 30 partidas</option>
                    <option value="next:50">Próximas 50 partidas</option>
                </select>
            </div>

            <div class="match-form-api">
                <div style="margin:10px" v-if="error" class="danger">{{error}}</div>

                <label v-if="method === 'between-dates'">
                    De:
                    <input v-model="fromDate" type="date">
                    Até:
                    <input v-model="toDate" type="date">
                </label>

                <label v-if="method === 'round'">
                    Rodada:
                    <input v-model="round" type="number">
                </label>

                <label v-if="method === 'next:20'">
                    Próximas 20 partidas.
                </label>

                <label v-if="method === 'next:30'">
                    Próximas 30 partidas.
                </label>

                <label v-if="method === 'next:50'">
                    Próximas 50 partidas.
                </label>

                <div class="actions">
                    <button @click="requestLeagueMatchs">Pesquisar Partidas</button>
                </div>
               
            </div>
        </div>
    </div>
    
</template>

<style>
    #league-season-matchs-form {
        background-color: #fff;
        padding:20px;
        display:flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        border:4px solid var(--p-color);
        border-radius: 10px;
        width:350px;
    }
    #league-season-matchs-form .close {
        position: relative;
        top:-10px;
        left: 95%;
    }
    #league-season-matchs-form .title {
        width:100%;
    }
    #league-season-matchs-form .method-select {
        margin-bottom: 20px;
        font-weight: 600;
    }
    #league-season-matchs-form .method-select select {
        padding:4px;
        font-size: 14px;
        border:1px solid #888;
        border-radius: 5px;
        outline: none;
    }
    #league-season-matchs-form .match-form-api {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    #league-season-matchs-form .match-form-api label {
       font-weight: 600;
    }
    #league-season-matchs-form .match-form-api label input {
        font-size: 14px;
        padding: 4px;
        border:1px solid #888;
        border-radius: 5px;
        outline: none;
    } 
    #league-season-matchs-form .match-form-api input[type=number] {
        width:50px;
    }
    #league-season-matchs-form .match-form-api .actions {
        width:100%;
        display: flex;
        justify-content: center;
    } 
    #league-season-matchs-form .match-form-api button {
        padding:10px 20px;
        margin-top: 40px;
        background-color: var(--p-color);
        color: var(--s-color);
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
    #league-season-matchs-form .match-form-api button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
</style>