<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart } from 'chart.js/auto';

const props = defineProps({
    newTicketsData: {
        type: Array,
        required: true
    },
    activeTicketsData: {
        type: Array,
        required: true
    },
    resolvedTicketsData: {
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
    
    // Utiliser les mêmes dates pour toutes les séries
    const labels = props.newTicketsData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString();
    });
    
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nouveaux tickets',
                    data: props.newTicketsData.map(item => item.count),
                    borderColor: 'rgb(59, 130, 246)', // Bleu
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                },
                {
                    label: 'Tickets actifs',
                    data: props.activeTicketsData.map(item => item.count),
                    borderColor: 'rgb(234, 88, 12)', // Orange
                    backgroundColor: 'rgba(234, 88, 12, 0.1)',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                },
                {
                    label: 'Tickets résolus/fermés',
                    data: props.resolvedTicketsData.map(item => item.count),
                    borderColor: 'rgb(34, 197, 94)', // Vert
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
};

onMounted(() => {
    initChart();
});

watch([() => props.newTicketsData, () => props.activeTicketsData, () => props.resolvedTicketsData], () => {
    initChart();
}, { deep: true });
</script>

<template>
    <div class="w-full h-80"> <!-- Hauteur plus grande pour un graphique combiné -->
        <canvas ref="canvas"></canvas>
    </div>
</template>
