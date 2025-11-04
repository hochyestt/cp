<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    @keydown.escape.window="close"
  >
    <div
      class="bg-white text-black p-6 rounded-2xl w-[90%] max-w-xl shadow-xl relative transform transition-all"
    >
      <button
        @click="close"
        class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl"
        aria-label="Close"
      >✕</button>

      <h2 class="text-2xl font-semibold mb-3">{{ habit.name }}</h2>

      <p v-if="habit.description" class="text-gray-700 mb-3">
        {{ habit.description }}
      </p>

      <div class="text-sm space-y-1 text-gray-600 mb-4">
        <p>
          <b>Цель:</b> 
          {{ habit.frequency_value }} раз в {{ niceFrequencyType }}
        </p>
        
        <p>
          <b>Прогресс:</b> 
          {{ habit.times_done_since_reset || 0 }} из {{ habit.frequency_value }} 
          ({{ calculatedPercentage }}%)
        </p>
        
        <p v-if="habit.last_done_at">
          <b>Последнее выполнение:</b> {{ formattedLastDone }}
        </p>
        
        <p v-if="habit.created_at">
          <b>Создано:</b> {{ createdAt }}
        </p>
      </div>

      <div class="flex gap-2 justify-end">
        <button 
          v-if="!isCompleted"
          @click="$emit('done', habit.id)" 
          class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition font-semibold"
        >
          Выполнено
        </button>
        <button @click="close" class="px-4 py-2 rounded bg-gray-200">Закрыть</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, required: true },
  habit: { 
    type: Object, 
    required: true,
    default: () => ({ name: 'Неизвестно', frequency_type: 'day', frequency_value: 1, description: '' })
  }
})
const emit = defineEmits(['close', 'done']) 

const close = () => emit('close')


const niceFrequencyType = computed(() => {
  return props.habit.frequency_type === 'day' ? 'день' : 'неделю'
})

const calculatedPercentage = computed(() => {
  const done = props.habit.times_done_since_reset || 0
  const required = props.habit.frequency_value || 1
  if (required === 0) return 0
  const percentage = Math.min(100, (done / required) * 100)
  return Math.round(percentage)
})

const isCompleted = computed(() => {
  const done = props.habit.times_done_since_reset || 0
  const required = props.habit.frequency_value || 1
  return done >= required
})

const formattedLastDone = computed(() => props.habit.last_done_at ? formatDateTime(props.habit.last_done_at) : '—')
const createdAt = computed(() => props.habit.created_at ? formatDateTime(props.habit.created_at) : '—')


function formatDateTime(value) {
  try {
    const d = new Date(value)
    if (isNaN(d)) return value
    return d.toLocaleString()
  } catch {
    return value
  }
}
</script>