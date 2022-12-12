<script>
    export default {
        props:['match'],
        data() {
            return {
                error:null,
            }
        },
        methods: {
            deleteMatch:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/match/${this.match.id}`, {
                    method:'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.matchs.splice(this.$parent.matchs.indexOf(this.match), 1);
                    this.$parent.matchConfirmOpened = false;
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div class="confirm delete championship match">
            <div class="message">Tem certeza que deseja excluir esta partida?</div>
            {{error ? error : ''}}
            <div class="match">
                <div class="info">

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

                </div>
                <div class="date-group-info">
                    <div class="group">
                        <span>Grupo: {{match.group}}</span>
                    </div>
                    <div class="finished">Terminada: <span>{{match.finished ? 'Sim' : 'Não'}}</span></div>
                    <div class="datetime">Data: <span>{{match.datetime}}</span></div>
                </div>  
            </div>
            
            <div class="buttons">
                <button 
                    @click="deleteMatch"
                >Excluir</button>
                <button 
                    @click="this.$parent.matchConfirmOpened = false" 
                    class="cancel"
                >Cancelar</button>
            </div>
            
        </div>
    </div>
    
</template>

<style>
    .confirm.delete button{
        margin-right: 30px;
        cursor: pointer;
    }
    .confirm.delete button.cancel{
        background-color: #ddd;
        border: 1px solid #000;

    }
    .confirm.delete.match {
        height: auto;
        
    }
    .confirm.delete.match .match {
        margin-bottom: 0;
    }
    .confirm.delete.match .buttons {
        margin-top: 0;
    }
    @media (max-width:420px) {
        .confirm.delete.match {
            width:75%;
            margin-left: 2px;
        }
        .confirm.delete.match .team-name {
            width:90px
        }
    }
</style>