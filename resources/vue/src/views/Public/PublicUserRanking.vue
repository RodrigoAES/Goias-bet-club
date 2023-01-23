<script>
    import UserCardViewer from '../../components/Public/UserCardViewer.vue';
    export default {
        data() {
            return {
                cards:null,
                cardRanking:null,
                winners:null,
                pages:null,
                activeCard:null,
                UserCardViewerOpened:false,
                UserCardViewerData:null,
                message:null,

            }
        },
        methods:{
            
            requestRanking: async function(id) {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}ranking/${id}`,{
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await  request.json();
                
                if(json.status === 'success') {
                    this.cardRanking = json.ranked_user_cards;
                    this.winners = json.winners;
                    this.pages = json.pages;
                    this.activeCard = id;

                } else if(json.status === 'message') {
                    this.message = json.message;
                }
            },
            switchPage: async function(url) {
                let request = await fetch(url ,{
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await  request.json();
                
                if(json.status === 'success') {
                    this.cardRanking = json.ranked_user_cards;
                    this.pages = json.pages;
                }
            },
            openViewer: function(UserCard) {
                this.UserCardViewerData = UserCard;
                this.UserCardViewerOpened = true;
            },
            closeCard:function() {
                this.UserCardViewerData = null;
                this.UserCardViewerOpened = false;
            }
        },  
        components: {
            UserCardViewer,
        },
        async mounted() {
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}cards`, {
                method:'GET',
            });

            let json = await request.json();

            if(json.status === 'success') {
                this.cards = json.cards;
            }

            if(this.$route.params.cardId) {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}ranking/${this.$route.params.cardId}`,{
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await  request.json();
                
                if(json.status === 'success') {
                    this.cardRanking = json.ranked_user_cards;
                    this.winners = json.winners;
                    this.pages = json.pages;
                    this.activeCard = id;

                } else if(json.status === 'message') {
                    this.message = json.message;
                }
            }
        }
    }
</script>

<template>
    <div class="ranking public">
        <UserCardViewer v-if="UserCardViewerOpened" :userCard="UserCardViewerData"/>
        <div class="ranking-cards">
            <router-link to="/"><span>←</span> Voltar Para a pagina inical</router-link>
            <div 
                v-if="(cards != null)" 
                v-for="card in cards"
                @click="requestRanking(card.id)"
                class="ranking-card"
            >
                <div class="name">
                    <span>Nº: {{card.id}}</span>
                    <span>Nome: {{card.name}}</span>
                    <span>Encerramento: {{card.endtime}}</span>
                </div>
            </div>
        </div>

        <div class="ranking-users">
            <div class="title">Ranking</div>
            <div v-if="message" class="message">{{message}}</div>
            <div v-if="cardRanking" class="users">
                <table>
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Nome</th>
                            <th>Código</th>
                            <th>pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(card, index) in cardRanking" @click="openViewer(card)">
                            <td class="ranking-pos">{{(index+1)}}º</td>
                            <td class="ranking-name">{{card.name}}</td>
                            <td class="ranking-code"><span class="mobile-title">Código</span>{{card.code}}</td>
                            <td class="ranking-points"><span class="mobile-title">Pontos</span>{{card.points}}</td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="pages" class="pagination">
                    <div 
                        v-if="pages.previous"
                        @click="switchPage(pages.previous)" 
                        class="link"
                    >Anterior</div>

                    <div 
                        v-if="(pages.links.length > 1)"
                        v-for="(link, index) in pages.links" 
                        @click="switchPage(link.url)"
                        :class="link.active ? 'active' : null"
                        class="link"
                    >{{(index+1)}}</div>

                    <div 
                        v-if="pages.next"
                        @click="switchPage(pages.next)" 
                        class="link"
                    >Próxima</div>
                </div>

                <div class="winners">
                    <div class="title">Vencedores</div>
                    <div v-if="(winners && winners.length > 0)" class="users">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>pontos</th>
                                    <th>Premio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(card, index) in winners" @click="openViewer(card)">
                                    <td class="ranking-name">{{card.name}}</td>
                                    <td class="ranking-code"><span class="mobile-title">Código</span>{{card.code}}</td>
                                    <td class="ranking-points"><span class="mobile-title">Pontos</span>{{card.points}}</td>
                                    <td class="ranking-award"><span class="mobile-title">Preimio:</span>R${{card.award.toFixed(2)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>

<style>
    .ranking {
        width: 100%;
        display: flex;
    }
    .ranking.public {
        margin-left: 30px;
        margin-top: 30px;
    }
    .ranking a {
        padding: 10px 20px;
        font-size: 10px 14px;
        font-weight: 600;
        cursor: pointer;
        background-color:var(--p-color) ;
        color:var(--s-color);
        border:none;
        border-radius: 5px;
        margin-bottom:20px;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    .ranking a span{
        font-size: 22px;
        font-weight: 700;
    }
    .ranking-cards {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .ranking-card{
        font-weight: 600;
        color:var(--p-color);
        margin-bottom: 20px;
        width:300px;
        border: 2px solid var(--p-color);
        padding: 20px;
        cursor:pointer;
    }
    .ranking-card.active {
        background-color: var(--s-color);

    }
    .ranking-card .name {
        width:100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .users {
        width:100%;
    }
    .ranking-users {
        margin-left: 50px;
        width:100%;
    }
    .ranking-users .title {
        margin-bottom: 15px;
    }
    .ranking table{
        border:1px solid #ccc;
        border-collapse: collapse;
        width:700px;
    }
    .ranking table thead{
        background-color: var(--p-color);
        color:var(--s-color);
        font-weight: 700;
    }
    .ranking table tr {
        border-bottom: 1px solid #ccc;
    }
    .ranking table td,
    .ranking table th {
        padding:10px 10px;
        border-right: 1px solid #ccc;
        
    }
    .mobile-title {
        display:none;
    }
    .ranking table td:last-child,
    .ranking table th:last-child {
        border-right: none;
        
    }
    .ranking table td {
        text-align: center;
    }
    .ranking-pos,
    .ranking-name,
    .ranking-code,
    .ranking-phone,
    .ranking-points{
        font-weight: 600;
    }
    .ranking-pos {
        font-size: 30px;
        color:var(--s-color);
    }
    .ranking-actions button {
        padding: 5px 6px;
        font-weight: 600;
        background-color: var(--p-color);
        color:var(--s-color);
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .pagination {
        width:100%;
        margin-top: 20px;
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .pagination .link {
        border: 1px solid #ccc;
        padding: 5px 10px;
        font-weight: 600;
        margin-right: 5px;
    }
    .pagination .link.active {
        background-color: var(--p-color);
        color:var(--s-color);
        border: 1px solid var(--s-color);
    }
    .message {
        font-weight: 600;
        font-size: 16px;
    }
    @media(max-width: 420px) {
        .ranking {
            flex-direction: column;
        }
        .ranking.public {
            margin-left: 8px;
            margin-top: 10px;
        }
        .ranking-users {
            margin-left: -10px;
            
        }
        .ranking table {
            width:105%;
        }
        .ranking table thead{
            display: none;
        }
        .ranking table tr {
            display: flex;
            flex-direction: column;
            width:100%;
        }
        .ranking table td {
            border-right: none;
        }
        .mobile-title {
            display: block;
        }
        .ranking-users .title {
            width:100%;
            display: flex;
            justify-content: center;
        }
        .ranking table td.ranking-pos {
            background-color: var(--p-color);
            border:2px solid var(--s-color);
        }
    }
</style>