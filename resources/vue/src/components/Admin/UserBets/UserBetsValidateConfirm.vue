<script>
    export default {
        props:['card'],
        methods:{

        },
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
        <div class="confirm">
            <div class="message">Tem certeza que deseja validar está cartela?</div>
            <div class="card-info">
                <div class="info">Código: {{card.code}}</div>
                <div class="info">Nome: {{card.name}}</div>
                <div class="info">Celular: {{card.phone}}</div>
                <div class="info">Data: {{date}}</div>
            </div>
            <div class="buttons">
                <button 
                    @click="this.$parent.validateCard(card.id); this.$parent.validateConfirmOpened = false" 
                    class="confirm"
                >Confirmar</button>

                <button 
                    @click="this.$parent.validateConfirmOpened = false"
                    class="cancel"
                >Cancelar</button>
            </div>
        </div>
    </div>
</template>

<style>
    .confirm {
        background-color: #fff;
        padding: 40px;
        border: 4px solid var(--p-color);
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
        background-color: var(--p-color);
        color: var(--s-color);
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