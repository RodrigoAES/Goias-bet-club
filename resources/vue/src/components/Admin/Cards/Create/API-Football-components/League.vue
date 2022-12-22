<script>
    import LeagueMatchsForm from './LeagueMatchsForm.vue';

    import Match from './Match.vue';
    import Team from './Team.vue';

    export default {
        props:['league', 'filter'],
        data() {
            return {
                optionsOpened:false,
                seasonsOpened:false,
                LeagueMatchsFormOpened:false,

                selectedSeason: this.filter === 'current' ? this.league.seasons[0].season : null,

                matchs: this.league.matchs ? this.league.matchs : null,
                teams:null,
            }
        },
        methods:{
            optionsToggle:function (leagueId) {
                if(!this.optionsOpened) {
                    this.optionsOpened = true;
                    
                    setTimeout(()=>{ 
                        let options = document.querySelector(`.league[data-id="${leagueId}"] .league-options`);
                        options.style.height = 'auto';
                        let height = `${options.offsetHeight}px`;
                        console.log(height);
                        options.style.height = '0px';

                        setTimeout(()=>{
                            options.style.height = height;
                        }, 1000);
                    }, 1);

                } else if(this.optionsOpened) {
                    let options = document.querySelector(`.league[data-id="${leagueId}"] .league-options`);
                    options.style.height = '0px';
                    setTimeout(()=>{
                        this.optionsOpened = false;
                    }, 1000);


                }
            },
            openSeasons:function() {
                this.seasonsOpened = true;
            },
            seasonsOpenToggle:function(leagueId) {
                let league = document.querySelector(`.league[data-id="${leagueId}"]`);
                let seasons = league.querySelector('.seasons');

                if(seasons.style.height && seasons.style.height === '42px') {
                    seasons.style.height = 'auto',
                    seasons.querySelector('.label img').style.transform = 'rotate(180deg)';

                } else {
                    seasons.style.height = '42px';
                    seasons.querySelector('.label img').style.transform = 'rotate(0deg)';
                }
            },
            OpenCloseMatchs:function(leagueId) {
                let league = document.querySelector(`.league[data-id="${leagueId}"]`);
                let matchs = league.querySelector('.matchs');

                if(matchs.style.height && matchs.style.height === '42px') {
                    matchs.style.height = 'auto';
                    let height = `${matchs.offsetHeight}px`;

                    matchs.style.height = '42px';
                    setTimeout(()=>{
                        matchs.style.height = height;
                    }, 1);

                    matchs.querySelector('.label img').style.transform = 'rotate(180deg)';

                } else {
                    matchs.style.height = `${matchs.offsetHeight}px`;
                    setTimeout(()=>{
                        matchs.style.height = '42px';
                    }, 1);
                    
                    matchs.querySelector('.label img').style.transform = 'rotate(0deg)';
                }
            },
            OpenCloseTeams:function(leagueId) {
                let league = document.querySelector(`.league[data-id="${leagueId}"]`);
                let teams = league.querySelector('.teams-api');

                if(teams.style.height && teams.style.height === '42px') {
                    teams.style.height = 'auto';
                    let height = `${teams.offsetHeight}px`;
                    
                    teams.style.height = '42px';
                    setTimeout(()=>{
                        teams.style.height = height;
                        setTimeout(()=>{
                            teams.style.height = 'auto';
                        }, 1000);
                    }, 1);

                    teams.querySelector('.label img').style.transform = 'rotate(180deg)';

                } else {
                    teams.style.height = `${teams.offsetHeight}px`;
                    setTimeout(()=>{
                        teams.style.height = '42px';
                    }, 1);
                    
                    teams.querySelector('.label img').style.transform = 'rotate(0deg)';
                }
            },
            requestLeagueTeams: async function() {
                this.optionsOpened = false;
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/api-football/league-teams/${this.league.id}/${this.selectedSeason}`, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.teams = json.teams;
                }
            },
            addMatch:function(match) {
                this.$emit('addMatch', match);
            },
           
        },
        components:{
            LeagueMatchsForm,
            Match,
            Team
        }
    }
</script>

<template>
    <div :data-id="league.id" class="league">
        <LeagueMatchsForm 
            v-if="LeagueMatchsFormOpened" 
            :league="league.id"
            :filter="filter"
            :season="selectedSeason"
        />

        <div class="up">
            <div class="info">
                <div class="logo">
                    <img :src="league.logo" />
                </div>
                <div class="name">
                    {{ league.name }}
                </div>
            </div>
        
            <div class="options">
                <div @click="optionsToggle(league.id)" class="open">
                    Opções
                    <img :src="`${this.$root.asset}assets/icons/down-arrow.png`" alt="">
                </div>
            </div>
        </div>

        <div v-if="optionsOpened" class="league-options">
        
            <span
                v-if="filter === 'current'" 
                @click="LeagueMatchsFormOpened = true; optionsOpened = false"
            >
                Ver Partidas desta liga
            </span>
            
            <span 
                v-if="filter === 'current'"
                @click="requestLeagueTeams"
            >
                Ver times desta liga
            </span>

            <span 
                v-if="filter === 'all'"
                @click="OpenSeasons"
            >
                Ver Temporadas desta liga
            </span>
            
        </div>

        <div v-if="seasonsOpened" class="seasons">
            <div 
                @click="seasonsOpenToggle(league.id)" 
                class="label"
            >
                Temporadas 
                <img :src="`${this.$root.asset}assets/icons/down-arrow.png`" alt="">
            </div>
            <div 
                v-for="season in league.seasons" 
                @click="this.selectedSeason = season.season; this.LeagueMatchsFormOpened = true;"
                class="season"
            >
                <div class="logo"><img :src="league.logo"></div>
                <div class="year">Teamporada: {{season.season}}</div>
                <div class="start"><span>início:</span>{{season.start}}</div>
                <div class="end"><span>Termino:</span>{{season.end}}</div>
            </div>
        </div>

        <div v-if="matchs && matchs.length > 0" class="matchs">
            <div @click="OpenCloseMatchs(league.id)" class="label">
                Partidas: 
                <span><img :src="`${this.$root.asset}assets/icons/down-arrow.png`"></span>
            </div>

            <Match 
                v-for="match in matchs" 
                :match="match"
                @addMatch="addMatch"
            />
        </div>

        <div v-if="teams && teams.length > 0" class="teams-api">
            <div @click="OpenCloseTeams(league.id)" class="label">
                Times: 
                <span><img :src="`${this.$root.asset}assets/icons/down-arrow.png`"></span>
            </div>

            <Team 
                v-for="team in teams"
                :team="team"
                :filter="filter"
                @addMatch="addMatch"
            />
        </div>
    </div>
</template>

<style>
.league {
    display:flex;
    flex-direction: column;
    align-items: center;
    padding: 10px;
    border: 2px solid #000;
    height: auto;
    margin-top: 10px;
    overflow-x: hidden;
}
.league .up {
    display:flex;
    align-items: center;
    flex-direction: row;
    justify-content: space-between;
    width: 90%;
}
.league .up .info {
    display: flex;
    align-items: center;
    margin-bottom: 0;
}
.league .logo {
    width:40px;
    height:40px;
    border-radius: 10px;
    overflow: hidden;
    display:flex;
    justify-content: center;
    align-items: center;
}
.league .logo img {
    max-width: 100%;
    max-height:100%;
}
.league .name {
    font-weight: 600;
    margin-left: 10px;
}
.league .options {
    position: relative;
    display:flex;
    align-items: center;
    font-weight: 600;
    cursor: pointer;
    z-index: 4;
}
.options .open img {
    width:16px;
    margin-bottom: -4px;
}
.league-options {
    margin-top: 5px;
    display: flex;
    flex-direction: column;
    background-color: #fff;
    font-weight: 600;
    font-size: 14px;
    border:1px solid #000;
    width:100%;
    height:0px;
    transition: all ease 1s;
    overflow-y: hidden;
}
.league-options span {
    margin-bottom: 3px;
    padding: 10px;
    cursor: pointer;
}
.league-options span:hover {
    background-color: #ddd;
}
.seasons {
    margin-top: 15px;
    overflow-y: hidden;
    width:auto
}
.season {
    display:flex;
    flex-direction: column;
    align-items: center;
    border:2px solid var(--p-color);
    padding: 10px;
    margin-top: 10px;
    width:auto;
    cursor: pointer;
}
.season div {
    margin-bottom: 5px;
}
.season div span {
    font-weight: 600;
    margin-right: 5px;
}
.season .year {
    font-weight: 600;
    color:var(--p-color);
}
.league .seasons .label {
    font-weight: 600;
    margin-top: 15px;
    display: flex;
    align-items: center;
    cursor: pointer;
}
.league .seasons .label img {
    width:15px;
    margin-left: 5px;
    margin-top: 4px;
    transform: rotate(180deg);
}
.league .matchs {
    margin-top: 20px;
    overflow-y: hidden;
    transition: all ease 1.5s;
}
.league .matchs .label {
    font-weight: 600;
    margin-top: 15px;
    margin-bottom: 15px;
    width:100%;
    display: flex;
    align-items: center;
    cursor: pointer;
}
.league .matchs .label img {
    width:15px;
    margin-left: 5px;
    margin-top: 4px;
    transform: rotate(180deg);
}
.league .teams-api {
    margin-top: 20px;
    overflow-y: hidden;
    transition: all ease 1.5s;
    width:100%;
}
.league .teams-api .label {
    font-weight: 600;
    margin-top: 15px;
    margin-bottom: 15px;
    width:100%;
    display: flex;
    align-items: center;
    cursor: pointer;
}
.league .teams-api .label img {
    width:15px;
    margin-left: 5px;
    margin-top: 4px;
    transform: rotate(180deg);
}

@media(max-width:420px) {
    .league {
        width:105%;
        margin-left: -20px;
    }
}
</style>

