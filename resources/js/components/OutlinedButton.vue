<template>
    <button
      @click="$emit('click')"
      :class="[
        'px-3 py-1 mx-2 font-semibold border-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-all duration-200 transform hover:scale-105',
        `text-${color}-600 border-${color}-300`,
        `hover:text-white hover:bg-gradient-to-r hover:from-${color}-600 hover:to-${color}-500`,
        `focus:ring-${color}-500`
      ]"
    >
      {{ text }}
    </button>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    color: {
      type: String,
      default: 'cyan'
    },
    text: {
      type: String,
      required: true
    }
  })
  
  const emit = defineEmits(['click'])
  </script>