<script>
    export default {
        data() {
            return {
                whatsappGroup: this.$root.wathsappGroup ?? null,

                rules: this.$root.rules ? this.$root.rules : null,

                logo: this.$root.logo ? this.$root.logo : null,
                siteName : this.$root.siteName ? this.$root.siteName : null,
                siteNameSecondColor:null,
                nameColor1: this.$root.nameColor1 ? this.$root.nameColor1 : null,
                nameColor2: this.$root.nameColor2 ? this.$root.nameColor2 : null,

                homeBackground: this.$root.homeBackground ? this.$root.homeBackground : null,
                homeContentBackground: this.$root.homeContentBackground ? this.$root.homeContentBackground : null,
                rulesBackground: this.$root.rulesBackground ? this.$root.rulesBackground : null,

                primaryColor: this.$root.primaryColor ? this.$root.primaryColor : null,
                secundaryColor: this.$root.secundaryColor ? this.$root.secundaryColor : null,
                thirdColor: this.$root.thirdColor ? this.$root.thirdColor : null,

                bonusTextColor1:this.$root.bonusTextColor1 ? this.$root.bonusTextColor1 : null,
                bonusTextColor2:this.$root.bonusTextColor2 ? this.$root.bonusTextColor2 : null,
            }
        },
        methods:{
            openRules:function(e) {
                let rules = e.currentTarget.parentElement;

                if(rules.style.height === '20px') {
                    rules.style.height = '320px';
                    rules.style.overflowY = 'scroll';

                    setTimeout(()=>{
                        rules.querySelector('.add-rule').style.top = 
                            `${
                                rules.offsetTop + 331
                            }px`;
                        rules.querySelector('.add-rule').style.left = 
                            `${
                                rules.offsetLeft + 
                                ((rules.offsetWidth / 2) -40)
                            }px`;    
                        rules.querySelector('.add-rule').style.display = 'flex';
                    }, 1000);
                    

                    e.currentTarget.querySelector('img').style.transform = 'rotate(180deg)';

                } else if(rules.style.height === '320px') {
                    rules.style.height = '20px';
                    rules.style.overflowY = 'hidden';
                    rules.querySelector('.add-rule').style.display = 'none';
                    e.currentTarget.querySelector('img').style.transform = 'rotate(0deg)';
                }
            }, 
            openDoubtPhones:function(e) {
                let doubtPhones = e.currentTarget.parentElement;
                if(doubtPhones.style.height === '20px') {
                    doubtPhones.style.height = '150px';
                    doubtPhones.style.overflowY = 'scroll';
                    doubtPhones.style.marginBottom = '50px'

                    setTimeout(()=>{
                        doubtPhones.querySelector('.add-phone').style.top = 
                            `${
                                doubtPhones.offsetTop + 155
                            }px`;
                        doubtPhones.querySelector('.add-phone').style.left = 
                            `${
                                doubtPhones.offsetLeft + 
                                ((doubtPhones.offsetWidth / 2) -40)
                            }px`;    
                        doubtPhones.querySelector('.add-phone').style.display = 'flex';
                    }, 1000);
                    

                    e.currentTarget.querySelector('img').style.transform = 'rotate(180deg)';

                } else if(doubtPhones.style.height === '150px') {
                    doubtPhones.style.height = '20px';
                    doubtPhones.style.overflowY = 'hidden';
                    doubtPhones.style.marginBottom = '0px'
                    doubtPhones.querySelector('.add-phone').style.display = 'none';
                    e.currentTarget.querySelector('img').style.transform = 'rotate(0deg)';
                }
            },
            addRule:function() {
                this.rules.push('');
            },
            updateConfig:async function() {
                let body = new FormData();

                body.append('wathsapp_group', this.whatsappGroup);

                // Rules
                let rules = document.querySelectorAll('.rules .rule');
                rules.forEach(rule => {
                    if(rule.innerText.trim() != '') {
                        body.append('rules[]', rule.innerText);
                    }
                });

                // Logo
                let logo = document.querySelector('#logo').files[0]
                if(logo) {
                    body.append('logo', logo);
                }
                
                // site name
                if(this.siteNameSecondColor) {
                    let name = `${this.siteName} <span>${this.siteNameSecondColor}</span>`;
                    body.append('name', name);
                } else {
                    body.append('name', this.siteName);
                }
                
                body.append('name_color_1', this.nameColor1);
                body.append('name_color_2', this.nameColor2);

                //Backgrounds
                let homeBg = document.querySelector('#home-bg-file').files[0];
                if(homeBg) {
                    body.append('home_bg', homeBg);
                }

                let homeContentBg = document.querySelector('#home-content-bg-file').files[0];
                if(homeContentBg) {
                    body.append('home_content_bg', homeContentBg);
                }

                let rulesBg = document.querySelector('#rules-bg-file').files[0];
                if(rulesBg) {
                    body.append('rules_bg', rulesBg);
                }

                let cardBg = document.querySelector('#card-bg-file').files[0];
                if(cardBg) {
                    body.append('card_bg', cardBg);
                }

                // Colors
                body.append('p_color', this.primaryColor);
                body.append('s_color', this.secundaryColor);
                body.append('t_color', this.thirdColor);

                // Bonus
                let bonus_bg = document.querySelector('#bonus_bg_file').files[0];
                if(bonus_bg) {
                    body.append('bonus_bg_image', bonus_bg);
                }
                body.append('bonus_text_color_1', this.bonusTextColor1);
                body.append('bonus_text_color_2', this.bonusTextColor2);

                let request = await fetch(`${import.meta.env.VITE_BASE_URL}admin/options`, {
                    method:'POST',
                    body: body,
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth')}`
                    }
                });

                let json = await request.json();
                if(json.status === 'success') {
                    this.$router.go();
                }
            }
        },
        mounted() {
            document.getElementById('site-config').style.display = 'none';
            setTimeout(()=>{
                this.rules = this.$root.rules;

                this.logo = this.$root.logo;

                if(this.$root.siteName && this.$root.siteName.indexOf('<span>') > -1) {
                    let name = this.$root.siteName.split('<span>');
                    name[1] = name[1].split('</span>');
                    name[1].pop();
                    
                    this.siteName = name[0];
                    this.siteNameSecondColor = name[1][0];

                } else {
                    this.siteName = this.$root.siteName;
                }
                
                this.nameColor1 = this.$root.nameColor1;
                this.nameColor2 = this.$root.nameColor2;
                this.homeBackground = this.$root.homeBackground;

                this.primaryColor = this.$root.primaryColor;
                this.secundaryColor = this.$root.secundaryColor;
                this.thirdColor = this.$root.thirdColor;

                document.getElementById('site-config').style.display = 'block';
            }, 3000);
        }
    }
