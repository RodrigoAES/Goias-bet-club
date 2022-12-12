<script>
    export default {
        props:['teams', 'championship', 'data', 'method', 'match'],
        data() {
            return {
                homeTeam: this.method === 'edit' ? this.data.homeTeam : null,
                awayTeam: this.method === 'edit' ? this.data.awayTeam : null,
                homeScore: this.method === 'edit' ? this.data.homeScore : 0,
                awayScore: this.method === 'edit' ? this.data.awayScore : 0,
                group: this.method === 'edit' ? this.data.group : null,
                finished: this.method === 'edit' ? this.data.finished : false,
                datetime: this.method === 'edit' ? this.data.datetime : null,

                selectHomeTeam:true,
                selectHomeTeamOpened:false,
                selectAwayTeam:true,
                selectAwayTeamOpened:false,
                
                errors: {
                    group:null,
                    finished:null,
                    datetime:null,
                    home_score:null,
                    away_score:null,
                    home_team_id:null,
                    away_team_id:null,
                }
            }
        }, 
        methods:{
            closeTeamsSelect:function() {
                this.selectHomeTeamOpened = false;
                this.selectAwayTeamOpened = false;
            }, 
            createMatch:async function() {
                let body = new FormData();
                body.append('championship_id', this.championship);
                body.append('group', this.group ? this.group : '');
                body.append('finished', this.finished ? '1' : '0');
                body.append('datetime', this.datetime ? this.datetime : ''),
                body.append('home_score', this.homeScore);
                body.append('away_score', this.awayScore);
                body.append('home_team_id', this.homeTeam ? this.homeTeam.id : '');
                body.append('away_team_id', this.awayTeam ? this.awayTeam.id : '');
                
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/match`, {
                    method:'POST',
                    body: body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.matchs.push(json.match);

                    this.$parent.matchFormOpened = false;

                    this.errors = {
                        group:null,
                        finished:null,
                        datetime:null,
                        home_score:null,
                        away_score:null,
                        home_team_id:null,
                        away_team_id:null,
                    }

                } else if(json.status === 'error') {
                    if(json.error.group) {
                        this.errors.group = json.error.group
                    }
                    if(json.error.finished) {
                        this.errors.finished = json.error.finished
                    }
                    if(json.error.datetime) {
                        this.errors.datetime = json.error.datetime
                    }
                    if(json.error.home_score) {
                        this.errors.home_score = json.error.home_score
                    }
                    if(json.error.away_score) {
                        this.errors.away_score = json.error.away_score
                    }
                    if(json.error.home_team_id) {
                        this.errors.home_team_id = json.error.home_team_id
                    }
                    if(json.error.away_team_id) {
                        this.errors.away_team_id = json.error.away_team_id
                    }
                }
            },
            updateMatch:async function() {
                let body = new FormData();
                body.append('group', this.group);
                body.append('finished', this.finished ? '1' : '0');
                body.append('datetime', this.datetime),
                body.append('home_score', this.homeScore);
                body.append('away_score', this.awayScore);
                body.append('home_team_id', this.homeTeam.id);
                body.append('away_team_id', this.awayTeam.id);

                body = new URLSearchParams(body);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/match/${this.data.id}`, {
                    method:'PUT',
                    body: body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    json.match.home_score = parseInt(json.match.home_score);
                    json.match.home_score = parseInt(json.match.home_score);
                    this.$parent.matchs[this.$parent.matchs.indexOf(this.match)] = json.match;

                    this.errors = {
                        group:null,
                        finished:null,
                        datetime:null,
                        home_score:null,
                        away_score:null,
                    }

                    this.$parent.matchFormOpened = false;

                } else if(json.status === 'error') {
                    if(json.error.group) {
                        this.errors.group = json.error.group
                    }
                    if(json.error.finished) {
                        this.errors.finished = json.error.finished
                    }
                    if(json.error.datetime) {
                        this.errors.datetime = json.error.datetime
                    }
                    if(json.error.home_score) {
                        this.errors.home_score = json.error.home_score
                    }
                    if(json.error.away_score) {
                        this.errors.away_score = json.error.away_score
                    }
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area" @click.self="closeTeamsSelect">
        <div class="match-form" @click.self="closeTeamsSelect">
            <div class="close"><span @click="(this.$parent.matchFormOpened = false)">✖</span></div>
            <div class="match" @click.self="closeTeamsSelect">
                <div class="home-away"><span>Casa</span><span>Visitante</span></div>
                <div class="info" >
                    <div class="team home" @click.self="closeTeamsSelect">
                        <div class="team-name" @click="selectHomeTeamOpened = true">{{homeTeam ? homeTeam.name : ''}}</div>

                        <div v-if="! homeTeam" class="select">
                            <div v-if="selectHomeTeam" @click="selectHomeTeamOpened = true">Selecionar <img :src="`${this.$root.asset}assets/icons/down-arrow.png`"></div>
                        </div>

                        <div v-if="selectHomeTeamOpened" class="select-team">
                            <div 
                                v-for="team in teams" 
                                @click="homeTeam = team; selectHomeTeamOpened = false" 
                                class="t"
                            >
                                <div class="flag">
                                    <img :src="team.flag">
                                </div>
                                <div class="name">{{team.name}}</div>
                            </div>
                        </div>

                        <div class="flag" @click="selectHomeTeamOpened = true">
                            <img v-if="homeTeam" :src="homeTeam ? homeTeam.flag : ''" alt="">
                        </div>

                        <div class="score home">
                            <input 
                                v-model="homeScore" 
                                type="number"
                                class="score"
                            />
                        </div>
                    </div>
                    
                    <div class="x">X</div>
    
                    <div class="team away">
                        <div class="score away">
                            <input 
                                v-model="awayScore" 
                                type="number"
                                class="score"
                            />
                        </div>

                        <div class="flag" @click="selectAwayTeamOpened = true">
                            <img v-if="awayTeam" :src="awayTeam ? awayTeam.flag : ''" alt="">
                        </div>

                        <div v-if="! awayTeam" class="select">
                            <div v-if="selectAwayTeam" @click="selectAwayTeamOpened = true">Selecionar <img :src="`${this.$root.asset}assets/icons/down-arrow.png`"></div>
                        </div>
                        <div v-if="selectAwayTeamOpened" class="select-team">
                            <div 
                                v-for="team in teams" 
                                @click="awayTeam = team; selectAwayTeamOpened = false" 
                                class="t"
                            >
                                <div class="flag">
                                    <img :src="team.flag">
                                </div>
                                <div class="name">{{team.name}}</div>
                            </div>
                        </div>
                        <div class="team-name" @click="selectAwayTeamOpened = true">{{awayTeam ? awayTeam.name : ''}}</div>
                    </div>
                    
                </div>
                <div class="date-group-info">
                    <div class="group">
                        Grupo: <input v-model="group" type="text" class="group-input">
                    </div>
                    <div class="finished">Terminada: <input v-model="finished" type="checkbox" class="finished"></div>
                    <div class="datetime">Data: <input v-model="datetime" type="datetime-local" class="datetime"></div>
                </div>  
            </div>

            <div class="danger" 
                v-if="(
                    errors.group
                    || errors.finished
                    || errors.datetime
                    || errors.home_score
                    || errors.away_score
                    || errors.home_team_id
                    || errors.away_team_id
                )"
            >
                <span 
                    v-if="errors.group"
                    v-for="error in errors.group" 
                >
                    {{error}}
                </span>
                <span 
                    v-if="errors.finished"
                    v-for="error in errors.finished" 
                >
                    {{error}}
                </span>
                <span 
                    v-if="errors.datetime"
                    v-for="error in errors.datetime" 
                >
                    {{error}}
                </span>
                <span 
                    v-if="errors.home_score"
                    v-for="error in errors.home_score" 
                >
                    {{error}}
                </span>
                <span 
                    v-if="errors.away_score"
                    v-for="error in errors.away_score" 
                >
                    {{error}}
                </span>
                <span 
                    v-if="errors.home_team_id"
                    v-for="error in errors.home_team_id" 
                >
                    {{error}}
                </span>
                <span 
                    v-if="errors.away_team_id"
                    v-for="error in errors.away_team_id" 
                >
                    {{error}}
                </span>
            </div>

            <div class="buttons" @click.self="closeTeamsSelect">
                <button v-if="method === 'create'" @click="createMatch">Criar</button>
                <button v-if="method === 'edit'" @click="updateMatch">Atulizar</button>
            </div>
        </div>
    </div>
</template>

<style>
    .match-form {
        padding: 40px 40px 10px 40px;
        background-color: #fff;
        border:4px solid #069446;
        border-radius: 12px;
        position: relative;
    }
    .match-form .close { 
        top:0;
        left:95%
    }
    .match-form .team {
        width:214px;
    }
    .match-form .match {
        cursor: default;
    }
    .group input {
        width:40px
    }
    .match-form .select {
        cursor: pointer;
        width:180%
    }
    .match-form .select div {
        width:100%
    }
    .match-form .select img {
        width:15px;
        margin-bottom: -3px;
    }
    .match-form .team {
        position: relative;
    }
    .match-form .select-team {
        position: absolute;
        display: flex;
        flex-direction: column;
        width:150px;
        background-color: #fff;
        border:1px solid #000;
        padding: 5px;
        left: 22%;
        top: 80%;
        max-height: 92px;
        overflow-y: scroll;
    }
    .match-form .select-team .t {
        display: flex;
        padding: 5px 0px;
        cursor: pointer;
    }
    .match-form .select-team .t.away {
        justify-content: flex-start;
    }
    .match-form .select-team .t:hover {
        background-color: #ddd;
    }
    .match-form .select-team .flag {
        width:20px;
        height: 20px;
    }
    .match-form .select-team .flag img {
        object-fit: contain;
    }
    .match-form .flag {
        width:40px;
        height: 40px;
        border-radius: 50%;
    }
    .match-form .flag img{
        width:80%;
        height: 80%;
        object-fit: contain;
    }
    .match-form .home-away {
        width:100%;
        display: flex;
        justify-content: space-around;
        font-weight: 700;
    }
    .match-form input {
        outline:none;
        border:1px solid #888;
        color:#555;
        font-weight: 600;
    }
    .match-form input.score {
        width:28px;
        border-radius: 3px;
    }
    .match-form input.score::-webkit-inner-spin-button,
    .match-form input.score::-webkit-outer-spin-button {
        -webkit-appearance: none; 
        margin: 0; 
        display: none;
    }
    .match-form input.group-input {
        border-radius: 3px;
        padding:4px;
        font-size: 14px;
    }
    .match-form .date-group-info .finished {
        display: flex;
        align-items: center;
        margin-right: 5px;
    }
    .match-form input.finished {
        height:20px;
        width:20px;
    }
    .match-form input.datetime {
        font-size: 14px;
        padding: 3px;
        border-radius: 4px;
    }
    .match-form .buttons button {
        background-color: #069446;
        color:rgb(255, 238, 0);
        cursor:pointer;
        margin-bottom:none;
    }
    .match-form .buttons button:hover {
        background-color: #09a750;
        transform: scale(1.1);
    }  
    .match-form .danger {
        display: flex;
        flex-direction: column;
    } 

    @media(max-width:420px) {
        .match-form{
            padding: 20px 20px;

            width:80%;
        }
        .match-form .match {
            height: auto;
        }
        .match-form .home-away {
            display: none;
        }
        .match-form .info {
            display:flex;
            flex-direction: column;
            border:none;
            width:100%;
        }
        .match-form .team {
            display:flex;
            flex-direction: column;
            width:auto;
        }
        .match-form .date-group-info {
            display:flex;
            flex-direction: column;
            margin-left: -40px;
            margin-bottom: 0;
        }
        .match-form .date-group-info input {
            margin:5px
        }
        .match-form .match .team-name {
            width:200px;
            justify-content: center;
            margin: 0;
        }
        .match .home .team-name {
            justify-content: center;
        }
        .match-form .flag {
            width:80px;
            height: 80px;
        }
        .match-form .flag img{
            max-width: 80%;
            max-height: 80%;
            width:80%;
            height: 80%;
            object-fit: contain;
        }
        .match .home .score {
            margin-left: 0;
        }
        .match .away .score {
            margin-right: 0;
        }
        .match-form .close { 
            top:0;
            left:90%
        }
        .match-form .select {
            width:auto;
        }
        .match-form .select-team {
            position: static;
            
        }
        .match-form .buttons {
            display: flex;
            margin-top: 10px;
        }
        .match-form .buttons button {
            font-size: 16px;
        }
        .match-form .select-team {
            position: absolute;
            display: flex;
            flex-direction: column;
            width:150px;
            background-color: #fff;
            border:1px solid #000;
            padding: 5px;
            left: 22%;
            top: 20%;
            max-height: 92px;
            overflow-y: scroll;
        }

    }
    
</style>