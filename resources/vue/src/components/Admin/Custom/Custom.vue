<script>
    import ChampionshipForm from './Championship/ChampionshipForm.vue';
    import ChampionshipConfirm from './Championship/ChampionshipConfirm.vue';

    import CustomTeamForm from './Teams/CustomTeamForm.vue';
    import CustomTeamConfirm from './Teams/CustomTeamConfirm.vue';

    import CustomMatchForm from './Matchs/CustomMatchForm.vue';
    import CustomMatchConfirm from './Matchs/CustomMatchConfirm.vue';

    

    export default {
        data() {
            return {
                championships:[],
                currentChampionship:null,
                matchs:[],
                teams:[],

                champFormOpened: false,

                championshipConfirmOpened:false,
                championshipConfirmData:null,

                matchFormOpened: false,
                matchFormMethod: '',
                matchFormData:null,
                matchFormMatch:null,

                matchConfirmOpened: false,
                matchConfirmData: null,

                teamFormOpened: false,
                teamFormData:null,
                teamFormMode:null,

                teamConfirmOpened:false,
                teamConfirmData:null,
            }
        }, 
        computed:{
            dates:function () {
                let dates = [];
                this.matchs.forEach(match => {
                    let datetime = match.datetime.split(' ');
                    let date = datetime[0].split('-');
                    date = `${date[2]}/${date[1]}/${date[0]}`;
                    let time = datetime[1].split(':');
                    time = `${time[0]}:${time[1]}`;

                    dates.push(`${date} ${time}`);
                });

                return dates;
            }
        },
        methods:{
            openChampForm: function() {
                this.champFormOpened = true;
            },
            openChampConfirm: function() {
                this.championshipConfirmData = this.currentChampionship;
                this.championshipConfirmOpened = true;

            },
            openMatchForm: function(method, data) {
                this.matchFormMethod = method;
                this.matchFormData = !data ? null : {
                    id:data.id,
                    homeTeam: {
                        name:data.home_name,
                        flag:data.home_flag,
                    },
                    awayTeam: {
                        name:data.away_name,
                        flag:data.away_flag,
                    },
                    homeScore: data.home_score,
                    awayScore: data.away_score,
                    group: data.group,
                    finished: data.finished,
                    datetime: data.datetime
                };
                this.matchFormMatch = data;
                this.matchFormOpened = true;
            },
            openMatchConfirm: function(match) {
                this.matchConfirmData = match;
                this.matchConfirmOpened = true;
                setTimeout(()=>{
                    this.matchFormOpened = false
                }, 1);
            },
            openTeamForm: function(mode, data) {
                this.teamFormMode = mode;
                this.teamFormData = data;
                this.teamFormOpened = true;
            },
            openConfirm:function(team) {
                this.teamConfirmData = team;
                this.teamConfirmOpened = true;
            }, 
            refreshChampionships:async function () {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championships`, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.championships = json.championships;
                    this.currentChampionship = this.championships.length > 0 ? this.championships[0].id : 1;
                }
                
            }
        },
        components: {
            ChampionshipForm,
            ChampionshipConfirm,

            CustomTeamForm,
            CustomTeamConfirm,

            CustomMatchForm,
            CustomMatchConfirm,
        }, 
        watch:{
            async currentChampionship(value, oldValue) {
                // Current championship teams
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/teams/${value}`, {
                method:'GET',
                headers:{
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.teams = json.teams;
                }

                // Current championship matchs
                request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championship/${value}`, {
                method:'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
                });

                json = await request.json();  

                if(json.status === 'success') {
                    this.matchs = json.matchs;
                }
            },
            championships(value) {
                if(value.length === 0) {
                    this.matchs = [];
                    this.teams = [];
                }
            }
        },
        async mounted() {
            // Championships
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championships`, {
                method:'GET',
                headers:{
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
            });

            let json = await request.json();

            if(json.status === 'success') {
                this.championships = json.championships;
                this.currentChampionship = json.championships[0].id;
            }

            // Current Champioship teams
            request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/teams/${this.currentChampionship}`, {
                method:'GET',
                headers:{
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
            });

            json = await request.json();

            if(json.status === 'success') {
                this.teams = json.teams;
            }

            // Current championship matchs
            request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championship/${this.currentChampionship}`, {
                method:'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
            });

            json = await request.json();  

            if(json.status === 'success') {
                this.matchs = json.matchs;
            }
        }
    }
</script>

<template>
    <div id="custom">
        <ChampionshipForm v-if="champFormOpened" />
        <ChampionshipConfirm 
            v-if="championshipConfirmOpened" 
            :championship="championshipConfirmData"
         />

        <CustomTeamForm 
            v-if="teamFormOpened" 
            :mode="teamFormMode" 
            :team="teamFormData" 
            :championship="currentChampionship"
        />
        <CustomTeamConfirm 
            v-if="teamConfirmOpened" 
            :team="teamConfirmData"
        />

        <CustomMatchForm 
            v-if="matchFormOpened" 
            :teams="teams" 
            :championship="currentChampionship"
            :method="matchFormMethod"
            :data="matchFormData"
            :match="matchFormMatch"
        />
        <CustomMatchConfirm 
            v-if="matchConfirmOpened"
            :match="matchConfirmData"
        />


        <div class="championship">
            <div class="championship-select">
                Campeonato:
                <select 
                    v-if="championships"
                    v-model="currentChampionship"
                >
                    <option 
                        v-for="champ in championships" 
                        :value="champ.id"
                    >
                        {{champ.name}}
                    </option>
                </select>
                <button @click="openChampForm">Novo+</button>
                <button class='delete' @click="openChampConfirm">Excluir</button>
            </div>
    
            <div class="matchs">
                <div v-if="(matchs.length > 0)" v-for="(match, index) in matchs" class="match">
    
                    <div class="info" @click="openMatchForm('edit', match)">
                        <div class="team home">
                            <div class="team-name">{{match.home_name}}</div>
                            <div class="flag">
                                <img :src="match.home_flag" alt="">
                            </div>
                            <div class="score home">{{match.home_score}}</div>
                        </div>
                        
                        
                        <div class="x">X</div>
    
                        <div class="team away">
                            
                            <div class="score away">{{match.away_score}}</div>
                            <div class="flag">
                                <img :src="match.away_flag" alt="">
                            </div>
                            <div class="team-name">{{match.away_name}}</div>
                        </div>
                        <div @click="openMatchConfirm(match)" class="remove"><span>✖</span></div>
                    </div>
                    <div class="date-group-info">
                        <div class="group">
                            <span>Grupo: {{match.group}}</span>
                        </div>
                        <div class="finished">Terminada: <span>{{match.finished ? 'Sim' : 'Não'}}</span></div>
                        <div class="datetime">Data: <span>{{dates[index]}}</span></div>
                    </div>  
                </div>
            </div>
            <div class="add-match">
                <span @click="openMatchForm('create', null)">+</span>
            </div>
        </div>

        <div class="champioship-teams">
            <div class="title">Times</div>
            <div class="teams">
                <div 
                    v-if="(teams.length > 0)" 
                    v-for="team in teams"
                    class="team"
                >   
                    <div class="flag">
                        <img :src="team.flag" alt="" />
                    </div>
                    <div class="name">{{team.name}}</div>
    
                    <div class="buttons-team">
                        <button @click="openTeamForm('edit', team)" class="edit">Editar</button>
                        <button @click="openConfirm(team)" class="delete">Excluir</button>
                    </div>
                </div>
    
                <div v-if="(teams.length === 0)" class="nothing">
                    Você ainda não tem times registrados no sistema.
                </div>
            </div>

            <div class="add-team">
                <span @click="openTeamForm('create')">+</span>
            </div>
        </div>
        
    </div>
</template>

<style>
    #custom {
        width:100%;
        height: 100%;
        display: flex;
    }
    .championship {
        border-bottom: 1px solid #000;
        padding-bottom: 40px;
    }
    .championship-select {
        margin-left: 15px;
        font-weight: 700;
        flex:1;
        width:450px;
    }
    .championship-select select {
        padding:5px;
        outline: none;
        border:1px solid #000;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
        margin-right: 5px;
    }
    .championship-select button {
        padding: 7px 20px;
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
    .championship-select button.delete {
        padding: 4px 10px;
        font-size: 12px;
        background-color: rgb(221, 37, 37);
        color:#fff;
        margin-left: 5px;
    }
    .add-match,
    .add-team {
        margin-top: 30px;
        width:100%;
        display: flex;
        justify-content: center;
    }
    .add-team {
        margin-top: 10px;
        justify-content: center;
        margin-bottom: 20px;
    }
    .add-match span,
    .add-team span {
        font-size: 40px;
        font-weight: 600;
        color: #069446;
        width:40px;
        height:40px;
        border:4px solid #069446;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }
    
    #custom .matchs {
        margin-top: 30px;
    }
    #custom .matchs .remove span{
        border:2px solid rgb(221, 37, 37);
        border-radius: 50%;
        padding: 2px 4px;
        color:rgb(221, 37, 37);
        position: relative;
        top: -14px;
        right: -8px;
    }
    #custom .match .flag img {
        width:80%;
    }
    .champioship-teams .title {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .teams {
        margin-left: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .teams .title {
        margin-bottom: 0px;
    }
    .teams .team {
        display: flex;
        align-items: center;
        justify-content: center;
        
        border:2px solid #000;
        border-radius: 8px;
        padding: 10px 20px;
        margin: 10px;
    }
    .teams .team .name {
        font-weight: 600;
        width:113px;
    }
    .teams .team .flag {
        width:45px;
        height: 45px;
        border:none;
        border-radius: 50%;
    }
    .teams .team .flag img {
        object-fit: contain;
        width:100%;
        height:100%;
    }
    .teams .team .buttons-team {
        margin-left: 10px;
        width:130px
    }
    .teams .team .buttons-team button {
        margin-left: 5px;
        border:none;
        padding: 4px 8px;
        border-radius: 3px;
        cursor: pointer;
    }
    .teams .team .buttons-team button.edit {
        background-color: #888;
        color:#fff;
    }
    .teams .team .buttons-team button.delete {
        background-color: rgb(221, 37, 37);
        color:#fff;
    }

    @media(max-width:420px) {
        #custom {
            flex-direction: column;
            font-size: 16px;
            width:100vw;
        }
        .championship {
            width:100%;
        }
        .championship-select {
            font-weight: 600;
            flex:1;
            width:100%;
        }
        .championship-select select {
            padding:0px;
            font-size: 16px;
        }
        .championship-select button {
            padding: 4px 10px;
            font-size: 16px;
        }
        .championship-select button.delete {
            padding: 4px 8px;
            font-size: 12px;
            position: relative;
            z-index: 2;
            margin-top: 5px;
        }
        #custom .matchs {
            margin-left: 15px;
            max-height: 320px;
            overflow: scroll;
        }
        #custom .matchs .match {
            width:90vw;
            margin-bottom: 0px;
        }
        #custom .matchs .match:first-child {
            margin-top: 10px;
        }
        #custom .matchs .team-name {
            font-size: 14px;
            width:98px;
            margin: 0;
        }
        #custom .matchs .score {
            margin: 5px;
        }
        #custom .matchs .remove{
            width:0px;
            height: 0px;
        }
        #custom .matchs .remove span{
            border:2px solid rgb(221, 37, 37);
            border-radius: 50%;
            padding: 1px 4px;
            color:rgb(221, 37, 37);
            position: relative;
            z-index: 2;
            background-color: #fff;
            top: -36px;
            left: -18px;
        }
        #custom .matchs .date-group-info{
            width:110%;
            margin-left: -24px;
            justify-content: center;
        }
        #custom .matchs .date-group-info .group,
        #custom .matchs .date-group-info .finished,
        #custom .matchs .date-group-info .datetime {
            font-size: 14px;
        }
        .add-match {
            margin-top: 5px;
            margin-left: -5px;
        }
        .add-team{
            margin-right: -10px;
        }
        .add-match span,
        .add-team span {
        font-size: 40px;
        width:45px;
        height:45px;
    }
        .champioship-teams .title {
            margin-top: 30px;
        }
        .teams {
            width:100vw;
            margin-left: 0;
            max-height: 360px;
            overflow-y: scroll;
        }
        .teams .team {
            padding:10px 15px;
        }
        .teams .team .name {
            font-weight: 600;
        }
        .teams .team .buttons-team button {
            font-size: 14px;
        }
        .teams .team .buttons-team {
            width:130px
        }
    }
</style>