<script>
import { RouterLink, RouterView } from 'vue-router';

    export default {
        data() {
          return {
            loggedUser:null,
            userCardCode:null,
            asset:import.meta.env.VITE_ASSET,
            base:import.meta.env.VITE_BASE_URL,
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
            }
        },
        async mounted() {
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/auth/validate`, {
            method:'GET',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth')}`
            }
            });
            let response = await request.json();

            if(response.authenticated) {
                this.loggedUser = response.user;
            }
        }
    }
</script>

<template>
  <header>
        <router-link to="/bolao">
            <img :src="`${asset}/assets/images/icon.png`" alt="incone bandeira do brasil" />
        </router-link> 
        
        <div class="title">Bolão da copa <span>2022</span></div>

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

<style scoped>
    header{
      display: flex;
      align-items: center;
      height: 70px;
      background-color: rgb(252, 252, 63);
    }
    header img {
      width:80px;
      margin-left: 30px;
    }
    .title {
      margin-left: 20px;
      font-family: Rubik Distressed;
      font-size: 25px;
      color: rgb(36, 36, 241);
    }
    .title span {
      color:#035528;
      margin-left: 5px;
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
        padding: 10px 20px;
        margin-right: 15px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: #069446;
        color:rgb(255, 238, 0);
        border:none;
        border-radius: 5px;
    }
    .home-button {
      padding: 10px 20px;
      margin-right: 15px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      background-color: #069446;
      color:rgb(255, 238, 0);
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
   
  @media (max-width: 420px) {
    header {
      width:100vw;
    }
    header img {
      width:60px;
      margin-left: 10px;
    }
    header .title {
        width:40%;
    }
    .login,
    .home-button{
      padding: 6px 10px;
      font-size: 15px;
    }
    .right {
      left:14%
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
