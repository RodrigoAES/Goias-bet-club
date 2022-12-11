<script>
    export default {
        props:['data', 'message']
    }
</script>

<template>
    <div class="modal-area-users">
        <div class="success-info account">
            <div class="title-success">Sucesso!</div>
            <div class="message">{{message}}</div>
            <div class="close"><span @click="this.$parent.closeSuccess()">✖</span></div>
            <div class="info name"><span>Nome:</span>{{data.name}}</div>
            <div class="info phone"><span>Email:</span>{{data.email}}</div>
            <div class="buttons-success">
            <button @click="this.$parent.accountSuccessOpened = false">Confirmar</button>
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

    @media (max-width:420px) {
        .success-info.account {
            width:280px;
            margin-top: 50px;
            margin-left: -10px;
        }
    }
</style>