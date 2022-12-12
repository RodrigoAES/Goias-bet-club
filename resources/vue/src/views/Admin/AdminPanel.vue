<script>
    import AdminSidebar from '../../components/Admin/AdminSidebar.vue'

    export default {
        data() {
            return {
                matches:null,
                menuActive:'card-create',
            }
        },
        components:{
            AdminSidebar,
        }, 
        
    }
</script>

<template>
    <div id="panel">
        <div class="left">
            <AdminSidebar :menu="this.menuActive"/>
        </div>
        <div class="content">
            <RouterView />
        </div>
    </div>
</template>

<style>
    #panel {
        min-width: 100vw;
        min-height: 100vh;
        display: flex;
    }
    .left{
        width:150px;
    }
   
    .content {

    }
</style>