<script>
    import UserCardViewer from '../../Public/UserCardViewer.vue';
    import UserCardEditor from './UserCardEditor.vue';
    import UserCardEditorSuccess from './UserCardEditorSuccess.vue';
    import UserBetsValidateConfirm from './UserBetsValidateConfirm.vue';
    import UserBetsValidateCardSuccess from './UserBetsValidateCardSuccess.vue';
    import UserCardDeleteConfirm from './UserCardDeleteConfirm.vue';
    

    export default {
        data() {
            return {
                userBets:null,
                pages:null,
                search:null,

                userCardViewerOpened:false,
                userCardViewerCard:null,

                userCardEditorOpened:false,
                userCardEditorCard:null,

                UserCardEditorSuccessOpened:false,
                userCardEditorSuccessCard:null,

                UserCardDeleteConfirmOpened:false,
                UserCardDeleteConfirmCard:null,


                validateConfirmOpened:false,
                validateConfirmCard:null,

                validateSuccessOpened:false,
                validateSuccessCard:null,
            }
        },
        computed: {
            dates: function() {
                let dates = [];
                this.userBets.forEach(bet => {
                    let d = new Date(bet.created_at);
                    dates.push(`${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()} ${d.getHours()}:${d.getMinutes()}`);
                });
                return dates;
            },
        },  
        methods:{
            openCard: function(card) {
                this.userCardViewerCard = card;
                this.userCardViewerOpened = true;
            },
            closeCard: function() {
                this.userCardViewerOpened = false;
                this.userCardEditorOpened = false;
                this.userCardEditorCard = null;
                this.userCardViewerCard = null;
            },
            openCardEditor: function(card) {
                this.userCardEditorCard = card;
                this.userCardEditorOpened = true;
            },  
            validateConfirm:function(card) {
                this.validateConfirmCard = card;
                this.validateConfirmOpened = true
            },
            validateCard:async function(id) {
                let body = new FormData();
                body.append('id', id);
                body = new URLSearchParams(body);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/validate`, {
                    method:'POST', //PUT
                    body: body,
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}` 
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.validateSuccessCard = json.card;
                    this.validateSuccessOpened = true;
                    
                    this.userBets.forEach(bet => {
                        if(bet.id == json.card.id) {
                            bet.validated = json.card.validated;
                        }
                    });
                }
            },
            openDeleteConfirm:function(card) {
                this.UserCardDeleteConfirmCard = card;
                this.UserCardDeleteConfirmOpened = true;
            },
            deleteUserCard:async function(id, bet) {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user-card/${id}`, {
                    method:'DELETE',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });
                
                let json = await request.json();
                
                if(json.status === 'success') {
                    this.userBets.splice(this.userBets.indexOf(bet), 1);
                }
            },
            makeSearch: async function () {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/search-user-card/${this.search}`, {
                    method:'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.userBets = json.user_cards;
                }
            },
            verifySearch:async function(e) {
                if(this.search.trim() === '') {
                    let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user-bets`, {
                        method:'GET',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('auth')}`,
                        }
                    });
                    
                    let json = await request.json();
                    if(json.status === 'success') {
                        this.userBets = json.user_bets.data;
                    }
                }
            },
            switchPage: async function(url) {
                let request = await fetch(url, {
                method:'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`,
                    }
                });
                
                let json = await request.json();
                if(json.status === 'success') {
                    this.pages = json.user_bets.links;
                    this.userBets = json.user_bets.data;

                }
            }
        },  
        components:{
            UserCardViewer,
            UserCardEditor,
            UserCardEditorSuccess,
            UserBetsValidateConfirm,
            UserBetsValidateCardSuccess,
            UserCardDeleteConfirm,
        },  
        async mounted() {
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/user-bets`, {
                method:'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`,
                }
            });
            console.log(request);
            let json = await request.json();
            if(json.status === 'success') {
                this.pages = json.user_bets.links;
                this.userBets = json.user_bets.data;

            }
        }
    }
</script>

