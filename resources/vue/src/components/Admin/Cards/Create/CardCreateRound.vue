<script>
    export default  {
        data() {
            return {
                round:null,
            }
        },
        methods: {
            roundMask:function(e) {
                if(e.target.value.length > 2) {
                    e.target.value = e.target.value.substring(0,2);
                    this.round = e.target.value;
                }
                if(parseInt(e.target.value) < 1) {
                    e.target.value = 1;
                    this.round = e.target.value;
                } else if(parseInt(e.target.value) > 38) {
                    e.target.value = 38;
                    this.round = e.target.value;
                }
            },
            requestRound:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/brasileirao/round/${this.round}`, {
                    method:'GET', 
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    
                    this.$parent.matchOpt = 'Brasileirao';
                    this.$parent.matches = json.round.matchs;
                    this.$parent.round = json.round.id;
                    this.$parent.roundOpened = false;
                    

                } else if(json.status === 'error') {

                }
            }
        }
    }
</script>

<template>
    <div class="modal-area-cards">
        <div class="confirm">
            <div class="message">Para as partidas do Brasileirão você deve escolher uma rodada.</div>
            <div class="input">
                <label for="round">Rodada:</label>
                <input @input="roundMask" v-model="round" min="1" max="38" type="number" name="round">
            </div>
            <div class="buttons">
                <button @click="requestRound">Okay</button>
                <button @click="(this.$parent.roundOpened = false)">Cancelar</button>
            </div>
        </div>
    </div>
</template>

<style>
    .modal-area-cards {
        position: absolute;
        width:100vw;
        height: 100vh;
        top:0;
        left:0%;
        background-color: rgba(0,0,0,0.4);
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .modal-area-cards .message {
        width:300px
    }
    .modal-area-cards .input {
        margin-top: 20px;
        width:150px;
        display: flex;
        align-items: center;
    }
    .modal-area-cards label {
        font-weight: 700;
        margin-right: 10px;
    }
    .modal-area-cards input  {
        width:100%;
        outline: none;
        padding:10px;
        font-size: 16px;
        font-weight: 600;
        color:#555;
        border:1px solid #888;
        border-radius: 5px;
    }

</style>