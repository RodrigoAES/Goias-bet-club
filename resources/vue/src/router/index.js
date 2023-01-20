import { createRouter, createWebHistory } from 'vue-router'

import AdminLogin from '../views/Admin/AdminLogin.vue';
import AdminPanel from '../views/Admin/AdminPanel.vue';
import CardCreate from '../components/Admin/Cards/Create/CardCreate.vue';
import Cards from '../components/Admin/Cards/Cards.vue';
import UserBets from '../components/Admin/UserBets/UserBets.vue';
import UserRanking from '../components/Admin/UserRanking/UserRanking.vue';
import Account from '../components/Admin/Account/Account.vue';
import Custom from '../components/Admin/Custom/Custom.vue';
import Config from '../components/Admin/SiteConfig/Config.vue';
import Attendances from '../components/Admin/Attendances/Attendances.vue';
import Sales from '../components/Admin/Sales/Sales.vue';

import Home from '../views/Public/Home.vue';
import UserCard from '../views/Public/UserCard.vue';
import PublicUserRanking from '../views/Public/PublicUserRanking.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
       
        {
            path:'/bolaodefutebol/admin/login',
            name:'login',
            component: AdminLogin
        },
        {
            path:'/bolaodefutebol/admin',
            redirect:'/bolaodefutebol/admin/cards',
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
                    path:'attendances',
                    name:'attendances',
                    component: Attendances
                },
                {
                    path:'sales',
                    name:'sales',
                    component:Sales
                },
                {
                    path:'config',
                    name:'config',
                    component: Config,               
                },
                {
                    path:'account',
                    name:'account',
                    component:Account,
                }
            ]
        },
        
        {
            path: '/bolaodefutebol/:attendant?',
            name: 'home',
            component: Home,
        },
        {
            path: '/bolaodefutebol/user-card/:code?',
            name: 'user-card',
            component: UserCard,
        },
        {
            path:'/bolaodefutebol/ranking/:cardId?',
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
        || to.name === 'custom'
        || to.name === 'user-bets' 
        || to.name === 'ranking'
        || to.name === 'attendances'
        || to.name === 'sales'
        || to.name === 'config'
        || to.name === 'account'
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

        if(
            to.name === 'cardCreate'
            || to.name === 'config'
            || to.name === 'custom'
        ) {
            if(response.user.level === 'attendant') {
                return {name: from.name}
            }
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
