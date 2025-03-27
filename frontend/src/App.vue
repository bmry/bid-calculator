<template>
    <div class="container">
        <h1>Bid Calculator</h1>
        <BidCalculator />
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import BidCalculator from './components/BidCalculator.vue';

export default defineComponent({
    name: 'App',
    components: { BidCalculator }
});
</script>

<style scoped>
.container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
h1 {
    text-align: center;
    color: #343a40;
    margin-bottom: 1.5rem;
}
</style>
