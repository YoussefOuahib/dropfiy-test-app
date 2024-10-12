<template>
    <button
      @click="$emit('click')"
      :class="buttonClasses"
    >
      {{ text }}
    </button>
  </template>
  
  <script setup>
  import {  defineEmits, computed} from 'vue'
  
  const props = defineProps({
    color: {
      type: String,
      default: 'sky'
    },
    text: {
      type: String,
      required: true
    }
  })
  
  const emit = defineEmits(['click']);
  const buttonClasses = computed(() => [
  'px-3 py-1 mx-2 font-semibold border-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-all duration-200 transform hover:scale-105',
  `text-${props.color}-600`,
  `border-${props.color}-600`,
  `hover:text-white`,
  `hover:bg-${props.color}-600`,
  `focus:ring-${props.color}-500`
])
  </script>