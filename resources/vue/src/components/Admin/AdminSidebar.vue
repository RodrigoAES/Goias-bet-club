<script>
    export default {
        props:['menu'],
        methods:{
            openNav:function() {
                let nav = document.querySelector('nav')
                nav.style.left = '0%';

                nav.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        nav.style.left = '-110%';
                    });
                });
            },
            closeNav:function () {
                let nav = document.querySelector('nav');
                nav.style.left = '-110%';
            }
        }
    }
</script>

<template>
    <div @click="openNav" class="nav-mobile">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <nav>
        <div class="close"><span @click="closeNav">✖</span></div>
        <div class="menu-title">Menu</div>
        
        <div class="links">
            <router-link to="/admin/cards" @click="this.$parent.menuActive = 'cards'" >
                <div 
                  class="link" 
                  :style="this.$route.name === 'cards' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Meus Bolões
                </div>
            </router-link>

            <router-link to="/admin/card-create" @click="this.$parent.menuActive = 'card-create'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'cardCreate' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Criar Bolão
                </div>
            </router-link>

            <router-link to="/admin/custom" @click="this.$parent.menuActive = 'cuustom'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'custom' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Customizado
                </div>
            </router-link>

            <router-link to="/admin/user-bets" @click="this.$parent.menuActive = 'user-bets'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'user-bets' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Apostas feitas
                </div>
            </router-link>

            <router-link to="/admin/user-ranking" @click="this.$parent.menuActive = 'user-rankinf'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'ranking' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Ranking
                </div>
            </router-link>

            <router-link to="/admin/attendances" @click="this.$parent.menuActive = 'attendances'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'attendances' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Atendimentos
                </div>
            </router-link>

            <router-link to="/admin/sales" @click="this.$parent.menuActive = 'sales'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'sales' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Vendas
                </div>
            </router-link>

            <router-link to="/admin/config" @click="this.$parent.menuActive = 'config'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'config' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Configurações
                </div>
            </router-link>

            <router-link to="/admin/account" @click="this.$parent.menuActive = 'account'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'account' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Contas
                </div>
            </router-link>
        </div>
    </nav>
</template>

<style>
    nav {
        width:100%;
        height: 100%;
        background-color: var(--t-color);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    nav .menu-title {
        background-color: var(--p-color);
        border-radius: 10% / 50%;
        color: var(--s-color);
        font-size: 20px;
        font-weight: 700;
        width:80%;
        text-align: center;
        margin-top: 15px;
        box-shadow: 5px 5px 0px var(--s-color);
    }
    
    nav .link {
        width: 100%;
        padding:5px 2px;
        background-color: var(--p-color);
        border-radius: 10% / 50%;
        display: flex;
        justify-content: center;
        color:#000;   
        box-shadow: 5px 5px 0px var(--s-color);
        height: 20px;
        margin-left: -4px;
    }
    nav .link:hover {
        background-color: rgb(216, 202, 0);
    }
    
    nav a {
        text-decoration: none;
        margin:10px;
        margin-left: 20px;
        font-weight: bold;
        width:100%;
    }
    .nav-mobile {
        display:none;
        position:absolute;
    }
    .nav-mobile div {
        width:30px;
        margin-bottom: 5px;
        height: 4px;
        background-color: var(--s-color);
        border-radius: 2px;
    }
    nav .close{
        position:absolute;
        top:10px;
        left:10px;
        color:var(--s-color);
        display:none;

    } 

    @media (max-width:420px) {
        nav {
            position: absolute;
            z-index: 30;
            left:-110%;
            transition: all ease 0.5s;
        }
        .nav-mobile {
            display:block;
            left:8px;
            top:78px
        }
        nav .close{
            display:block
        }
        nav .links {
            width: 60%;
        }
        nav .menu-title {
            border-radius: 5% / 50%;
        }
        nav .link {
            border-radius: 8% / 50%;
        }
        
    }
</style>