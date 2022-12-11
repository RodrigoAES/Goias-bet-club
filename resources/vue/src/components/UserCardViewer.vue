<script>
    export default {
        props:['userCard'],
        computed: {
            date:function() {
                let date = new Date(this.userCard.created_at);
                return  `${date.getDate() < 10 ? '0'+date.getDate() : date.getDate()}/${date.getMonth()+1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
            }
        }
    }
</script>

<template>
    <div class="modal-area admin-card-viewer">
        <div v-if="userCard" class="card">
            <div class="close"><span @click="this.$parent.closeCard()">✖</span></div>
            <div class="bet-card"><span>Cartela: {{userCard.card.name}}</span></div>
            <div class="name"><span>Nome: {{userCard.name}}</span></div>
            <div class="user-card phone"><span>Celular: {{userCard.phone}}</span></div>
            <div class="user-card created-at"><span>Data: {{date}}</span></div>
            <div class="validated"><span>Validado: {{userCard.validated ? 'Sim' : 'Não'}}</span></div>
            <div class="viewer-price"><span>Preço: R$ {{userCard.card.price}}</span></div>
            <div class="home-away"><span>Casa</span><span>Fora</span></div>

            <div v-for="(bet, index) in userCard.bets" class="match bet">
                <div class="info">
                    <div class="team home">
                        <div class="team-name">{{bet.match.home_name}}</div>
                        <div class="flag">
                            <img 
                                v-if="userCard.card.championship === 'world-cup'" 
                                :src="bet.match.home_flag" 
                                :alt="`${bet.match.home_name} flag`" 
                            />

                            <img 
                                v-else-if="userCard.card.championship === 'brasileirao'" 
                                :src="`http://localhost:8000/api/brasileirao/flag/${bet.match.home_flag}`" 
                                :alt="bet.match.home_name" 
                                class="brasileirao"
                            />

                            <img 
                                v-else
                                :src="bet.match.home_flag"
                                :alt="bet.match.home_name"
                                class="custom" 
                            />
                        </div>
                        <div class="score home">{{bet.match.home_score}}</div>
                    </div>
                    
                    <div class="x">X</div>
    
                    <div class="team away">
                        <div class="score away">{{bet.match.away_score}}</div>
                        <div class="flag">
                            <img 
                                v-if="userCard.card.championship === 'world-cup'" 
                                :src="bet.match.away_flag" 
                                :alt="`${bet.match.away_name} flag`" 
                            />

                            <img 
                                v-else-if="userCard.card.championship === 'brasileirao'" 
                                :src="`http://localhost:8000/api/brasileirao/flag/${bet.match.away_flag}`" 
                                :alt="bet.match.home_name" 
                                class="brasileirao"
                            />

                            <img 
                                v-else
                                :src="bet.match.away_flag"
                                :alt="bet.match.away_name"
                                class="custom" 
                            />
                        </div>
                        <div class="team-name">{{bet.match.away_name}}</div>
                    </div>
                </div>
                <div v-if="userCard.card.type === 'common'" class="bet-buttons">
                    <button
                        :class="bet.bet === 'victory' ? 'active' : null"
                    >Vitória</button>

                    <button
                    :class="bet.bet === 'draw' ? 'active' : null"
                    >Empate</button>

                    <button
                    :class="bet.bet === 'loss' ? 'active' : null"
                    >Derrota</button>
                </div>
                <div v-if="userCard.card.type === 'detailed'" class="user-bet">
                    <span>Apostado:</span>
                    <span>
                        <img 
                            v-if="userCard.card.championship === 'world-cup'" 
                            :src="bet.match.home_flag" 
                            :alt="`${bet.match.home_name} flag`" 
                        />

                        <img 
                            v-else-if="userCard.card.championship === 'brasileirao'" 
                            :src="`http://localhost:8000/api/brasileirao/flag/${bet.match.home_flag}`" 
                            :alt="bet.match.home_name" 
                            class="brasileirao"
                        />

                        <img 
                            v-else
                            :src="bet.match.home_flag"
                            :alt="bet.match.home_name"
                            class="custom" 
                        />

                        {{bet.home_score}} X {{bet.away_score}}
                        
                        <img 
                            v-if="userCard.card.championship === 'world-cup'" 
                            :src="bet.match.away_flag" 
                            :alt="`${bet.match.away_name} flag`" 
                        />
                    
                        <img 
                            v-else-if="userCard.card.championship === 'brasileirao'" 
                            :src="`http://localhost:8000/api/brasileirao/flag/${bet.match.away_flag}`" 
                            :alt="bet.match.home_name" 
                            class="brasileirao"
                        />

                        <img 
                            v-else
                            :src="bet.match.away_flag"
                            :alt="bet.match.away_name"
                            class="custom" 
                        />
                    </span>
                </div>
                <div class="date-group-info">
                    <div class="group">Grupo <span>{{bet.match.group}}</span></div>
                    <div class="finished">Terminada: <span>{{bet.match.finished === 'FALSE' ? 'NÂO' : 'SIM'}}</span></div>
                    <div class="datetime">Data: <span>{{bet.match.datetime}}</span></div>
                </div>  
            </div>
            <div v-if="(! userCard.validated)" class="actions">
                <button 
                    @click="this.$parent.validateCard(userCard.id); this.$parent.userCardViewerOpened = false"
                    class="validate"
                >Validar</button>
            </div>
        </div>
    </div>
</template>

<style>
    .modal-area.admin-card-viewer {
        top:0;
        left:0;
        margin-top: 0;
        margin-left: 0;
        overflow-y: scroll;
        display: flex;
        flex: column;
        justify-content: center;
    }
    .modal-area.admin-card-viewer .card {
        background-color: #fff;
        margin-top: 1600px;
        margin-bottom: 50px;
        border: 5px solid #069446;
        box-shadow: 0px 0px 20px #069446;
        position: relative;
    }
    .modal-area.admin-card-viewer .card .close {
        top:1%;
    }
    .modal-area.admin-card-viewer .actions {
        width:100%;
        display: flex;
        justify-content: center;
    }
    .modal-area.admin-card-viewer .actions .validate {
        margin-right: 10px;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        background-color: #006ed4;
        color:#fff;
        font-size: 16px;
        font-weight: 600;
    }
    .modal-area.admin-card-viewer .actions .validate:hover {
        background-color: #1c7fdb;
        transform: scale(1.1);
    }
    .modal-area.admin-card-viewer .validated,
    .modal-area.admin-card-viewer .viewer-price {
        font-weight: 600;
        margin-left: 14px;
        
        margin-bottom: 15px;
    }  
    .modal-area.admin-card-viewer .validated{
        margin-top: -13px;
    }

    @media (max-width:420px) {
        .modal-area.admin-card-viewer .card {
            width:100vw;
            margin-left: -0px;
        }
        .modal-area.admin-card-viewer .card .close{
            left:90%;
        }
    }
</style>