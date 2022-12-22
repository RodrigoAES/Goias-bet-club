<script>
    import CardCreateForm from './CardCreateForm.vue'; 
    import CardCreateRound from './CardCreateRound.vue';

    import Loading from '../../../Loading.vue';

    import Country from './API-Football-components/Country.vue';
    import League from './API-Football-components/League.vue';
    import Team from './API-Football-components/Team.vue';

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

                APIFootballSearchQuery:'',
                APIFootballSearchLoading:false,
                APIFootballSearchFilter:'current',
                countries:null,
                leagues:null,
                teams:null,
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
                let championship = this.championship.split('-')[1];
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championship/${championship}`, {
                    method:'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();  

                if(json.status === 'success') {
                    this.matches = json.matchs;
                }
            },
            APIFootballSearch:async function() {
                if(this.APIFootballSearchQuery.trim() != '') {
                    this.APIFootballSearchLoading = true;

                    this.matches = null;
                    this.countries = null;
                    this.leagues = null;
                    this.teams = null;

                    let filter = this.APIFootballSearchFilter;
                    let originalQuery = this.APIFootballSearchQuery;

                    async function searchRequest(query) {
                        let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/api-football/search${filter === 'all' ? '/false/' : ''}?q=${query}`, {
                            method:'GET',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth')}`
                            }
                        });
                        let json = await request.json();

                        return json;
                    }

                    async function searchQueryToEnglish(query) {
                        let request = await fetch(`https://microsoft-translator-text.p.rapidapi.com/translate?to%5B0%5D=en&api-version=3.0&from=pt-br&profanityAction=NoAction&textType=plain`, {
                            method: 'POST',
                            headers: {
                                'content-type': 'application/json',
                                'X-RapidAPI-Key': '6e64059c43msh880a092c33ffec8p13d910jsn08fc535cb6c1',
                                'X-RapidAPI-Host': 'microsoft-translator-text.p.rapidapi.com'
                            },
                            body: `[{"Text":"${query}"}]`
                        });

                        let json = await request.json();

                        return json[0].translations[0].text;
                    }

                    let enQuery = await searchQueryToEnglish(originalQuery);

                    let json = await searchRequest(enQuery);

                    if(json.status === 'success') {
                        if(
                            json.results.countries.length === 0 
                            && json.results.leagues.length === 0 
                            && json.results.teams.length === 0 
                        ) {
                            json = await searchRequest(originalQuery);
                        }

                        if(json.status === 'success') {
                            this.countries = json.results.countries;
                            this.leagues = json.results.leagues;
                            this.teams = json.results.teams;

                            this.APIFootballSearchLoading = false;
                        }
                    }  
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

            Loading,

            // API Footaball
            Country,
            League,
            Team,
        },  
        watch:{
            championship(value, oldValue) {
                if(value === 'world-cup') {
                    this.matches = null;
                    this.requestWorldCupMatchs();

                } else if(value === 'brasileirao') {
                    this.matches = null;
                    this.roundOpened = true;

                } else if(value.indexOf('custom') > -1) {
                    this.matches = null;
                    this.requestCustomChampMatchs();

                } else if (value.indexOf('api') > -1) {

                } 
            },
            APIFootballSearchFilter() {
                this.countries = null;
                this.leagues = null;
                this.teams = null;
                this.matchs = null;
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
                    <div class="championship">
                        <div class="title">Próximas partidas</div>
                        <div class="switch-championship">
                            <select class="select" v-model="championship">
                                <option value="world-cup">Copa do Mundo 2022</option>
                                <option value="brasileirao">Brasileirão</option>
                                <option 
                                    v-for="champ in championships" 
                                    :value="`${champ.type === 'custom' ? 'custom' : champ.type === 'api-football' ? 'api' : null}-${champ.id}`"
                                >
                                    {{champ.name}}
                                </option>
                            </select>    
                        </div>
                    </div>
                    
                    <div class="search">
                        <div class="search-filter">
                            Filtro:
                            <select v-model="APIFootballSearchFilter">
                                <option value="current">Em andamento</option>
                                <option value="all">Todos</option>
                            </select>
                        </div>
                        

                        <div class="search-action">
                            <input 
                                v-model="APIFootballSearchQuery"
                                @keyup.enter="APIFootballSearch"
                                type="text" 
                                placeholder="Pesquisar..."
                            />
                            
                            <button @click="APIFootballSearch">
                                {{! APIFootballSearchLoading ? 'Buscar' : ''}}
                                <Loading 
                                    v-if="APIFootballSearchLoading"
                                    :size="15"
                                />
                            </button>
                        </div>
                        
                    
                    </div>
                </div>

                <div class="search-results">
                    <div v-if="countries && countries.length > 0" class="countries">
                        <div class="subtitle">Países</div>
                        <Country 
                            v-for="country in countries"
                            :country="country" 
                            :filter="APIFootballSearchFilter"   
                            @addMatch="addMatch"                    
                        />
                    </div>
                    
                    <div v-if="leagues && leagues.length > 0" class="leagues">
                        <div class="subtitle">Ligas/Copas</div>
                        <League
                            v-for="league in leagues"
                            :league="league" 
                            :filter="APIFootballSearchFilter"  
                            @addMatch="addMatch"                     
                        />
                    </div>

                    <div v-if="teams && teams.length > 0" class="teams-result">
                        <div class="subtitle">Times/Seleções</div>
                        <Team
                            v-for="team in teams"
                            :team="team" 
                            :filter="APIFootballSearchFilter" 
                            @addMatch="addMatch"                     
                        />
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
                            <div class="score home">{{match.home_score ? match.home_score : 0}}</div>
                        </div>
                        
                        
                        <div class="x">X</div>
    
                        <div class="team away">
                            <div class="score away">{{match.away_score ? match.away_score : 0}}</div>
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
                                <div class="score home">{{match.home_score ? match.home_score : 0}}</div>
                            </div>
                            
                            
                            <div class="x">X</div>
        
                            <div class="team away">
                                <div class="score away">{{match.away_score ? match.away_score : 0}}</div>
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
                            <div class="finished">Terminada: <span>{{match.finished ? 'SIM' : 'NÃO'}}</span></div>
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
    #create-card .head{
        display: flex;
        flex-direction: column;
    }
    #create-card .head .championship {
        display:flex;
        align-items: center;
        padding-bottom: 0;
        border-bottom: 0;
    }
    #create-card .head .search {
        position: relative;
        left: 0;
        width:100%;
        display:flex;
        flex-direction: column;
        width:100%;
    }
    #create-card .head .search-action {
        display:flex;
        align-items: center;
        justify-content: flex-start;
        width:100%;
    }
    #create-card .head .search-action button {
        width:72px;
        display:flex;
        justify-content: center;
    }
    #create-card .head .search-filter {
        position: relative;
        width:100%;
        margin-bottom: 6px;
        font-weight: 600;
    }
    #create-card .head .search-filter select {
        outline: none;
        padding:2px 5px;
        border:1px solid #888;
        border-radius: 4px;
    }
    #create-card .head .search input {
        width:300px;
    }
    #create-card .search-results {
        margin-top: 20px;
    }
    #create-card .search-results .subtitle{
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 10px;
    }
    #create-card .search-results .leagues {
        margin-top: 20px;
    }
    #create-card .search-results .teams-result {
        margin-top: 20px;
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
    #card .match .flag img{
        max-width: 80%;
        max-height: 80%;
    }
    #card .match .flag img.brasileirao {
        max-width: 40px;
        max-height: 40px;
    }
    #card .match .flag img.custom {
        max-width: 80%;
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