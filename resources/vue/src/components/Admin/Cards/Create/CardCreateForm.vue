<script>
    export default {
        props:['error'],
        data() {
            return {
                endtime:null,
                price:null,
                type:null,
                hostPercentage:null,
                bonus:null,
                valuation:null,
                name:null,
            }
        },
        methods:{
            datetimeMask:function(e) {
                if(e.target.value.length === 2 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value}/`;

                } else if(e.target.value.length === 5 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value}/`;

                } else if(e.target.value.length === 10 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value} `;

                } else if(e.target.value.length === 13 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value}:`;

                } else if(e.target.value.length >= 17 && e.inputType != 'deleteContentBackward') {
                    e.target.value = `${e.target.value.substring(0,16)}`;
                }
            },
            hostPercentMask:function(e) {
                if(e.target.value.length > 3) {
                    e.target.value = e.target.value.substring(0,3);
                    this.hostPercentage = e.target.value;
                }
                if(parseInt(e.target.value) < 0) {
                    e.target.value = 0;
                    this.hostPercentage = e.target.value;
                }
                if(parseInt(e.target.value) > 100) {
                    e.target.value = 100;
                    this.hostPercentage = e.target.value;
                }
            }
        }
    }
</script>

<template>
    <div class="modal-area-create-card">
        <div id="cardCreateForm">
            <div class="close"><span @click="this.$parent.closeForm()">✖</span></div>
            <div class="title">Cartela</div>
            

            <div v-if="error.matchs != null || typeof error === 'string'" class="alert-danger danger" >
                {{error}}
            </div>

            <label for="name">Nome da cartela</label>
            <div v-if="error.name != null" v-for="error in error.name" class="danger">
                {{error}}
            </div>
            <input 
                v-model="name" 
                type="text" 
                name="name"
                :style="error.name != null ? 'border-color:rgb(221, 37, 37)' : null"
            />

            <label for="type">Tipo</label>
            <select v-model="type" name="type" class="select-type">
                <option value="common">Comum</option>
                <option value="detailed">Detalhada</option>
            </select>

            <label for="host_percentage">Porcentagem do organizador:</label>
            <div class="host_percentage">
                <input 
                    v-model="hostPercentage" 
                    @input="hostPercentMask"
                    name="host_percentage" 
                    type="number" 
                />
                %
            </div>

            <label for="bonus">Bônus da cartela:</label>
            <div class="bonus">
                R$ <input v-model="bonus" name="bonus" type="number">
            </div>
            
            <label for="award-valuation">Estimativa de prêmio:</label>
            <div class="award-valuation">
                R$ <input v-model="valuation" name="award-valuation" type="number">
            </div>
            
            <label for="endtime">Data de encerramento:</label>
            <div v-if="error.endtime != null" v-for="error in error.endtime" class="danger">
                {{error}}
            </div>

            <input 
                v-model="endtime" 
                type="datetime-local" 
                name="endtime" 
                @input="datetimeMask"
                :style="error.endtime != null ? 'border-color:rgb(221, 37, 37)' : null"
            />
            
            <label for="price">Preço:</label>
            <div v-if="error.price" v-for="error in error.price" class="danger">
                {{error}}
            </div>
            <div class="price">
               <span>R$</span>
               <input 
                    v-model="price" 
                    type="number" 
                    name="price" 
                    :style="error.price != null ? 'border-color:rgb(221, 37, 37)' : null"
                />
            </div>

            <button @click="this.$parent.createCard(endtime, price, name, type, hostPercentage, bonus, valuation)">Criar bolão</button>
        </div>
    </div>
    
</template>

<style>
    .modal-area-create-card {
        position: fixed;
        z-index: 99;
        background-color: rgba(0,0,0,0.4);
        margin-top: -110px;
        margin-left: 30px;
        width:100vw;
        height: 100vh;
        display:flex;
        justify-content: center;
        align-items: flex-start;
        overflow-y: scroll;
        
    }
    #cardCreateForm {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: #fff;
        border:5px solid var(--p-color);
        border-radius: 20px;
        margin: 20px 0px;

    }
    #cardCreateForm .title {
        width: 100%;
        display: flex;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    #cardCreateForm .close {
        position: relative;
        border-radius: 50%;
    }
    #cardCreateForm .close span {
        position: absolute;
        top:-5%;
        left:3%;
        font-size: 22px;
        font-weight: 600;
        color:#069446;
        cursor: pointer;
    }
    #cardCreateForm label {
        font-weight: 600;
        margin-top: 15px;
    }
    #cardCreateForm .select-type {
        padding: 10px;
        outline: none;
        border: 1px solid #888;
        border-radius: 8px;
    }
    #cardCreateForm .host_percentage {
        font-size: 20px;
        font-weight: 700;
    }
    #cardCreateForm .host_percentage input{
        font-weight: 600;
    }
    #cardCreateForm .host_percentage input::-webkit-inner-spin-button, 
    #cardCreateForm .host_percentage input::-webkit-outer-spin-button {
        -webkit-appearance: none; 
        display: none;
    }
    #cardCreateForm .price {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    #cardCreateForm .price span {
        font-weight: 500;
        margin-right: 4px;
    }
    #cardCreateForm input {
        margin-top: 5px;
        font-size: 16px;
        outline: none;
        border:1px solid #888;
        border-radius: 8px;
        padding: 10px 10px;
    }
    #cardCreateForm input[type=number] {
        width:50px;
    }
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    #cardCreateForm .bonus,
    #cardCreateForm .award-valuation {
        font-size: 20px;
        font-weight: 500;
    }
    #cardCreateForm .bonus input[type=number],
    #cardCreateForm .award-valuation input[type=number]{
        width:100px
    }
    #cardCreateForm button {
        border: none;
        border-radius: 6px;
        padding:10px 20px;
        background-color: var(--p-color);
        color: var(--s-color);
        font-size: 16px;
        font-weight: 600;
        margin-top: 25px;
        cursor: pointer;
    }
    #cardCreateForm button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }
    .danger {
        color:rgb(221, 37, 37);
    }
    .alert-danger {
        border: 2px solid rgb(221, 37, 37);
    }

    @media (max-width:420px) {
        #cardCreateForm .close span{
            left:10%;
        }
    }
</style>