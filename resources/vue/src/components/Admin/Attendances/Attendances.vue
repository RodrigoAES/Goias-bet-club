<script>
    import Loading from '../../Loading.vue';

    export default {
        data() {
            return {
                attendances:[],
                filter:'all',

                attendant:'all',
                search:null,

                pagination:null,

                loadingAttendantReceipt:false,
            }
        },
        watch: {
            attendant(attendant) {
                if(attendant === 'all') {
                    this.allAttendances();
                } else {
                    this.attendantAttendances(this.attendant);
                }
            },
            filter() {
                if(this.attendant === 'all') {
                    this.allAttendances();
                } else {
                    this.attendantAttendances(this.attendant);
                }
            }
        },
        methods: {
            allAttendances:async function(page){
                let url = page ? page : `${import.meta.env.VITE_BASE_URL}admin/attendances`;
                url += this.filter === 'all' ? '' : `?filter=${this.filter}`;
                let request = await fetch(url, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.attendances = json.attendances.data;

                    json.attendances.links.pop();
                    json.attendances.links.shift();
                    this.pagination = {
                        pageNumber: json.attendances.last_page,
                        currentPage: json.attendances.current_page,
                        links: json.attendances.links,
                        nextPage: json.attendances.next_page_url,
                        prevPage: json.attendances.prev_page_url,
                    }
                }
            },
            attendantAttendances:async function(id, page) {
                let url = page ? page : `${import.meta.env.VITE_BASE_URL}admin/attendance/${id}`;
                url += this.filter === 'all' ? '' : `?filter=${this.filter}`;

                let request = await fetch(url, {
                    method:'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.attendances = json.attendances.data;

                    json.attendances.links.pop();
                    json.attendances.links.shift();
                    this.pagination = {
                        pageNumber: json.attendances.last_page,
                        currentPage: json.attendances.current_page,
                        links: json.attendances.links,
                        nextPage: json.attendances.next_page_url,
                        prevPage: json.attendances.prev_page_url,
                    }
                }
            },
            searchClient:async function() {
                let url = `${import.meta.env.VITE_BASE_URL}admin/search-client-attendance?q=${this.search}`;
                url += this.attendant != 'all' ? `&attendant=${this.attendant}` : '';
                let request = await fetch(url, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.attendances = json.attendances;
                }
            },
            attendantAttendancesPDF:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/attendant-attendances-pdf?filter=${this.filter}`, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let blob = await request.blob();
                let blobURL = URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = blobURL;

                let attendantName = this.$root.attendants.filter(attendant => attendant.id === this.attendant);
                attendantName = attendantName[0].name;

                link.download = `Atendimentos-${attendantName}.pdf`;

                document.body.appendChild(link);

                link.dispatchEvent(
                    new MouseEvent('click', { 
                        bubbles: true, 
                        cancelable: true, 
                        view: window 
                    })
                );
                document.body.removeChild(link);
                this.loadingPdf = false;
            }
        },
        components: {
            Loading,
        },  
        async mounted() {
            let interval = setInterval(()=>{
                if(this.$root.loggedUser) {
                    if(this.$root.loggedUser.level === 'admin' || this.$root.loggedUser.level === 'sub-admin') {
                        this.allAttendances();
                        clearInterval(interval);

                    } else if(this.$root.loggedUser.level === 'attendant') {
                        this.attendantAttendances(this.$root.loggedUser.id);
                        clearInterval(interval);
                    }
                }
            }, 1);
        }
    }
</script>

<template>
    <div id="attendances">
        <div class="title">Atendimentos</div>

        <div class="filter">
            <label for="filter">Filtro:</label>
            <select v-model="filter" name="filter" id="filter">
                <option value="all">Todos os atendimentos</option>
                <option value="payment">Somente pagamentos</option>
                <option value="doubt">Somente dúvidas</option>
            </select>
        </div>

        <div class="actions">
            <div class="attendant">
                <label for="attendant">Atendente:</label>
                <select v-model="attendant" name="attendant" id="attendant">
                    <option value="all">Todos os atendentes</option>
                    <option
                        v-if="(this.$root.attendants && this.$root.attendants.length > 0)"
                        v-for="attendant in this.$root.attendants"
                        :value="attendant.id"
                    >
                        {{attendant.name}}
                    </option>
                </select>
                <div 
                    v-if="
                        this.$root.loggedUser 
                        && ['admin', 'sub-admin'].includes(this.$root.loggedUser.level) 
                        && attendant != 'all'
                    " 
                    class="attendant-receipt"
                >
                
                    <button @click="attendantAttendancesPDF">
                        {{!loadingAttendantReceipt ? 'Gerar relatório' : ''}}
                        <Loading 
                            v-if="loadingAttendantReceipt"
                            :size="10"
                        />
                    </button>
                </div>
            </div>

            <div class="search">
                <input v-model="search" name="search" id="search" type="text" placeholder="Busca..." />
                <button @click="searchClient">Buscar</button>
            </div>

            
        </div>
        

        <table>
            <thead>
                <tr>
                    <th>Atendente</th>
                    <th>Cliente</th>
                    <th>Cel. cliente</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="attendance in attendances">
                    <td>{{attendance.attendant_name}}</td>
                    <td>{{attendance.client_name}}</td>
                    <td>{{attendance.client_phone}}</td>
                    <td>{{attendance.user_card_code}}</td>
                    <td>{{attendance.type === 'payment' ? 'Pagemento' : attendance.type === 'doubt' ? 'Dúvida' : null}}</td>
                    <td>{{attendance.date}}</td>
                </tr>
            </tbody>
        </table>

        <div v-if="pagination && pagination.pageNumber > 1" class="pagination">
            <div class="prev-page">
                <div 
                    v-if="pagination.prevPage" 
                    @click="
                        attendant === 'all' ? 
                        allAttendances(pagination.prevPage) : 
                        attendantAttendances(null, pagination.prevPage)
                    "
                    class="page"
                >
                        Anterior
                </div>
            </div>
            

            <div 
                v-for="page in pagination.links" 
                @click="
                    attendant === 'all' ? 
                    allAttendances(page.url) : 
                    attendantAttendances(null, page.url)
                "
                :class="page.active ? 'active' : null" 
                class="page"
            >
                {{page.label}}
            </div>

            <div class="next-page">
                <div 
                    v-if="pagination.nextPage"
                    @click="
                        attendant === 'all' ? allAttendances(pagination.nextPage) : 
                        attendantAttendances(null, pagination.nextPage)
                    "
                    class="page"
                >
                    Próxima
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    #attendances {
        width:100%;
    }
    #attendances .title {
        margin-bottom: 20px;
    }
    #attendances .filter {
        margin-bottom: 10px;
    }
    #attendances .filter label {
        font-weight: 600;
        margin-right: 6px;
    }
    #attendances .filter select {
        padding:4px 10px;
        font-size: 14px;
        outline: none;
        border:1px solid #888;
        border-radius: 5px;
    }
    #attendances .actions {
        display:flex;
        justify-content:space-between;
        align-items: center;
        width:100%;
    }
    #attendances .search {
        left:0;
    }
    #attendances .attendant {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    #attendances .attendant label {
        font-weight: 600;
        margin-right: 6px;
    }
    #attendances .attendant select {
        padding:4px 10px;
        font-size: 14px;
        outline: none;
        border:1px solid #888;
        border-radius: 5px;
    }
    #attendances .attendant button {
        font-size: 13px;
        padding: 8px 15px;
        margin-left: 7px;
        border: none;
        background-color: var(--p-color);
        color: var(--s-color);
        border-radius: 6px;
        cursor: pointer;
        width: 114px;
    }
    #attendances table {
        border:1px solid #aaa;
        border-collapse: collapse;
        width:100%;
        
    }
    #attendances table thead {
        background-color: var(--p-color);
        color:var(--s-color);
        font-weight: 700;
    }
    #attendances table tbody tr:hover{
        background-color: #ddd;
    }

    #attendances table tr {
        border-bottom: 1px solid #aaa;
    }
    #attendances table th,
    #attendances table td {
        padding:12px 8px;
        border-right: 1px solid #aaa;
        text-align: center;
        font-weight: 600;
    }
    #attendances table th:last-child,
    #attendances table td:last-child{
        border-right: none;
    }
    #attendances table th {
        font-weight: 700;
    }
    #attendances .next-page,
    #attendances .prev-page {
        width:100px;
        display: flex;
        justify-content: center;
    }

</style>