<script>
    export default {
        props:['user'],
        data() {
            return {
                paymentPermission: this.user.permissions.payment_permission ? true : false,
                doubtPermission: this.user.permissions.doubt_permission ? true : false,
                validatePermission: this.user.permissions.validate_permission ? true : false,
            }
        },
        methods: {
            updateAccountPermissions:async function() {
                let body = new FormData();
                body.append('payment_permission', this.paymentPermission ? 1 : 0);
                body.append('doubt_permission', this.doubtPermission ? 1 : 0);
                body.append('validate_permission', this.validatePermission ? 1 : 0);

                body = new URLSearchParams(body);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/account/permissions/${this.user.id}`, {
                    method: 'PUT',
                    body: body,
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.$parent.users[this.$parent.users.indexOf(this.user)].permissions = {
                        payment_permission: json.attendant.payment_permission,
                        doubt_permission: json.attendant.doubt_permission,
                        validate_permission: json.attendant.validate_permission
                    }

                    this.$parent.accountPermissionFormopened = false;
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div id="account-permissions">
            <div 
                @click="this.$parent.accountPermissionFormopened = false"
                class="close"
            >
                <span>✖</span>
            </div>

            <div class="title">Permissões para: {{user.name}}</div>

            <div class="payment">
                <label for="payment">Receber pagamentos:</label>
                <input v-model="paymentPermission" type="checkbox" name="payment_permission" id="payment_permission" />
            </div>

            <div class="doubt">
                <label for="doubt">Tirar dúvidas:</label>
                <input v-model="doubtPermission" type="checkbox" name="doubt_permission" id="doubt_permission" />
            </div>   
           
            <div class="validate">
                <label for="validate">Válidar cartelas:</label>
                <input v-model="validatePermission" type="checkbox" name="validate_permission" id="validate_permission" />
            </div> 

            <button @click="updateAccountPermissions">Salvar</button>
        </div>
    </div>
</template>

<style>
    #account-permissions {
        background-color: #fff;
        border:4px solid var(--p-color);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding:20px;
        width:320px
    }
    #account-permissions .close {
        position: relative;
        top:-15px;
        left:50%
    }
    #account-permissions .payment,
    #account-permissions .doubt,
    #account-permissions .validate {
        display: flex;
        align-items: center;
        width:100%;
    }   
    #account-permissions label {
        font-weight: 600;
    }
    #account-permissions input[type=checkbox] {
        width:20px;
        height:20px;
        margin-left: 10px;
    }
    #account-permissions button {
        padding: 10px 20px; 
        font-size: 16px;
        font-weight: 600;
        border:none;
        border-radius: 6px;
        margin-top: 20px;
        background-color: var(--p-color);
        color: var(--s-color);
        cursor: pointer;
    }
    #account-permissions button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
</style>