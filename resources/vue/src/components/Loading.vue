<script>
    export default {
        props:['size'],
        data() {
            return {
                s: `${this.size}px`,
            }
        }
    }
</script>

<template>
    <div class="loading">

    </div>
</template>

<style>
    .loading {
        width: v-bind(s);
        height: v-bind(s);
        border: 5px solid var(--s-color);
        border-radius: 50%;
        border-left: 5px solid var(--p-color);
        animation-name: rotate;
        animation-duration: 1.7s;
        animation-iteration-count: infinite;
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