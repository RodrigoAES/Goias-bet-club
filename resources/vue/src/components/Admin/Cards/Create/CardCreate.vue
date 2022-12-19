<script>
    import CardCreateForm from './CardCreateForm.vue'; 
    import CardCreateRound from './CardCreateRound.vue';

    export default {
        data() {
            return {
                matches:null,
                cardMatches:[],
                round:null,
                cardEndtime:null,
                cardPrice:null,
                cardName:null,
                formOpened: false,
                formError:{
                    endtime:null,
                    price:null,
                    matchs:null,
                },
                championship:null,
                championships:[],
                roundOpened:false,
            }
        },
        methods:{
            addMatch: function(match) {
                if(this.cardMatches.indexOf(match) === -1){
                    this.cardMatches.push(match);
                }
            },
            removeMatch: function(match) {
                this.cardMatches.splice(
                    this.cardMatches.indexOf(match), 1
                );
            },
            createCard: async function(endtime, price, name, type, host_percentage) {
                let body = new FormData();
                body.append('name', name);

                if(endtime) {
                    endtime = endtime.split('T');
                    endtime = `${endtime[0]} ${endtime[1]}`;
                    console.log(endtime);
                    body.append('endtime', endtime);

                } else {
                    this.formError.endtime = ['A cartela precisa ter um prazo de encerramento'];
                    return;
                }
                
                body.append('price', price);
                body.append('type', type);
                body.append('championship', this.championship);
                body.append('host_percentage', host_percentage);

                if(this.championship === 'brasileirao') {
                    body.append('round', this.round);
                }

                for(let i=0; i<this.cardMatchesIds.length; i++) {
                    body.append('matchs[]', this.cardMatchesIds[i]);
                }

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/card`, {
                    method:'POST',
                    body: body,
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });
                let json = await request.json();

                if(json.error) {
                    this.formError = json.error;

                } else {
                    this.closeForm();
                    this.cardMatches = [];
                }   
            },
            openForm: function() {
                this.formOpened = true;
            },
            closeForm: function() {
                this.formOpened = false;
                this.formError = {
                    endtime:null,
                    price:null,
                    matchs:null,
                }
            },
            requestWorldCupMatchs:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/matches`, {
                method:'GET',
                headers:{
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
            });
                let json = await request.json();
                this.matches = json.matches;
            }, 
            requestCustomChampMatchs:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championship/${this.championship}`, {
                    method:'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();  

                if(json.status === 'success') {
                    this.matches = json.matchs;
                }
            }
        },
        computed:{
            cardMatchesIds:function() {
                let ids = [];
                this.cardMatches.forEach((match)=>{
                    ids.push(match.id);
                });
                return ids;
            }
        },
        components: {
            CardCreateForm,
            CardCreateRound,
        },  
        watch:{
            championship(value, oldValue) {
                if(value === 'world-cup') {
                    this.matches = null;
                    this.requestWorldCupMatchs();

                } else if(value === 'brasileirao') {
                    this.matches = null;
                    this.roundOpened = true;

                } else {
                    this.matches = null;
                    this.requestCustomChampMatchs();
                }
            }
        },
        async mounted() {
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championships`, {
                method:'GET',
                headers:{
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
            });

            let json = await request.json();

            if(json.status === 'success') {
                this.championships = json.championships;
                this.championship = 'world-cup';
            }
        }
    }
</script>

