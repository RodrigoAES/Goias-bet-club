import { createRouter, createWebHistory } from 'vue-router'

import AdminLogin from '../views/AdminLogin.vue';
import AdminPanel from '../views/AdminPanel.vue';
import CardCreate from '../components/CardCreate.vue';
import Cards from '../components/Cards.vue';
import UserBets from '../components/UserBets.vue';
import UserRanking from '../components/UserRanking.vue';
import Account from '../components/Account.vue';
import Custom from '../components/Custom.vue';

import Home from '../views/Home.vue';
import UserCard from '../views/UserCard.vue';
import PublicUserRanking from '../views/PublicUserRanking.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
       
        {
            path:'/admin/login',
            name:'login',
            component: AdminLogin
        },
        {
            path:'/admin',
            redirect:'/admin/cards',
            name:'AdminPanel',
            component: AdminPanel,
            children: [
                {
                    path:'card-create',
                    name:'cardCreate',
                    component: CardCreate
                },
                {
                    path:'cards',
                    name:'cards',
                    component: Cards
                },
                {
                    path:'custom',
                    name:'custom',
                    component:Custom
                },
                {
                    path:'user-bets',
                    name:'user-bets',
                    component: UserBets,
                },
                {
                    path:'user-ranking',
                    name:'ranking',
                    component: UserRanking,
                }, 
                {
                    path:'account',
                    name:'account',
                    component:Account,
                }
            ]
        },
        
        {
            path: '/',
            name: 'home',
            component: Home,
        },
        {
            path: '/user-card/:code?',
            name: 'user-card',
            component: UserCard,
        },
        {
            path:'/ranking',
            name:'publicRanking',
            component: PublicUserRanking,
        }
        
    ]
});

router.beforeEach(async (to, from)=>{
   
    if(
        to.name === 'AdminPanel' 
        || to.name === 'cardCreate' 
        || to.name === 'cards' 
        || to.name === 'user-bets' 
        || to.nem === 'ranking'
    ) {
        let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/auth/validate`, {
            method:'GET',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth')}`
            }
        });
        let response = await request.json();
        
        if(! response.authenticated) {
            return {name: 'login'}
        }

    } else if(to.name === 'login') {
        let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/auth/validate`, {
            method:'GET',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth')}`
            }
        });
        let response = await request.json();

        if(response.authenticated) {
            return {name:'AdminPanel'}
        }
    }
});

export default router
