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

      <h2 class="text-2xl font-semibold mb-3">{{ task.title }}</h2>

      <p v-if="task.description" class="text-gray-700 mb-3">
        {{ task.description }}
      </p>

      <div class="text-sm space-y-1 text-gray-600 mb-4">
        <p><b>Статус:</b> {{ niceStatus }}</p>
        <p v-if="task.start_time"><b>Начало:</b> {{ formattedStart }}</p>
        <p v-if="task.end_time"><b>Окончание:</b> {{ formattedEnd }}</p>
        <p><b>Создано:</b> {{ createdAt }}</p>
      </div>

      <div class="flex gap-2 justify-end">
        <button @click="close" class="px-4 py-2 rounded bg-gray-200">Закрыть</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, required: true },
  task: { type: Object, required: true }
})
const emit = defineEmits(['close'])

const close = () => emit('close')

const niceStatus = computed(() => {
  if (!props.task || !props.task.status) return 'нет'
  return ({ pending: 'В ожидании', in_progress: 'В процессе', completed: 'Выполнено' }[props.task.status] ?? props.task.status)
})

const formattedStart = computed(() => props.task.start_time ? formatDateTime(props.task.start_time) : '')
const formattedEnd = computed(() => props.task.end_time ? formatDateTime(props.task.end_time) : '')
const createdAt = computed(() => props.task.created_at ? formatDateTime(props.task.created_at) : '')

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