</script>

<template>
    <div id="site-config">
        <div class="title">Configurações do site</div>

        <div class="wathsapp-group">
            <label for="wathsapp-group">Grupo do Whatsapp:</label>
            <textarea v-model="whatsappGroup"
                name="wathsapp-group" 
                id="wathsapp-group" 
                cols="30" 
                rows="4" 
                placeholder="Cole aqui o link do grupo"
            ></textarea>
        </div>

        <div class="rules" style="height:20px">
            <div @click="openRules" class="label">
                <span>Regulamentação</span> 
                <img :src="`${this.$root.asset}assets/icons/down-arrow.png`" />
            </div>
            
            <div 
                v-if="(rules && rules.length > 0)" 
                v-for="(rule, index) in rules"
                class="rule-container"
            >   
                <span>{{index+1}}-</span>
                <div class="rule" contenteditable="true">
                    {{rule}}
                </div>
            </div>

            <div class="add-rule">
                <span @click="addRule">+</span>
            </div>
        </div>

        <div
            style="margin-top:50px" 
            class="title"
        >
            Personalização do site
        </div>

        <div class="logo-opt">
            
            <div class="input-area">
                <div class="label">Logo:</div>
                <label id="file" for="logo">
                    Selecionar Imagem 
                    <img :src="`${this.$root.asset}assets/icons/image.png`"/>
                </label>
                <input style="display: none;" id="logo" type="file">
            </div>
            
        </div>

        <div class="site-name">
            <div class="label">Nome/Titulo Do site:</div>
            <div class="name">
                <label for="site-name-1">Primeira cor:</label>
                <input v-model="siteName" type="text" name="site-name-1">
                <input v-model="nameColor1" type="color">
            </div>

            <div class="name">
                <label for="site-name-2">Segunda cor:</label>
                <input v-model="siteNameSecondColor" type="text" name="site-name-2">
                <input v-model="nameColor2" type="color">
            </div>
        </div>

        <div class="home-bg">
            <div class="input-area">
                <div class="label">Imagem de fundo da Home:</div>
                <label for="home-bg-file">
                    Selecionar imagem
                    <img :src="`${this.$root.asset}assets/icons/image.png`"/>
                </label>
            </div>

            <div class="image-area">
                <img :src="homeBackground" alt="">
            </div>
            
            <input id="home-bg-file" style="display:none" type="file" accept="image/jpeg,image/png">
        </div>

        <div class="home-content-bg">
            <div class="input-area">
                <div class="label">Imagem de fundo do conteúdo:</div>
                <label for="home-content-bg-file">
                    Selecionar imagem
                    <img :src="`${this.$root.asset}assets/icons/image.png`"/>
                </label>
            </div>
            
            <input id="home-content-bg-file" style="display:none" type="file" accept="image/jpeg,image/png">
        </div>

        <div class="rules-bg">
            <div class="input-area">
                <div class="label">Imagem de fundo do regulamento:</div>
                <label for="rules-bg-file">
                    Selecionar imagem
                    <img :src="`${this.$root.asset}assets/icons/image.png`"/>
                </label>
            </div>
            
            <input id="rules-bg-file" style="display:none" type="file" accept="image/jpeg,image/png">
        </div>

        <div class="card-bg">
            <div class="input-area">
                <div class="label">Imagem de fundo da cartela:</div>
                <label for="card-bg-file">
                    Selecionar imagem
                    <img :src="`${this.$root.asset}assets/icons/image.png`"/>
                </label>
            </div>
            
            <input id="card-bg-file" style="display:none" type="file" accept="image/jpeg,image/png">
        </div>

        <div class="site-colors">
            <div class="label">Cores do site:</div>
            <div class="site-color">
                <label for="p-color">Cor Primaria:</label>
                <input v-model="primaryColor" type="color" />
            </div>

            <div class="site-color">
                <label for="s-color">Cor Secundaria:</label>
                <input v-model="secundaryColor" type="color" />
            </div>

            <div class="site-color">
                <label for="t-color">Cor tercearia:</label>
                <input v-model="thirdColor" type="color" />
            </div>
        </div>

        <div class="bonus">
            <div class="label">Mensagem de Bonus e estimativa de prêmio:</div>

            <div class="bonus_bg_image">
                <div class="input-area">
                    <div class="label-soft">Selecionar imagem de fundo:</div>
                    <label for="bonus_bg_file">
                        Selecionar imagem
                        <img :src="`${this.$root.asset}assets/icons/image.png`"/>
                        <input id="bonus_bg_file" type="file" style='display:none'/>
                    </label>
                </div>
            </div>

            <div class="bonus_text_color_1">
                <label for="bonus_text_color_1">Primeira cor do texto:</label>
                <input v-model="bonusTextColor1" type="color" name="bonus_text_color_1" />
            </div>

            <div class="bonus_text_color_2">
                <label for="bonus_text_color_2">Segunda cor do texto:</label>
                <input v-model="bonusTextColor2" type="color" name="bonus_text_color_2">
            </div>
        </div>

        <div class="button-save">
            <button @click="updateConfig">Salvar</button>
        </div>
        
    </div>
