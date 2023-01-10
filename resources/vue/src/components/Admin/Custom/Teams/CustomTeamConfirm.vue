<script>
    export default {
        props:['team'],
        data() {
            return {
                error:null,
            }
        },
        methods: {
            deleteTeam:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/team/${this.team.id}`, {
                    method:'DELETE', 
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.teams.splice(this.$parent.teams.indexOf(this.team), 1);
                    this.$parent.teamConfirmOpened = false;

                } else if(json.status === 'error') {
                    this.error = json.error;
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div class="confirm delete championship">
            <div class="message">Tem certeza que deseja excluir o time?</div>
            <div class="name">{{team.name}}</div>
            <div v-if="error" class="danger">{{error}}</div>
            <div class="buttons">
                <button 
                    @click="deleteTeam"
                >Excluir</button>
                <button 
                    @click="this.$parent.teamConfirmOpened = false" 
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
</style>