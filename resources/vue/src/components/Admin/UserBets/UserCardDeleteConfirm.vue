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
    <div class="modal-area admin-card-viewer">
        <div class="confirm delete">
            <div class="close"><span>✖</span></div>
            <div class="message">Tem certeza que deseja excluir a cartela?</div>
            <div class="buttons">
                <button 
                    @click="this.$parent.deleteUserCard(card.id, card); this.$parent.UserCardDeleteConfirmOpened = false"
                >Excluir</button>
                <button 
                    @click="this.$parent.UserCardDeleteConfirmOpened = false" 
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