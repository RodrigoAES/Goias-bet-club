<script>
    export default {
        props:['response'],
        computed: {
            date: function () {
                let date = new Date(this.response.created_at);
                return `${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
                
            }
        }
    }
</script>

<template>
    <div class="modal-area-bet">
        <div class="success-info">
            <div class="title-success">Sucesso!</div>
            <div class="message">Guarde o código da cartela para consultar!</div>
            <div class="close"><span @click="this.$parent.closeSuccess()">✖</span></div>
            <div class="info code"><span>Código da cartela:</span>{{response.code}}</div>
            <div class="info name"><span>Nome:</span>{{response.name}}</div>
            <div class="info phone"><span>Celular:</span>{{response.phone}}</div>
            <div class="info created_at"><span>Data:</span>{{date}}</div>
            <div class="buttons-success">
            <button @click="this.$parent.toUserCard(response.code); this.$parent.betSuccessOpened = false">Ver Cartela</button>
            </div>
        </div>
    </div>
</template>

<style>
    .success-info {
        background-color: #fff;
        padding: 30px;
        border: 4px solid #069446;
        border-radius: 20px;
        position: relative;
    }
    .info {
        margin-bottom: 20px;
        font-weight: 700;
    }
    .info span {
        color: #069446;
        margin-right: 10px;
    }
    .title-success {
        color: #069446;
        font-size: 26px;
        font-weight: 700;
        width: 100%;    
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    .close {
        position: absolute;
        left:90%;
        top:4%;
        font-size: 22px;
        font-weight: 700;
        cursor: pointer;
    }
    .buttons-success {
        width:100%;
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
    .buttons-success button{
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: #069446;
        color:rgb(255, 238, 0);
        border:none;
        border-radius: 5px;
        margin: auto;
    }
    .buttons-success button:hover {
        background-color: #09a750;
        transform: scale(1.1);
    }
    .message {
        margin-bottom: 6px;
    }
</style>