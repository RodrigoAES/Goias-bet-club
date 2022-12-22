<script>
    export default {
        props:['team'],
        data() {
            return{
                fromDate:null,
                toDate:null,

                error:null,
            }
        },
        methods: {
            requestTeamMatchs:async function() {
                if(this.fromDate && this.toDate) {
                    let url = `${import.meta.env.VITE_BASE_URL}admin/api-football/team-matchs/${this.team}/0/${this.fromDate}|${this.toDate}`;

                    let request = await fetch(url, {
                        method:'GET',
                        headers:{
                            'Authorization': `Bearer ${localStorage.getItem('auth')}`
                        }
                    });

                    let json = await request.json();

                    if(json.sttatus === 'success') {
                        this.$parent.matchs = json.matchs;
                        this.$parent.teamMatchsFormOpened = false;
                    }

                } else {
                    this.error = 'É necessario preencher as datas ou pesquisar no modo ocorrendo.';
                }
                
            }
        }
    }
</script>

<template>
    <div class="modal-area">
        <div id="team-matchs-form">
            <div @click="this.$parent.teamMatchsFormOpened = false" class="close">✖</div>
            <div class="title">Pesquisar partidas</div>

            <div class="match-form-api">
                <div style="margin:10px" v-if="error" class="danger">{{error}}</div>

                <label >
                    De:
                    <input v-model="fromDate" type="date">
                    Até:
                    <input v-model="toDate" type="date">
                </label>

                <div class="actions">
                    <button @click="requestTeamMatchs">Pesquisar Partidas</button>
                </div>
               
            </div>
        </div>
    </div>
</template>

<style>
#team-matchs-form {
    background-color: #fff;
    padding:20px;
    display:flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    border:4px solid var(--p-color);
    border-radius: 10px;
    width:350px;
}
#team-matchs-form .close {
    position: relative;
    top:-10px;
    left: 95%;
}
#team-matchs-form .title {
    width:100%;
}
#team-matchs-form .method-select {
    margin-bottom: 20px;
    font-weight: 600;
}
#team-matchs-form .method-select select {
    padding:4px;
    font-size: 14px;
    border:1px solid #888;
    border-radius: 5px;
    outline: none;
}
#team-matchs-form .match-form-api {
    display: flex;
    flex-direction: column;
    width: 100%;
}
#team-matchs-form .match-form-api label {
   font-weight: 600;
}
#team-matchs-form .match-form-api label input {
    font-size: 14px;
    padding: 4px;
    border:1px solid #888;
    border-radius: 5px;
    outline: none;
} 
#team-matchs-form .match-form-api input[type=number] {
    width:50px;
}
#team-matchs-form .match-form-api .actions {
    width:100%;
    display: flex;
    justify-content: center;
} 
#team-matchs-form .match-form-api button {
    padding:10px 20px;
    margin-top: 40px;
    background-color: var(--p-color);
    color: var(--s-color);
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}
#team-matchs-form .match-form-api button:hover {
    background-color: var(--p-color-h);
    transform: scale(1.1);
}
</style>