<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10 text-white overflow-hidden min-h-0">
      <h1 class="text-3xl font-bold mb-6 text-center">История</h1>

      <div class="grid grid-cols-2 gap-8 h-full min-h-0">
        <!-- Все задачи -->
        <section class="bg-white/40 p-6 rounded-xl flex flex-col min-h-0">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Задачи</h2>

          <ul class="space-y-2 flex-1 overflow-y-auto pr-2 min-h-0">
            <li
              v-for="task in tasks"
              :key="task.id"
              class="bg-white/70 text-black p-3 rounded hover:bg-white/90 transition"
            >
              <h3 class="font-bold text-lg">{{ task.title }}</h3>
            </li>

            <li v-if="tasks.length === 0" class="text-center text-gray-600 p-4">
              Нет задач
            </li>
          </ul>
        </section>

        <!-- Все привычки -->
        <section class="bg-white/40 p-6 rounded-xl flex flex-col min-h-0">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Привычки</h2>

          <ul class="space-y-2 flex-1 overflow-y-auto pr-2 min-h-0">
            <li
              v-for="habit in habits"
              :key="habit.id"
              class="bg-white/70 text-black p-3 rounded hover:bg-white/90 transition"
            >
              <h3 class="font-bold text-lg">{{ habit.name }}</h3>
            </li>

            <li v-if="habits.length === 0" class="text-center text-gray-600 p-4">
              Нет привычек
            </li>
          </ul>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'

const tasks = ref([])
const habits = ref([])

onMounted(async () => {
  await loadAllData()
})

const loadAllData = async () => {
  try {
    //  все задачи
    const tasksResponse = await fetch('/tasks')
    if (tasksResponse.ok) {
      tasks.value = await tasksResponse.json()
    }

    //  все привычки
    const habitsResponse = await fetch('/habits')
    if (habitsResponse.ok) {
      habits.value = await habitsResponse.json()
    }
  } catch (error) {
    console.error('Ошибка загрузки данных:', error)
  }
}
</script>

<style scoped>
ul::-webkit-scrollbar { 
  width: 8px; 
}
ul::-webkit-scrollbar-thumb { 
  background: rgba(0,0,0,0.2); 
  border-radius: 9999px; 
}
</style>