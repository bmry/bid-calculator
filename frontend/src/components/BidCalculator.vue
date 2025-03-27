<template>
    <div class="bid-calculator">
        <form @submit.prevent>
            <div class="form-group">
                <label for="price">Vehicle Base Price:</label>
                <input
                    type="number"
                    id="price"
                    v-model.number="price"
                    class="form-control"
                    placeholder="Enter price"
                />
            </div>
            <div class="form-group">
                <label for="type">Vehicle Type:</label>
                <select id="type" v-model="vehicleType" class="form-control">
                    <option value="common">Common</option>
                    <option value="luxury">Luxury</option>
                </select>
            </div>
        </form>
        <div v-if="fees" class="fees">
            <h3>Fees Breakdown</h3>
            <ul>
                <li v-for="(item, index) in fees.items" :key="index">
                    {{ item.name }}: {{ item.amount }}
                </li>
            </ul>
            <h3>Total: {{ fees.total }}</h3>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash-es'; // Using lodash-es for debounce (install via npm if needed)

export default defineComponent({
    name: 'BidCalculator',
    setup() {
        const price = ref<number>(0);
        const vehicleType = ref<string>('common');
        const fees = ref<null | { items: { name: string; amount: number }[]; total: number }>(null);

        // Debounced API call to reduce excessive requests
        const calculateBidDebounced = debounce(async () => {
            if (!price.value) {
                fees.value = null;
                return;
            }
            try {
                const apiUrl = import.meta.env.VITE_API_URL; // Read API host from env variable
                const response = await axios.post(`${apiUrl}/api/v1/bid/calculate`, {
                    price: price.value,
                    type: vehicleType.value
                });
                fees.value = response.data;
            } catch (error) {
                console.error('Error calling API:', error);
                fees.value = null;
            }
        }, 300); // 300ms debounce delay

        // Watchers trigger calculation automatically when price or vehicleType changes
        watch([price, vehicleType], () => {
            calculateBidDebounced();
        });

        return {
            price,
            vehicleType,
            fees
        };
    }
});
</script>

<style scoped>
.bid-calculator {
    background-color: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.form-group {
    margin-bottom: 1rem;
}
label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}
.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
.fees {
    margin-top: 1.5rem;
    background-color: #e9ecef;
    padding: 1rem;
    border-radius: 4px;
}
.fees ul {
    list-style: none;
    padding: 0;
}
.fees li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #ced4da;
}
.fees li:last-child {
    border-bottom: none;
}
</style>
