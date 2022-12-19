<script>
    export default {
        props:['size'],
        data() {
            return {
                s: `${this.size}px`,
                infoSize: `${this.size / 1.5}px`,
            }
        }
    }
</script>

<template>
    <div class="loading">
        <div class="info">

        </div>
    </div>
</template>

<style>
    .loading {
        width: v-bind(s);
        height: v-bind(s);
        border-left: none;
        border-radius:100%;
        background: linear-gradient(to right, var(--p-color) 20%, var(--s-color) 30%, var(--s-color) 50%);
        animation-name: rotate;
        animation-duration:1.5s;
        animation-iteration-count: infinite;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .loading .info{
        width:v-bind(infoSize);
        height:v-bind(infoSize);
        background-color: var(--p-color);
        border-radius: 50%;
        margin-top: 20px;
    } 

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>