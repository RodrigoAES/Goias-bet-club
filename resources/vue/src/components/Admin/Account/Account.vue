<script>
    import AccountForm from './AccountForm.vue';
    import AccountSuccess from './AccountSuccess.vue';
    import AccountConfirm from './AccountConfirm.vue';
    import AccountPermissionsForm from './AccountPermissionsForm.vue';
   
    export default {
        data() {
            return {
                users:null,

                accountFormOpened:false,
                accountFormMethod:null,
                accountFormUser:null,

                accountSuccessOpened:false,
                accountSuccessData:null,
                accoutSuccessMessage:'',

                accountConfirmOpened:false,
                accountConfirmData:null,
                accountConfirmMessage:null,
                accountConfirmAction:null,

                accountPermissionFormopened:false,
            }
        },
         
        components: {
            AccountForm,
            AccountSuccess,
            AccountConfirm,
            AccountPermissionsForm
        }, 
        methods: {
            openCreate: function() {
                this.accountFormMethod = 'create';
                this.accountFormUser = null;
                this.accountFormOpened = true;
            },  
            openEdit: function(user) {
                this.accountFormMethod = 'edit';
                this.accountFormUser = user;
                this.accountFormOpened = true;
            }, 
            openDeleteConfirm: function(user) {
                this.accountConfirmData = user;
                this.accountConfirmAction = 'delete'
                this.accountConfirmMessage = 'Tem certeza que deseja excluir esta conta?';
                this.accountConfirmOpened = true;
            },
            openStatusConfirm:function(user) {
                this.accountConfirmData = user;
                this.accountConfirmAction = 'status'
                this.accountConfirmMessage = `Tem certeza que deseja ${user.active ? 'desativar' : 'ativar'} esta conta?`;
                this.accountConfirmOpened = true;
            },
            openAccountPermissions:function(user) {
                this.accountFormUser = user;
                this.accountPermissionFormopened = true
            }
        },
        async mounted() {
            let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/users`, {
                method:'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                }
            });

            let json = await request.json();

            if(json.status === 'success') {
                this.users = json.users;
            }
        }
    }
</script>

<template>
    <div id="account">
        <AccountForm 
            v-if="accountFormOpened"
            :method="accountFormMethod"
            :user="accountFormUser"
        />

        <AccountSuccess 
            v-if="accountSuccessOpened" 
            :data="accountSuccessData" 
            :message="accoutSuccessMessage"
        />

        <AccountConfirm 
            v-if="accountConfirmOpened"
            :data="accountConfirmData"
            :message="accountConfirmMessage"
            :action="accountConfirmAction"
        />

        <AccountPermissionsForm 
            v-if="accountPermissionFormopened"
            :user="accountFormUser"
        />
        <div id="users">
            <div class="title">Contas</div>
            <div class="new-user">
                <button 
                    v-if="
                        this.$root.loggedUser 
                        && this.$root.loggedUser.level === 'admin'
                    " 
                    @click="openCreate"
                >
                    Novo <span>+</span>
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Op????es</th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr
                        v-if="users"
                        v-for="(user, index) in users"
                    >
                        <td><span class="mobile-title">Nome</span>{{user.name}}</td>
                        <td><span class="mobile-title">Email</span>{{user.email}}</td>
                        <td class="actions">
                            <button 
                                v-if="(
                                    this.$root.loggedUser && this.$root.loggedUser.level === 'admin' 
                                    ||this.$root.loggedUser &&  this.$root.loggedUser.id === user.id
                                )"
                                @click="openEdit(user)" 
                                class="edit"
                            >Editar</button>

                            <button 
                                @click="openDeleteConfirm(user)"
                                v-if="(
                                    this.$root.loggedUser && this.$root.loggedUser.level === 'admin'
                                ) && user.level != 'admin'"
                                class="delete"
                            >Excluir</button>

                            <button
                                @click="openStatusConfirm(user)"
                                v-if="(
                                    this.$root.loggedUser && this.$root.loggedUser.level === 'admin' 
                                    ||this.$root.loggedUser &&  this.$root.loggedUser.id === user.id
                                ) && user.level != 'admin'" 
                                class='status'
                            >{{user.active ? 'Desativar' : 'Ativar'}}</button>

                            <button
                                @click="openAccountPermissions(user)"
                                v-if="(this.$root.loggedUser && this.$root.loggedUser.level === 'admin' )" 
                                class='permisions'
                            >Permiss??es</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
</template>

<style>
    #account {
        display:flex;
        width: 100%;
    }
    #users .new-user {
        width:100%;
        display: flex;
        justify-content: flex-end;
    }
    #users .new-user button {
        border: none;
        border-radius: 6px;
        padding:10px 20px;
        background-color: var(--p-color);
        color: var(--s-color);
        font-size: 16px;
        font-weight: 600;
        margin-top: 25px;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    #users .mew-user button span {
        font-size: 22px;
        margin-left: 5px;
    }
    #users table {
        border:1px solid #ccc;
    }
    #users table{
        border:1px solid #ccc;
        border-collapse: collapse;
        width:900px;
    }
    #users table thead{
        background-color: var(--p-color);
        color: var(--s-color);
        font-weight: 700;
    }
    #users table tr {
        border-bottom: 1px solid #ccc;
    }
    #users table td,
    #users table th {
        padding:10px 10px;
        border-right: 1px solid #ccc;
        
    }
    #users table td:last-child,
    #users table th:last-child {
        border-right: none;
        
    }
    #users table td {
        text-align: center;
    }
    #users table .actions {
        display: flex;
        justify-content: flex-start;
    }
    #users table .actions button {
        margin-right: 10px;
        border: none;
        padding: 6px 10px;
        border-radius: 5px;
        cursor: pointer;
    }
    #users table .actions .edit {
        background-color: #888;
        color:#fff;
    }
    #users table .actions .delete {
        background-color: rgb(221, 37, 37);
        color:#fff;
    }
    #users table .actions .status {
        background-color: #006ed4;
        color:#fff;
    }
    #users table .actions .permisions {
        background-color: #20af6c;
        color: #fff;
    }

    @media(max-width:420px) {
        #users table {
            width:90vw;
            border:none;
        }
        #users table thead {
            display:none;
        }
        #users table tr {
            display:flex;
            flex-direction: column;
            border:2px solid var(--p-color);
            margin-bottom: 10px;
            font-weight: 600;
        }
        #users table tr .mobile-title {
            font-weight: 700;
            color: var(--p-color)
        }
        #users table .actions {
            justify-content: center;
        }
    }   
</style>