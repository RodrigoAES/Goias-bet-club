<script>
    import TeamMatchsForm from './TeamMatchsForm.vue';

    import Match from './Match.vue';

    export default {
        props:['team', 'filter'],
        data() {
            return {
                optionsOpened:false,

                teamMatchsFormOpened:false,

                matchs:null,
            }
        },
        methods:{
            optionsToggle:function (teamId) {
                if(!this.optionsOpened) {
                    this.optionsOpened = true;
                    setTimeout(()=>{
                        let options = document.querySelector(`.team-api[data-id="${teamId}"] .team-api-options`);
                        options.style.height = 'auto';
                        let height = `${options.offsetHeight}px`;
                        options.style.height = '0px';
                        setTimeout(()=>{
                            options.style.height = height;
                        }, 10);
                    }, 1);

                } else if(this.optionsOpened) {
                    let options = document.querySelector(`.team-api[data-id="${teamId}"] .team-api-options`);
                    options.style.height = '0px';

                    setTimeout(()=>{
                        this.optionsOpened = false;
                    }, 1000);
                }
            },
            OpenCloseMatchs:function(teamId) {
                let team = document.querySelector(`.team-api[data-id="${teamId}"]`);
                let matchs = team.querySelector('.matchs');

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
            requestTeamMatchs:async function() {
                if(this.filter === 'all') {
                    this.teamMatchsFormOpened = true;

                } else if(this.filter === 'current') {
                    let url = `${import.meta.env.VITE_BASE_URL}admin/api-football/team-matchs/${this.team.id}/1/0`;

                    let request = await fetch(url, {
                        method:'GET',
                        headers:{
                            'Authorization': `Bearer ${localStorage.getItem('auth')}`
                        }
                    });

                    let json = await request.json();

                    if(json.status === 'success') {
                        this.matchs = json.matchs;
                    }
                }
            },
            addMatch:function(match) {
                this.$emit('addMatch', match);
            },
        },
        components:{
            TeamMatchsForm,

            Match
        }
    }
</script>

<template>
    <div :data-id="team.id" class="team-api">
        <TeamMatchsForm 
            v-if="teamMatchsFormOpened"
            :team="team.id"
        />

        <div class="up">
            <div class="info">
                <div class="flag">
                    <img :src="team.flag" />
                </div>
                <div class="name">{{team.name}}</div>
            </div>
            
            
            <div class="options">
                <div @click="optionsToggle(team.id)" class="open">
                    Opções
                    <img :src="`${this.$root.asset}assets/icons/down-arrow.png`" alt="">
                </div>
            </div>
        </div>

        <div v-if="optionsOpened" class="team-api-options">
            <span @click="requestTeamMatchs(); optionsOpened = false">Ver partidas deste time</span>
        </div>

        <div v-if="matchs && matchs.length > 0" class="matchs">
            <div @click="OpenCloseMatchs(team.id)" class="label">
                Partidas: 
                <span><img :src="`${this.$root.asset}assets/icons/down-arrow.png`"></span>
            </div>

            <Match 
                v-for="match in matchs" 
                :match="match"
                @addMatch="addMatch"
            />
        </div>

    </div>
</template>

<style>
    .team-api {
        display:flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        border: 2px solid #000;
        transition: all ease 1s;
        margin-bottom: 10px;
    }
    .team-api .up {
        display:flex;
        align-items: center;
        flex-direction: row;
        justify-content: space-between;
        width:90%;
    }
    .team-api .up .info {
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }
    .team-api .flag {
        width:40px;
        height:40px;
        border-radius: 50%;
        overflow: hidden;
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .team-api .flag img {
        max-width: 80%;
        max-height:80%;
    }
    .team-api .name {
        font-weight: 600;
        margin-left: 10px;
    }
    .team-api .options {
        position: relative;
        display:flex;
        align-items: center;
        font-weight: 600;
        cursor: pointer;
        
    }
    .options .open img {
        width:16px;
        margin-bottom: -4px;
    }
    .team-api-options {
        display: flex;
        flex-direction: column;
        background-color: #FFFFFF;
        font-weight: 600;
        font-size: 14px;
        border:1px solid #000;
        width:100%;
        z-index: 50;
        height:0px;
        overflow-y: hidden;
        transition: all ease 1s;
        margin-top: 6px;
    }
    .team-api-options span {
        margin-bottom: 3px;
        padding: 10px;
        cursor: pointer;
    }
    .team-api-options span:hover {
        background-color: #ddd;
    }
    .team-api .matchs {
        margin-top: 20px;
        overflow-y: hidden;
        transition: all ease 1.5s;
    }
    .team-api .matchs .label {
        font-weight: 600;
        margin-top: 15px;
        margin-bottom: 15px;
        width:100%;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    .team-api .matchs .label img {
        width:15px;
        margin-left: 5px;
        margin-top: 4px;
        transform: rotate(180deg);
    }
</style>