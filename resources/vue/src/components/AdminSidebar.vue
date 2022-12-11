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
            <router-link to="/admin/cards" @click="this.$parent.menuActive = 'cards'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'cards' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Minhas Cartelas
                </div>
            </router-link>

            <router-link to="/admin/card-create" @click="this.$parent.menuActive = 'card-create'">
                <div 
                  class="link" 
                  :style="this.$route.name === 'cardCreate' ? 'color:#1d1de6;box-shadow: 0px 0px 20px #1d1de6' : null"
                >
                    Criar Cartela
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
        background-color: #069446;
    }
    nav .menu-title {
        color:rgb(255, 238, 0);
        font-size: 20px;
        font-weight: 700;
        width:100%;
        text-align: center;
        padding-top: 15px;
    }
    nav.links {
        display: flex;
        flex-direction: column;
        text-align: center;
        width: 100%;
    }
    nav .link {
        width: 100%;
        padding:10px 0px;
        background-color: rgb(255, 238, 0);
        text-align: center;
        color:#000;   
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
    nav .link.active {
        color:#1d1de6;
        background-color: rgb(216, 202, 0);
    }
    .nav-mobile {
        display:none;
        position:absolute;
    }
    .nav-mobile div {
        width:30px;
        margin-bottom: 5px;
        height: 4px;
        background-color: #069446;
        border-radius: 2px;
    }
    nav .close{
        position:absolute;
        top:10px;
        left:10px;
        color:rgb(216, 202, 0);
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
    }
</style>