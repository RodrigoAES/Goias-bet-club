<script>
    import League from './League.vue';

    export default {
        props:['country', 'filter'],
        data() {
            return {
                optionsOpened:false,

                leagues:null,
            }
        },
        methods: {
            optionsToggle:function (countryCode) {
                if(!this.optionsOpened) {
                    this.optionsOpened = true;
                    setTimeout(()=>{
                        let options = document.querySelector(`.country[data-code="${countryCode}"] .country-options`);
                        options.style.height = 'auto';

                        let height = `${options.offsetHeight}px`;
                        options.style.height = '0px';
                        
                        setTimeout(()=>{
                            options.style.height = height;
                        }, 10);


                    }, 1);
                    
                } else if(this.optionsOpened) {
                    let options = document.querySelector(`.country[data-code="${countryCode}"] .country-options`);
                    options.style.height = '0px';

                    setTimeout(()=>{
                        this.optionsOpened = false
                    }, 1000);
                }
            },
            OpenCloseLeagues:function(countryCode) {
                let country = document.querySelector(`.country[data-code="${countryCode}"]`);
                let leagues = country.querySelector('.leagues');
                if(leagues.style.height && leagues.style.height === 'auto') {
                    let height = `${leagues.offsetHeight}px`;
                    leagues.style.height = height;
                    setTimeout(()=>{
                        leagues.style.height = '45px';
                    }, 1);
                    
                    leagues.querySelector('.label img').style.transform = 'rotate(0deg)';

                } else {
                    let country = document.querySelector(`.country[data-code="${countryCode}"]`);
                    let leagues = country.querySelector('.leagues');

                    leagues.style.height = `auto`;
                    let height = `${leagues.offsetHeight}px`;
                    console.log(height);
                    leagues.style.height = '45px';
                    setTimeout(()=>{
                        leagues.style.height = height;
                        setTimeout(()=>{
                            leagues.style.height = 'auto';
                        }, 1100);
                    }, 10);
                    leagues.querySelector('.label img').style.transform = 'rotate(180deg)';
                }
            },
            requestCountryLeagues:async function(countryCode) {
                this.optionsOpened = false
                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/api-football/country-leagues/${this.country.code}/${this.$parent.APIFootballSearchFilter === 'all' ? 'false' : ''}`, {
                    method:'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();
                
                if(json.status === 'success') {
                    this.leagues = json.leagues;

                    setTimeout(()=>{
                        let country = document.querySelector(`.country[data-code="${countryCode}"]`);
                        country.querySelector('.leagues').style.height = 'auto';
                    }, 1);
                }
            },
            addMatch:function(match) {
                this.$emit('addMatch', match);
            },
            
        },
        components: {
            League,
        }
    }
</script>

<template>
    <div :data-code="country.code" class="country">
        <div class="up">
            <div class="info">
                <div class="flag">
                    <img :src="country.flag" />
                </div>
                <div class="name">{{country.name}}</div>
            </div>
            
            
            <div class="options">
                <div @click="optionsToggle(country.code)" class="open">
                    Opções
                    <img :src="`${this.$root.asset}assets/icons/down-arrow.png`" alt="">
                </div>
            </div>
        </div>

        <div v-if="optionsOpened" class="country-options">
            <span @click="requestCountryLeagues(country.code)">Ver ligas deste país</span>
        </div>

        <div v-if="leagues && leagues.length > 0" class="leagues">
            <div @click="OpenCloseLeagues(country.code)" class="label">Ligas: <span><img :src="`${this.$root.asset}assets/icons/down-arrow.png`"></span></div>
            <League
                v-for="league in leagues"
                :league="league"
                :filter="filter"
                @addMatch="addMatch"
            />
        </div>
       
    </div>
</template>

<style>
    .country {
        display:flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        border: 2px solid #000;
    }
    .country .up {
        display:flex;
        align-items: center;
        flex-direction: row;
        justify-content: space-between;
        width:90%;
    }
    .country .up .info {
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }
    .country .flag {
        width:40px;
        height:40px;
        border-radius: 10px;
        overflow: hidden;
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .country .flag img {
        max-width: 100%;
        max-height:100%;
    }
    .country .name {
        font-weight: 600;
        margin-left: 10px;
    }
    .country .options {
        position: relative;
        display:flex;
        align-items: center;
        font-weight: 600;
        cursor: pointer;
    }
    .options .open img {
        width:16px;
        margin-bottom: -4px;
    }
    .country-options {
        display: flex;
        flex-direction: column;
        background-color: #FFFFFF;
        font-weight: 600;
        font-size: 14px;
        border:1px solid #000;
        width:100%;
        z-index: 50;
        height:0px;
        overflow-y: hidden;
        transition: all ease 1s;
    }
    .country-options span {
        margin-bottom: 3px;
        padding: 10px;
        cursor: pointer;
    }
    .country-options span:hover {
        background-color: #ddd;
    }
    .country .leagues {
        height: 0px;
        overflow-y: hidden;
        transition: all ease 2s;
    }
    .country .leagues .label {
        font-weight: 600;
        margin-top: 15px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    .country .leagues .label img {
        width:15px;
        margin-left: 5px;
        margin-top: 4px;
        transform: rotate(180deg);
    }
</style>