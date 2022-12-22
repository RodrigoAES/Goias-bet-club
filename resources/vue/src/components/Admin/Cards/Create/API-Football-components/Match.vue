<script>
    export default {
        props:['match'],
        methods:{
            addMatch:function() {
                this.$emit('addMatch', this.match);
            }
        }

    }
</script>

<template>
    <div @click="addMatch" class="match api">
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
                    Number.isInteger(parseInt(match.group)) ? 'Rodada:' : 
                    'Grupo:'
                }}
                <span>{{match.group}}{{Number.isInteger(parseInt(match.group)) ? 'ª' : ''}}</span>
            </div>
            <div class="finished">Terminada: <span>{{match.finished ? 'SIM' : 'NÃO'}}</span></div>
            <div class="datetime">Data: <span>{{match.datetime}}</span></div>
        </div>  
    </div>
</template>

<style>
    .match.api {
        margin-top: 0px;
        margin-bottom: 0px;
        overflow-x: hidden;
        height:auto
    }
    .match.api .date-group-info {
        margin-left: -25px;
    }
</style>