import { createRouter, createWebHistory } from 'vue-router'

import AdminLogin from '../views/Admin/AdminLogin.vue';
import AdminPanel from '../views/Admin/AdminPanel.vue';
import CardCreate from '../components/Admin/Cards/Create/CardCreate.vue';
import Cards from '../components/Admin/Cards/Cards.vue';
import UserBets from '../components/Admin/UserBets/UserBets.vue';
import UserRanking from '../components/Admin/UserRanking/UserRanking.vue';
import Account from '../components/Admin/Account/Account.vue';
import Custom from '../components/Admin/Custom/Custom.vue';

import Home from '../views/Public/Home.vue';
import UserCard from '../views/Public/UserCard.vue';
import PublicUserRanking from '../views/Public/PublicUserRanking.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
       
        {
            path:'/bolao/admin/login',
            name:'login',
            component: AdminLogin
        },
        {
            path:'/bolao/admin',
            redirect:'/bolao/admin/cards',
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
            path: '/bolao',
            name: 'home',
            component: Home,
        },
        {
            path: '/bolao/user-card/:code?',
            name: 'user-card',
            component: UserCard,
        },
        {
            path:'/bolao/ranking',
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
