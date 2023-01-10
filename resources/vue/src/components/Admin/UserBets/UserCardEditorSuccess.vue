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
                <div class="title">Salvo Com Sucesso!</div>
                <div class="info">Código: {{card.code}}</div>
                <div class="info">Nome: {{card.name}}</div>
                <div class="info">Celular: {{card.phone}}</div>
                <div class="info">Data: {{date}}</div>
                <div class="buttons">
                    <button 
                        @click="this.$parent.UserCardEditorSuccessOpened = false"
                    >Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>

</style>