<template>
    <CardCreateForm 
        v-if="formOpened" 
        :error="formError"
    />

    <CardCreateRound 
        v-if="roundOpened"
    />

    <div id="create-card">
        <div id="next-matches">
            <div class="matches">
                <div class="head">
                    <div class="title">Próximas partidas</div>
                    <div class="switch-championship">
                        <select class="select" v-model="championship">
                            <option value="world-cup">Copa do Mundo 2022</option>
                            <option value="brasileirao">Brasileirão</option>

                            <option 
                                v-for="champ in championships" 
                                :value="champ.id"
                            >
                                {{champ.name}}
                            </option>

                        </select>    
                    </div>
                </div>
                
                <div v-if="matches" v-for="match in matches" class="match" @click="addMatch(match)">
                    <div class="info">
                        <div class="team home">
                            <div class="team-name">{{match.home_name}}</div>
                            <div class="flag">
                                <img 
                                    v-if="championship === 'world-cup'" 
                                    :src="match.home_flag" 
                                    :alt="`${match.home_name} flag`" 
                                />
                                
                                <img 
                                    v-else-if="championship === 'brasileirao'" 
                                    :src="`${this.$root.base}brasileirao/flag/${match.home_flag}`" 
                                    :alt="match.home_name" 
                                    class="brasileirao"
                                />

                                <img 
                                    v-else
                                    :src="match.home_flag"
                                    :alt="match.home_name"
                                    class="custom" 
                                />
                            </div>
                            <div class="score home">{{match.home_score}}</div>
                        </div>
                        
                        
                        <div class="x">X</div>
    
                        <div class="team away">
                            <div class="score away">{{match.away_score}}</div>
                            <div class="flag">
                                <img 
                                    v-if="championship === 'world-cup'" 
                                    :src="match.away_flag" 
                                    :alt="`${match.away_name} flag`" 
                                />

                                <img 
                                    v-else-if="championship === 'brasileirao'" 
                                    :src="`${this.$root.base}brasileirao/flag/${match.away_flag}`" 
                                    :alt="match.home_name" 
                                    class="brasileirao"
                                />

                                <img 
                                    v-else
                                    :src="match.away_flag"
                                    :alt="match.away_name"
                                    class="custom" 
                                />
                            </div>
                            <div class="team-name">{{match.away_name}}</div>
                        </div>
                        
                    </div>
                    <div class="date-group-info">
                        <div class="group">
                            {{
                                championship === 'world-cup' ? 'Grupo:' : 
                                championship === 'brasileirao' ? 'Rodada:' :
                                Number.isInteger(parseInt(championship)) ? 'Grupo/Rodada:' :
                                null
                            }}
                            <span>{{match.group}}</span>
                        </div>
                        <div class="finished">Terminada: <span>{{match.finished === 'FALSE' ? 'NÂO' : 'SIM'}}</span></div>
                        <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                    </div>  
                </div>
            </div>
        </div>
        <div id="test"></div>

        <div class="line"></div>

        <div id="card">
            <div class="matches">
                <div class="title">Bolão</div>
                <div class="card">
                    <div class="home-away"><span>Casa</span><span>Visitante</span></div>
                    <div v-for="match in cardMatches" class="match">
                        <div class="info">
                            <div class="team home">
                                <div class="team-name">{{match.home_name}}</div>
                                <div class="flag">
                                    <img 
                                        v-if="championship === 'world-cup'" 
                                        :src="match.home_flag" 
                                        :alt="`${match.home_name} flag`" 
                                    />

                                    <img 
                                        v-else-if="championship === 'brasileirao'" 
                                        :src="`${this.$root.base}brasileirao/flag/${match.home_flag}`" 
                                        :alt="match.home_name" 
                                        class="brasileirao"
                                    />

                                    <img 
                                        v-else
                                        :src="match.home_flag"
                                        :alt="match.home_name"
                                        class="custom" 
                                    />
                                </div>
                                <div class="score home">{{match.home_score}}</div>
                            </div>
                            
                            
                            <div class="x">X</div>
        
                            <div class="team away">
                                <div class="score away">{{match.away_score}}</div>
                                <div class="flag">
                                    <img 
                                        v-if="championship === 'world-cup'" 
                                        :src="match.away_flag" 
                                        :alt="`${match.away_name} flag`" 
                                    />

                                    <img 
                                        v-else-if="championship === 'brasileirao'" 
                                        :src="`${this.$root.base}brasileirao/flag/${match.away_flag}`" 
                                        :alt="match.home_name" 
                                        class="brasileirao"
                                    />

                                    <img 
                                        v-else
                                        :src="match.away_flag"
                                        :alt="match.away_name"
                                        class="custom" 
                                    />
                                </div>
                                <div class="team-name">{{match.away_name}}</div>
                            </div>
                            <div @click="removeMatch(match)" class="remove"><span>✖</span></div>
                        </div>
                        <div class="date-group-info">
                            <div class="group">
                                {{
                                    championship === 'world-cup' ? 'Grupo' : 
                                    championship === 'brasileirao' ? 'Rodada' : 
                                    Number.isInteger(parseInt(championship)) ? 'Grupo/Rodada' :
                                    null
                                }} 
                                
                                <span>{{match.group}}</span>
                            </div>
                            <div class="finished">Terminada: <span>{{match.finished === 'FALSE' ? 'NÂO' : 'SIM'}}</span></div>
                            <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                        </div>  
                    </div>

                    <div class="buttons">
                        <button @click="openForm" v-if="cardMatches.length > 0">Criar Bolão</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</template>

