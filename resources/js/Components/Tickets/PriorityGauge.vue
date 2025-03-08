<!-- resources/js/Components/Tickets/PriorityGauge.vue -->
<template>
    <div class="priority-gauge" :title="priorityLabels[priority]">
      <div class="gauge-container">
        <div class="gauge-level" :class="gaugeClass" :style="{ height: gaugeHeight }"></div>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    priority: {
      type: String,
      required: true
    }
  });
  
  const priorityLabels = {
    'critical': 'Critique',
    'high': 'Haute',
    'medium': 'Moyenne',
    'low': 'Basse'
  };
  
  const gaugeClass = {
    'bg-red-500': props.priority === 'critical',
    'bg-orange-500': props.priority === 'high',
    'bg-blue-500': props.priority === 'medium',
    'bg-green-500': props.priority === 'low'
  };
  
  const gaugeHeight = {
    'critical': '100%',
    'high': '75%',
    'medium': '50%',
    'low': '25%'
  }[props.priority];
  </script>
  
  <style scoped>
  .priority-gauge {
    display: inline-block;
  }
  .gauge-container {
    width: 8px;
    height: 24px;
    background-color: #f3f4f6;
    border-radius: 4px;
    position: relative;
    overflow: hidden;
  }
  .gauge-level {
    position: absolute;
    bottom: 0;
    width: 100%;
    transition: height 0.2s;
  }
  </style>