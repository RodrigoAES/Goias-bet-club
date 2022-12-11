<script>
    export default {
        props:['card'],
        computed:{
            date: function() {
                let d = new Date(this.card.created_at);
                return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()} ${d.getHours()}:${d.getMinutes()}`
            } 
        }
    }
</script>

<template>
    <div class="modal-area admin-card-viewer">
        <div class="success">
            <div class="card-info">
                <div class="title">Validado Com Sucesso!</div>
                <div class="info">Código: {{card.code}}</div>
                <div class="info">Nome: {{card.name}}</div>
                <div class="info">Celular: {{card.phone}}</div>
                <div class="info">Data: {{date}}</div>
                <div class="buttons">
                    <button 
                        @click="this.$parent.validateSuccessOpened = false"
                    >Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .success {
        background-color: #fff;
        padding: 40px;
        border-radius: 20px;
        border:4px solid #069446;
    }
    .success .title{
        color:#069446;
        margin-bottom: 20px;
    }
    .success .buttons button{
        margin-top: 20px;
        background-color: #069446;
        color: rgb(255, 238, 0);
        cursor: pointer;
    }
    .success .buttons button:hover {
        background-color: #09a750;
        transform: scale(1.1);
    }
    @media (max-width:420px) {
        .success {
            padding: 20px;
            width:280px;
            margin-left: -20px;
        }
    }
</style>