<template>
    <div class="user-bets">
        <UserCardViewer v-if="userCardViewerOpened" :userCard="userCardViewerCard" />
        <UserCardEditor v-if="userCardEditorOpened" :userCard="userCardEditorCard"/>
        <UserCardEditorSuccess v-if="UserCardEditorSuccessOpened" :card="userCardEditorSuccessCard"/>
        <UserBetsValidateConfirm v-if="validateConfirmOpened" :card="validateConfirmCard" />
        <UserBetsValidateCardSuccess v-if="validateSuccessOpened" :card="validateSuccessCard" />
        <UserCardDeleteConfirm v-if="UserCardDeleteConfirmOpened"  :card="UserCardDeleteConfirmCard"/>

        <div class="search">
            <input @input="verifySearch" placeholder="Busca..." v-model="search" type="text">
            <button @click="makeSearch">Buscar</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Celular</th>
                    <th class="date">Data</th>
                    <th class="validated">Validado</th>
                    <th class="card-name">Cartela</th>
                    <th class="table-price">Preço</th>
                    <th style="width:256px">Opções</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="userBets" v-for="(bet, index) in userBets">
                    <td class="table-title t"><span>Usuario:</span>{{bet.name}}</td>
                    <td class="code"><span class="table-title">Código:</span>{{bet.code}}</td>
                    <td class="name"><span class="table-title">Nome:</span>{{bet.name}}</td>
                    <td class="phone"><span class="table-title">Celular:</span>{{bet.phone}}</td>
                    <td class="date"><span class="table-title">Data:</span>{{dates[index]}}</td>
                    <td class="validated"><span class="table-title">Validado:</span>{{bet.validated ? 'Sim' : 'Não'}}</td>
                    <td class="card-name"><span class="table-title">Cartela:</span>{{bet.card.name}}</td>
                    <td class="table-price"><span class="table-title">Preço:</span>R$ {{bet.card.price}}</td>
                    <td class="actions">
                        <button @click="openCard(bet)" class="open">Ver</button>
                        <button @click="validateConfirm(bet)" class="validate">Validar</button>
                        <button @click="openCardEditor(bet)" class="edit">Editar</button>
                        <button @click="openDeleteConfirm(bet)" class="delete">Excluir</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="paginate">
            <div 
            v-for="(page, index) in pages" class="page"
            @click="switchPage(page.url)"
            :class="page.active ? 'active' : null "
            >{{index === 0 ? 'Anterior' : index === pages.length-1 ? 'Próximo' : page.label}}
        </div>
        </div>
    </div>
</template>

<style>
    .user-bets table {
        border: 1px solid #ccc;
        width:calc(100vw - 200px);
        border-collapse: collapse;
    }
    .user-bets thead{
        background-color: var(--p-color);
        color: var(--s-color);
    }
    .user-bets tbody {
        font-weight: 600;
    }
    .user-bets table tbody tr:hover{
        background-color: #ddd;
    }
    .user-bets table th,
    .user-bets table td {
        padding: 12px 8px;
        border-bottom: 1px solid #ccc;
        border-right: 1px solid #ccc;;
    }
    .user-bets table th:last-child,
    .user-bets table td:last-child {
        border-right: none;
    }
    .user-bets table td span.table-title {
        margin-right: 10px;
        display:none;
    }
    .user-bets table td.table-title {
        display:none;
        background-color: #ccc;
    }
    .user-bets table td.code {
        width:auto;
        display:table-cell
    }
    .user-bets table td.name {
        width:150px
    }
    .user-bets table td.phone {
        width:130px
    }
    .user-bets table td.date {
        width:130px;
    }
    .user-bets table td.validated {
        width:10px
    }
    .user-bets table td.card-name {
        width:100px
    }
    .user-bets table td.table-price {
        width:60px;
    }

    .user-bets table td.actions {
        width:256px;
    }
    .user-bets table td.actions button {
        margin-right: 10px;
        border: none;
        padding: 6px 10px;
        border-radius: 5px;
        cursor: pointer;
    }
    .user-bets table td.actions button.open {
        background-color: var(--p-color);
        color: var(--s-color);
    }
    .user-bets table td.actions button.validate {
        background-color: #006ed4;
        color:#fff;
    }
    .user-bets table td.actions button.edit {
        background-color: #888;
        color:#fff;
    }
    .user-bets table td.actions button.delete {
        background-color: rgb(221, 37, 37);
        color:#fff;
    }
    .search {
        display:flex;
        align-items: center;
        position:relative;
        left:75%;
        margin-bottom: 10px;
    }
    .search input {
        padding: 6px 10px;
        outline: none;
        border:1px solid #888;
        border-radius: 6px;
    }
    .search button {
        background-color: var(--p-color);
        color: var(--s-color);
        cursor: pointer;
        padding: 8px 15px;
        border:none;
        margin-left: 10px;
        border-radius: 6px;
    }
    .paginate {
        display:flex;
        width:100%;
        justify-content: center;
        margin-top: 20px;
    }
    .page {
        border:1px solid #aaa;
        padding: 10px 15px;
        cursor: pointer;
        font-weight: 600;
        margin-right: 10px;
    }
    .page:hover {
        background-color: #ddd;
    }
    .page.active {
        background-color: var(--p-color);
        color: var(--s-color);
        border-color: var(--p-color);
    }
    
    @media (max-width:420px) {
        .user-bets table {
            border: 1px solid #ccc;
            width:100vw;
            margin-right: 0px;
            border-collapse: collapse;
        }
        .user-bets table td.code,
        .user-bets table td.name,
        .user-bets table td.phone,
        .user-bets table td.date,
        .user-bets table td.validated,
        .user-bets table td.card-name,
        .user-bets table td.table-price,
        .user-bets table td.actions {
            width:100%;
            display:flex;
        }
        .user-bets table td.table-title,
        .user-bets table td span.table-title {
            display:block;
        }
        .user-bets table tr {
            display:flex ;
            flex-direction: column;
            width:100%;
        }
        .user-bets table thead {
            display: none;
        }
        .search {
            display:flex;
            align-items: center;
            position:relative;
            left:10%;
            margin-bottom: 10px;
        }
        .page {
            border:1px solid #aaa;
            padding: 5px 10px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 5px;
        }
    }
</style>