<script>
    export default {
        props:['championship'],
        methods: {
            deleteChampionship:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/championship/${this.championship}`, {
                    method:'DELETE', 
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.refreshChampionships()
                    this.$parent.championshipConfirmOpened = false;
                } 
            }
        }
    }
</script>

<template>
    <div class="modal-area ">
        <div class="confirm delete championship">
            <div class="message">Tem certeza que deseja excluir o Campeonato e todos os seus times e partidas</div>
            <div class="buttons">
                <button 
                    @click="deleteChampionship"
                >Excluir</button>
                <button 
                    @click="this.$parent.championshipConfirmOpened = false" 
                    class="cancel"
                >Cancelar</button>
            </div>
            
        </div>
    </div>
    
</template>


<style>
    @media(max-width:420px) {
        .confirm.delete {
            width:280px
        }
        .championship .buttons {
            display: block;
        }
        .buttons button {
            padding: 10px 20px;
            margin:0;
            font-size: 16px;
        }
        .confirm.delete button.cancel {
            padding: 10px 20px;
            margin:10px;
            font-size: 16px;
        }
        .confirm.delete button {
            margin-right: 0;
        }
        .confirm .message {
            font-size: 16px;
        }
    }
</style>