</template>

<style>
    #site-config {
        width:100%;
        height: auto;
    }
    #site-config .title {
        margin-bottom: 30px;
    }
    #site-config .wathsapp-group {
        display: flex;
        flex-direction: column;
    }
    #site-config .wathsapp-group label {
        font-size: 18px;
        font-weight: 700;
    }
    #site-config .wathsapp-group textarea {
        width:400px;
        resize: none;
        font-size: 14px;
        outline: none;
        border:1px solid #888;
        border-radius: 4px;
        margin-top: 5px;
    }
    #site-config .rules {
        display:flex;
        flex-direction: column;
        margin-top: 20px;
        height: 20px;
        width:370px;
        border:2px solid #888;
        padding: 15px;
        overflow-y: hidden;
        transition: all ease 1s;
    }
    #site-config .rules::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 4px;
        background-color: #fff;
        border-radius: 10px;
    }
    #site-config .rules::-webkit-scrollbar-thumb {
        background-color: #aaa;
        color: #aaa;
        border-radius: 10px;
        cursor: pointer;
    }
    #site-config .rules::-webkit-scrollbar-thumb:hover{
        background-color: #888;
    }
    #site-config .rules .label{
        font-weight: 600;
        display: flex;
        align-items: center;
        cursor: pointer;
        margin-bottom: 25px;
    }
    #site-config .rules .label img {
        width:15px;
        margin-left: 5px;
    }
    #site-config .rules .rule-container {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    #site-config .rules .rule-container span {
        font-weight: 600;
        font-size: 20px;
        width: 20px;
        margin-right: 5px;
    }
    #site-config .rules .rule {
        width: 300px;
        height: 100px;
        border:1px solid #888;
        outline: none;
        resize: none;
        font-size: 14px;
        padding: 10px;
        overflow-y: scroll;
    }
    #site-config .rules .rule::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 4px;
        background-color: #fff;
        border-radius: 10px;
    }
    #site-config .rules .rule::-webkit-scrollbar-thumb {
        background-color: #aaa;
        color: #aaa;
        border-radius: 10px;
        cursor: pointer;
    }
    #site-config .rules .rule::-webkit-scrollbar-thumb:hover {
        background-color: #888;
    }
    #site-config .rules .add-rule,
    #site-config .phones-doubt-service .add-phone{
        position: absolute;
        display: flex;
        justify-content: center;
        display: none;

    }
    #site-config .rules .add-rule span,
    #site-config .phones-doubt-service .add-phone span {
        font-size: 40px;
        font-weight: 600;
        color: #888;
        width: 50px;
        height: 50px;
        border:4px solid #888;
        border-radius: 50%;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    #site-config .phones-doubt-service .phone {
        margin-bottom:15px
    }
    #site-config .input-area {
        display:flex;
        align-items: center;
    }
    #site-config .input-area .label{
        font-weight: 700;
        font-size: 18px;
        margin-right: 10px;
    }
    #site-config .input-area #file {
        border: 3px solid #888;
        padding: 10px;
        font-weight: 600;
        color:#555;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    #site-config .input-area #file img  {
        width: 30px;
        margin-left: 10px;
    }
    #site-config .site-name {
        margin-top: 40px;
    }
    #site-config .site-name .label {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    #site-config .site-name .name {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    #site-config .site-name label {
        font-weight: 600;
        margin-right: 5px;
    }
    #site-config .site-name .name label[for=site-name-1] {
        margin-right: 9px;
    }
    #site-config .site-name input[type=text] {
        font-size: 16px;
        font-weight: 600;
        color:#555;
        padding:10px;
        border:1px solid #888;
        border-radius: 4px;
        width:300px;
        outline: none;
    }
    #site-config .site-name input[type=color] {
        height: 40px;
    }
    #site-config .home-bg,
    #site-config .home-content-bg,
    #site-config .rules-bg,
    #site-config .card-bg{
        margin-top: 30px;
        display: flex;
        align-items: center;
    }
    #site-config .home-bg .label,
    #site-config .home-content-bg .label,
    #site-config .rules-bg .label,
    #site-config .card-bg .label {
        font-weight: 700;
        font-size: 18px;
        margin-right: 5px;
    }
    #site-config .home-bg label,
    #site-config .home-content-bg label,
    #site-config .rules-bg label,
    #site-config .card-bg label {
        border: 3px solid #888;
        padding: 10px;
        font-weight: 600;
        color:#555;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    #site-config .home-bg label img,
    #site-config .home-content-bg label img,
    #site-config .rules-bg label img,
    #site-config .card-bg label img {
        width: 30px;
        margin-left: 10px;
    }
    #site-config .site-colors {
        margin-top: 40px;
    }
    #site-config .site-colors .label {
        font-weight: 700;
        font-size: 18px;
    }
    #site-config .site-colors .site-color {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width:180px;
    }
    #site-config .site-colors label {
        font-weight: 600;
        margin-right: 5px;
    }
    #site-config .site-colors label[for=p-color] {
        margin-right: 28px;
    }
    #site-config .bonus {
        margin-top: 40px;
    }
    #site-config .bonus .label {
        font-size: 18px;
        font-weight: 700;
    }
    #site-config .bonus .bonus_bg_image {
        margin-top: 15px;
    }
    #site-config .bonus .label-soft {
        font-weight: 600;
    }
    #site-config .bonus label[for=bonus_bg_file] {
        border: 3px solid #888;
        padding: 10px;
        font-weight: 600;
        color:#555;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin-left: 10px;
    }
    #site-config .bonus label[for=bonus_bg_file] img {
        width: 30px;
        margin-left: 10px;
    }

    #site-config .bonus .bonus_text_color_1,
    #site-config .bonus .bonus_text_color_2 {
        font-weight: 600;
        margin-top: 10px;
    }
    #site-config .bonus .bonus_text_color_1 input[type=color] {
        margin-left: 10px;
    }
    #site-config .bonus .bonus_text_color_2 input[type=color] {
        margin-left: 6px;
    }
    #site-config button {
        margin-top: 30px;
        margin-left: 96px;
        font-size: 16px;
        font-weight: 600;
        padding: 10px 20px;
        border:none;
        border-radius: 6px;
        background-color: var(--p-color);
        color:var(--s-color);
        cursor: pointer;
    }
    #site-config button:hover {
        background-color: var(--p-color-h);
        transform: scale(1.1);
    }

    @media(max-width:420px) {
        #site-config {
            margin-left: -25px;
        }
        #site-config .rules {
            width: 326px;
        }
        #site-config .rules .add-rule span {
            margin-left: 16px;
        }
        #site-config .site-name {
            width: 360px;
        }
        #site-config .site-name input[type=color] {
            width: 100px;
        }
        #site-config .button-save {
            width:80%;
            display: flex;
            justify-content: center;
        }
    }
</style>