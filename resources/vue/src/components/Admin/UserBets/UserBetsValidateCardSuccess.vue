<script>
    import { isMemberExpressionBrowser } from '@vue/compiler-core';
import Loading from '../../Loading.vue';

    export default {
        props:['card'],
        data() {
            return {
                loading:false,
            }
        },
        computed:{
            date: function() {
                let d = new Date(this.card.created_at);
                return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()} ${d.getHours()}:${d.getMinutes()}`
            } 
        },
        methods:{
            generateReceipt:async function() {
                this.loading = true;
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user-receipt/${this.card.id}`, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let blob = await request.blob();
                let blobURL = URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = blobURL;
                link.download = `Comprovante-${this.card.code}-${this.card.name}.pdf`

                document.body.appendChild(link);

                link.dispatchEvent(
                    new MouseEvent('click', { 
                        bubbles: true, 
                        cancelable: true, 
                        view: window 
                    })
                );

                document.body.removeChild(link);
                this.loading = false;
            }
        },
        components: {
            Loading
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

                    <button @click="generateReceipt">
                        <span v-if="!loading">Gerar comprovante</span>
                        <loading v-if="loading" :size="30" />
                    </button>
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
        border:4px solid var(--p-color);
    }
    .success .title{
        color: var(--p-color);
        margin-bottom: 20px;
    }
    .success .buttons button{
        margin-top: 20px;
        background-color: var(--p-color);
        color: var(--s-color);
        cursor: pointer;
        margin-right: 20px;
    }
    .success .buttons button:hover {
        background-color: var(--p-color);
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