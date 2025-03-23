<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart } from 'chart.js/auto';

const props = defineProps({
    data: {
        type: Array,
        required: true
    }
});

const canvas = ref(null);
let chart = null;

const initChart = () => {
    if (chart) {
        chart.destroy();
    }

    const ctx = canvas.value.getContext('2d');
    
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.data.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString();
            }),
            datasets: [{
                label: 'Tickets résolus/fermés',
                data: props.data.map(item => item.count),
                borderColor: 'rgb(34, 197, 94)', // Vert pour différencier des autres graphiques
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `Tickets résolus/fermés: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
};

onMounted(() => {
    initChart();
});

watch(() => props.data, () => {
    initChart();
}, { deep: true });
</script>

<template>
    <div class="w-full h-64">
        <canvas ref="canvas"></canvas>
    </div>
</template>
