<script>
    export default {
        data() {
            return {
                sales:null,

                filter:'30',
                attendant:'all',
                search:null,
                pagination:null,
            }
        },
        methods:{
            allSales:async function(page) {
                let url = page ? 
                `${page}&filter=${this.filter}` : 
                `${import.meta.env.VITE_BASE_URL}admin/sales?filter=${this.filter}`;

                let request = await fetch(url, {
                method:'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.sales = json.sales.data;

                    json.sales.links.pop();
                    json.sales.links.shift();
                    this.pagination = {
                        pageNumber: json.sales.last_page,
                        currentPage: json.sales.current_page,
                        links: json.sales.links,
                        nextPage: json.sales.next_page_url,
                        prevPage: json.sales.prev_page_url,
                    }
                }
            },
            attendantSales:async function() {
                let url = `${import.meta.env.VITE_BASE_URL}admin/attendant-sales/`;
                url += this.attendant;
                url += `?filter=${this.filter}`;

                let request = await fetch(url, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.sales = json.sales.data;
                    json.sales.links.pop();
                    json.sales.links.shift();
                    this.pagination = {
                        pageNumber: json.sales.last_page,
                        currentPage: json.sales.current_page,
                        links: json.sales.links,
                        nextPage: json.sales.next_page_url,
                        prevPage: json.sales.prev_page_url,
                    }
                }
            },
            searchSale:async function() {
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/sales/search?q=${this.search}`, {
                    method:'GET',
                    headers:{
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();

                if(json.status === 'success') {
                    this.sales = json.sales;
                }
            },
            attendantSalesPDF: async function() {
                let url = `${import.meta.env.VITE_BASE_URL}admin/sales/attendant-sales-pdf/${this.attendant}`;
                url += this.filter ? `?filter=${this.filter}` : '';

                let request = await fetch(url, {
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

                link.download = `Vendas-${attendantName}.pdf`;

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
        watch:{
            attendant(value) {
                if(value === 'all') {
                    this.allSales();
                } else {
                    this.attendantSales();
                }
            },
            filter() {
                if(this.attendant === 'all') {
                    this.allSales();
                } else {
                    this.attendantSales();
                }
            }
        },  
        async mounted() {
            this.allSales();
        }
    }
</script>

<template>
    <div id="sales">
        <div class="title">Vendas</div>

        <div class="filter">
            <label for="filter">Filtro:</label>
            <select v-model="filter" name="filter" id="filter">
                <option value="all">Todas as datas</option>
                <option value="15">Últimos 15 dias</option>
                <option value="30">Últimos 30 dias</option>
                <option value="45">Últimos 45 dias</option>
                <option value="60">Últimos 60 dias</option>
                <option value="120">Últimos 120 dias</option>
            </select>
        </div>

        <div class="actions">
            <div class="attendant">
                <label for="attendant">Atendente:</label>
                <div class="container">
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

                    <div v-if="attendant != 'all'" class="attendant-receipt">
                        <button @click="attendantSalesPDF">
                            {{!loadingAttendantReceipt ? 'Gerar relatório' : ''}}
                            <Loading 
                                v-if="loadingAttendantReceipt"
                                :size="10"
                            />
                        </button>
                    </div>
                </div>
            </div>

            <div class="search">
                <input v-model="search" name="search" id="search" type="text" placeholder="Busca..." />
                <button @click="searchSale">Buscar</button>
            </div>

            
        </div>

        <table>
            <thead>
                <tr>
                    <th>Atendente</th>
                    <th>Cliente</th>
                    <th>Telefone</th>
                    <th>Código</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="sales" v-for="sale in sales">
                    <td class="mobile-tr-title"><span>Venda</span>{{sale.id}}</td>
                    <td><span class="mobile-title">Atendente:</span>{{sale.attendant_name ?? 'Nenhum'}}</td>
                    <td><span class="mobile-title">Cliente:</span>{{sale.name}}</td>
                    <td><span class="mobile-title">Telefone:</span>{{sale.phone}}</td>
                    <td><span class="mobile-title">Código:</span>{{sale.code}}</td>
                    <td><span class="mobile-title">Valor:</span>{{sale.value}}</td>
                </tr>
            </tbody>
        </table>    
        
        <div v-if="pagination && pagination.pageNumber > 1" class="pagination">
            <div class="prev-page">
                <div 
                    v-if="pagination.prevPage" 
                    @click="
                        attendant === 'all' ? 
                        allSales(pagination.prevPage) : 
                        attendantSales(null, pagination.prevPage)
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
                    allSales(page.url) : 
                    attendantSales(null, page.url)
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
                        attendant === 'all' ? 
                        allSales(pagination.nextPage) : 
                        attendantSales(null, pagination.nextPage)
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
#sales {
    width: 100%;
}
#sales .title {
    margin-bottom: 20px;
}
#sales .filter {
    margin-bottom: 10px;
}
#sales .filter label {
    font-weight: 600;
    margin-right: 6px;
}
#sales .filter select {
    padding:4px 10px;
    font-size: 14px;
    outline: none;
    border:1px solid #888;
    border-radius: 5px;
}
#sales .actions {
    display:flex;
    justify-content:space-between;
    align-items: center;
    width:98%;
}
#sales .search {
    left:0;
}
#sales .attendant {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}
#sales .attendant .container {
    display: flex;
}
#sales .attendant label {
    font-weight: 600;
    margin-right: 6px;
}
#sales .attendant select {
    padding:4px 10px;
    font-size: 14px;
    outline: none;
    border:1px solid #888;
    border-radius: 5px;
}
#sales .attendant button {
    font-size: 13px;
    font-weight: 500;
    padding: 8px 15px;
    margin-left: 7px;
    border: none;
    background-color: var(--p-color);
    color: var(--s-color);
    border-radius: 6px;
    cursor: pointer;
    width: 114px;
    outline:none;
}
#sales table {
    border:1px solid #aaa;
    border-collapse: collapse;
    width:98%;
}
#sales table thead {
    background-color: var(--p-color);
    color:var(--s-color);
    font-weight: 700;
}
#sales table tbody tr:hover{
    background-color: #ddd;
}

#sales table tr {
    border-bottom: 1px solid #aaa;
}
#sales table th,
#sales table td {
    padding:12px 8px;
    border-right: 1px solid #aaa;
    text-align: center;
    font-weight: 600;
}
#sales table .mobile-tr-title {
    display:none;
    background-color: var(--s-color);
    color:var(--p-color);
}
#sales table .mobile-tr-title span {
    margin-right: 10px;
}
#sales table th:last-child,
#sales table td:last-child{
    border-right: none;
}
#sales table th {
    font-weight: 700;
}
#sales .next-page,
#sales .prev-page {
    width:100px;
    display: flex;
    justify-content: center;
}
#sales .next-page,
#sales .prev-page {
    width:100px;
    display: flex;
    justify-content: center;
}

@media(max-width:420px) {
    #sales {
        width:100vw;
    }
    #sales .title {
        margin-top: 20px;
        width:100%;
        text-align: center;
    }
    #sales .actions {
        justify-content:flex-start;
        align-items: flex-start;
        width:100%;
        flex-direction: column;
    }
    #sales table {
        width:82vw;
    }
    #sales table thead {
        display:none;
    }
    #sales table tr {
        display: flex;
        flex-direction: column;
        border:1px solid #000;
    }
    #sales table .mobile-title {
        display: block;
        margin-right: 20px;
    }
    #sales table .mobile-tr-title {
        display:block;
    }
    #sales table td {
        display:flex;
    }
    #sales .attendant {
        display:flex;
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>