<script>
    
</script>

<template>
    <div class="modal-area admin-card-viewer">
        <div class="confirm">
            <div class="message">Ranking publicado com sucesso!</div>
            <div class="buttons">
                <button 
                    @click="this.$parent.publicRankingConfirmOpened = false" 
                    class="confirm"
                >Confirmar</button>
            </div>
        </div>
    </div>
</template>

<style>
    .confirm {
        background-color: #fff;
        padding: 40px;
        border: 4px solid #069446;
        border-radius: 20px;
    }
    .message {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .confirm .buttons{
        margin-top: 40px;
    }
    .confirm .buttons button.confirm {
        background-color: #069446;
        color: rgb(255, 238, 0);
        cursor: pointer;
        margin-right: 30px;
    }
    .confirm .buttons button.cancel {
        background-color: #ddd;
        color: #000;
        cursor: pointer;
        border:1px solid #000;
    }
    @media(max-width:420px) {
        .confirm {
            width:240px;
            margin-left: -20px;
        }
        .confirm .buttons button.confirm {
            margin-right: 10px;
            width:auto
        }
    }
</style>