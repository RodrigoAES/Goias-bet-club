<script>
    export default {
        props:['card'],
        data() {
            return {

            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div class="confirm">
            <div class="message">Tem certeza que deseja excluir a cartela?</div>
            <div class="buttons">
                <button @click="this.$parent.deleteCard(card, card.id); this.$parent.confirmOpened = false">Excluir</button>
                <button @click="this.$parent.confirmOpened = false">Cancelar</button>
            </div>
            
        </div>
    </div>
    
</template>

<style>
    .modal-area {
        position: fixed;
        z-index: 99;
        background-color: rgba(0,0,0,0.4);
        margin-top: -110px;
        margin-left: 22px;
        width:100vw;
        height: 100vh;
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .confirm {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: #fff;
        border:5px solid var(--p-color);
        border-radius: 20px;

    }
    .confirm .message {
        width: 100%;
        display: flex;
        justify-content: center;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 10px;
    }
    .confirm .close {
        position: relative;
        border-radius: 50%;
    }
    .confirm .close span {
        position: absolute;
        top:-65px;
        left:96%;
        font-size: 22px;
        font-weight: 600;
        color: var(--p-color);
        cursor: pointer;
    }

    .confirm button {
        border: none;
        border-radius: 6px;
        padding:10px 20px;
        background-color: var(--p-color);
        color: var(--s-color);
        font-size: 16px;
        font-weight: 600;
        margin-top: 25px;
        cursor: pointer;
        margin-right: 20px;
    }
    .confirm button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
    .confirm button:last-child {
        border: 1px solid #888;
        border-radius: 6px;
        padding:10px 20px;
        background-color: #ccc;
        color:#000;
        font-size: 16px;
        font-weight: 500;
        margin-top: 25px;
        cursor: pointer;
    }
    .confirm button:last-child:hover {
        background-color: #ddd;
        transform: scale(1.0);
    }
    
    @media (max-width:420px) {
        .modal-area {
            margin-top: -110px;
            margin-left: -2px;
        }
        .confirm {
            padding:20px;
            width:calc(100vw - 100px)
        }
    }
   
</style>