<style>
    #create-card {
        display:flex;
        width:100%;
    }
    .matches {
        padding:20px;
    }
    .matches .head {
        display:flex;
        justify-content: center;
        align-items: center;

    }
    .matches .title {
        font-size: 25px;
        font-weight: 700;
        width: auto;
        display: flex;
        justify-content: center;
        margin-bottom: 25px;
        margin-right: 15px;
    }
    .switch-championship {
        height: auto;
        margin-bottom: 25px;
    }
    .matches .select {
        padding:5px;
        outline: none;
        border:1px solid #000;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
    }
    .match{
        height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-bottom: 30px;
    }
    .match .info {
        margin:10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border:2px solid #000;
        border-radius: 18px;
        padding: 8px 0px;
    }
    .match .team {
        display:flex;
        align-items: center;
    }
    .match .team-name {
        width:110px;
        font-weight: 700;
    }
    .match .home .team-name {
        display: flex;
        justify-content: flex-end;
        margin-right: 10px;
    }
    .match .away .team-name {
        margin-left: 10px;
    }
    .match .flag {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .match .flag img{
        max-width: 70px;
        max-height: 70px;
    }
    .match .flag img.brasileirao {
        max-width: 40px;
        max-height: 40px;
    }
    .match .flag img.custom {
        max-width: 80%;
    }
    .match .home .score {
        font-weight: 700;
        font-size: 22px;
        margin-left: 10px;
    }
    .match .away .score {
        font-weight: 700;
        font-size: 22px;
        margin-right: 10px;
    }
    .match .x {
        margin:0px 20px;
        font-size: 26px;
        font-weight: 700;
    }
    .match .date-group-info {
        display:flex;
        align-items: center;
        justify-content: space-around;
        margin-bottom: 25px;
        font-weight: 500;
    }
    .match .date-group-info span {
        font-weight: 700;
    }
    .match .group{
        margin-right: 15px;
    }
    .match .finished {
        margin-right: 15px;
    }
    #card .card {
        border:2px solid #000;
        border-radius: 20px;
        padding: 25px 20px;
        width:496px;
    }
    #card .home-away {
        display: flex;
        justify-content: space-around;
        width:100%;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    #card .info {
        position: relative;
        z-index: 2;
    }
    #card .remove {
        position: absolute;
        z-index: 2;
        right:3px;
        top:3px;
        display: flex;
        justify-content: center;
        align-items: center;
        border:2px solid rgb(221, 37, 37);
        border-radius: 50%;
        height: 20px;
        width: 20px;
    }
    #card .remove span {
        margin-top: -2px;
        margin-right: -1px;
        font-weight: 700;
        color:rgb(221, 37, 37)
    }
    #card .buttons {
        width:100%;
        display: flex;
        justify-content: center;
    }
    #card .buttons button {
        border: none;
        border-radius: 6px;
        padding:10px 20px;
        background-color: var(--p-color);
        color: var(--s-color);
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
    #card .buttons button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
    .line {
        margin: 20px 0px;
        width: calc(100vw - 10%);
        height: 1px;
        background-color: #000;
        display: none;
    }
    

    @media (max-width:420px) {
        #create-card {
            flex-direction: column;
            margin-top: 15px;
        }
        .matches .title {
            width:auto;
            font-size: 22px;
        }
        #next-matches {
            height: 280px;
            overflow-y: scroll;
        }
        .line {
            display: block;
        }
        #card .card {
            border:2px solid #000;
            border-radius: 20px;
            padding: 25px 6px;
            width:calc(100vw - 40px);
            margin-left: -27px;
        }
        #card .remove {
        top:-10px;
        right:-5px ;
        height: 22px;
        width: 22px;
        background-color: #fff;
        }
        #card .remove span {
            margin-right: -1px;
        }
        .match .team-name {
            width:85px;
            font-size: 14px;
            font-weight: 700;
            overflow-x: hidden;
            text-align: center;
        }
        .match .flag {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
            display:flex;
            justify-content: center;
            align-items: center;
        }
        .match .flag img{
            max-width: 50px;
            max-height: 50px;
        }
        .match .home .score {
            font-weight: 700;
            font-size: 18px;
            margin-left: 10px;
        }
        .match .away .score {
            font-weight: 700;
            font-size: 18px;
            margin-right: 10px;
        }
        .match .x {
            margin:0px 10px;
            font-size: 20px;
            font-weight: 700;
        }
        .match .group{
            margin-right: 6px;
        }
        .match .finished {
            margin-right: 6px;
        }
        .match .date-group-info {
            width:100%;
            margin-right: -40px;
        } 
    }
</style>