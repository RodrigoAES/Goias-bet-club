<script>
import { RouterLink, RouterView } from 'vue-router';


    export default {
        data() {
          return {
            loggedUser:null,

            userCardCode:null,

            asset:import.meta.env.VITE_ASSET,
            base:import.meta.env.VITE_BASE_URL,

            whatsappGroup:null,
            attendants:null,
            rules:[],

            logo:'',
            siteName:'',
            siteNameSecondColor:'',

            cardBackground:`url("${import.meta.env.VITE_ASSET}core/public/card_bg")`,

            primaryColor:'#069446',
            primaryColorHover:'#09a750',
            secundaryColor:'rgb(255, 238, 0)',
            thirdColor: '#45E421',
            nameColor1:'rgb(36, 36, 241)',
            nameColor2:'#035528',

            bonusTextColor1:'#000000',
            bonusTextColor2:'#000000',
          }
        },
        methods: {
            logout: async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/auth/logout`, {
                    method:'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    localStorage.removeItem('auth');
                    this.$router.push({name:'home'});
                }
            },
        },
        async beforeCreate() {
            // site config
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}config`, {
              method:'GET',
            });

            let json = await request.json();

            if(json.status === 'success') {
              this.whatsappGroup = json.whatsapp_group;
              this.attendants = json.attendants ? json.attendants : [];
              this.rules = json.rules ? json.rules : [];

              this.logo = json.logo;
              this.siteName = json.name;
              this.nameColor1 = json.name_color_1;
              this.nameColor2 = json.name_color_2;
              this.homeBackground = json.home_bg;

              this.primaryColor = json.p_color;
              this.secundaryColor = json.s_color;

              this.bonusTextColor1 = json.bonus_text_color_1;
              this.bonusTextColor2 = json.bonus_text_color_2;
            }

            this.root = document.documentElement;
            this.root.style.setProperty("--p-color", this.primaryColor);
            this.root.style.setProperty("--p-color-h", this.primaryColorHover);
            this.root.style.setProperty("--s-color", this.secundaryColor);
            this.root.style.setProperty("--t-color", this.thirdColor);
            this.root.style.setProperty("--name-color", this.nameColor1);
            this.root.style.setProperty("--name-color-s", this.nameColor2);
            this.root.style.setProperty("--titles-color", this.titlesColor);
            this.root.style.setProperty("--subtitles-color", this.subtitlesColor);
            this.root.style.setProperty("--bonus-text-color-1", this.bonusTextColor1);
            this.root.style.setProperty("--bonus-text-color-2", this.bonusTextColor2);

            // Admin validate user
            request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/auth/validate`, {
            method:'GET',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth')}`
            }
            });
            json = await request.json();

            if(json.authenticated) {
                this.loggedUser = json.user;
            }
        } 
    }
</script>

<template>
  <header>
        <router-link to="/">
            <img :src="`${asset}core/public/logo`" alt="logo" />
        </router-link> 
        <div class="title-main" v-html="siteName"></div>

        <div class="right" >
            <button 
            v-if="this.$route.name === 'home'"
                @click="this.$router.push({name:'login'})"
                class="login"
            >Login</button>

            <button 
                v-if="
                    this.$route.name === 'AdminPanel' 
                    || this.$route.name === 'cardCreate' 
                    || this.$route.name === 'cards' 
                    || this.$route.name === 'user-bets' 
                    || this.$route.name === 'ranking' 
                    || this.$route.name === 'attendances'
                    || this.$route.name === 'sales'
                    || this.$route.name === 'account'
                    || this.$route.name === 'custom'
                    || this.$route.name === 'login'
                "
                @click="this.$router.push({name:'home'})"
                class="home-button"
            >Home</button>   

            <button 
                v-if="
                    this.$route.name === 'AdminPanel' 
                    || this.$route.name === 'cardCreate' 
                    || this.$route.name === 'cards' 
                    || this.$route.name === 'user-bets' 
                    || this.$route.name === 'ranking'
                    || this.$route.name === 'attendances'
                    || this.$route.name === 'sales' 
                    || this.$route.name === 'account'
                    || this.$route.name === 'custom'
                "
                @click="logout"
                class="logout"
            >
                <img :src="`${asset}assets/images/logout.png`" alt="logout">  
            </button>
        </div>
  </header>

  <div id="app-body">
    <RouterView />
  </div>

</template>

<style >
    header{
      display: flex;
      align-items: center;
      height: 70px;
      background-color:var(--t-color);
      position: relative;
    }
    header a img {
      width: 60px;
      margin-left: 30px;
    }
    .title-main {
      margin-left: 20px;
      font-family: Open Sans;
      font-size: 30px;
      color: var(--name-color);
      font-weight: 700;
    }
    .title-main span {
      font-family: Rubik Spray Paint;
      color:var(--name-color-s);
      margin-left: 5px;
      font-weight: 500;
    }

    #app-body{
      min-height:calc(100vh - 70px);
      display: flex;
      justify-content: center;
    }

    .right {
      position: relative;
      left:60%;
      display: flex;
      align-items: center;
    }
    .login {
        padding: 8px 25px;
        margin-right: 15px;
        font-size: 20px;
        font-weight: 600;
        cursor: pointer;
        background-color: var(--p-color);
        color: var(--s-color);
        border:none;
        border-radius: 22% / 60%;
        box-shadow: 8px 5px 0px var(--s-color);
    }
    .home-button {
      padding: 10px 20px;
      margin-right: 15px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      background-color: var(--p-color);
      color: var(--s-color);
      border:none;
      border-radius: 5px;
      margin-left: -40px;
  }
  .logout {
    border:none;
    background-color: transparent;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    cursor: pointer;
  }
  .logout img {
    width:30px;
    margin-left: -0px;
  }

  .card {
    background-image: v-bind(cardBackground);
  }
   
  @media (max-width: 420px) {
    header {
      width:100vw;
    }
    header a img {
      width:60px;
      margin-left: 10px;
      
    }
    header .title-main {
        width:140px;
    }
    .login,
    .home-button{
      padding: 6px 10px;
      font-size: 15px;
      margin-left: 10px;
    }
    .home-button {
      margin-right: 5px;
    }
    .right {
      left:0%
    }
    #app-body{
      min-height:calc(100vh - 70px);
      max-width: 100vw;
      overflow-x: hidden;
      display: flex;
      justify-content: center;
    }
  }
</style>
