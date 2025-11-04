<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10 flex gap-10 text-white">
      <div class="flex flex-col gap-6 w-1/2">
        <section class="bg-white/40 p-6 shadow-md">
          <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Задачи</h2>
            <button @click="showTaskModal = true" class="text-white text-3xl hover:scale-110 transition">+</button>
          </div>
          <ul class="space-y-2">
            <li 
              v-for="task in tasks" 
              :key="task.id" 
              class="bg-white/70 text-black p-3 text-lg font-medium"
            >
              {{ task.title }}
            </li>
            <li v-if="tasks.length === 0" class="text-gray-600 text-center">Нет задач</li>
          </ul>
        </section>

        <section class="bg-white/40 p-6 shadow-md">
          <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Привычки</h2>
            <button @click="showHabitModal = true" class="text-white text-3xl hover:scale-110 transition">+</button>
          </div>
          <ul class="space-y-2">
            <li 
              v-for="habit in habits" 
              :key="habit.id" 
              class="bg-white/70 text-black p-3 text-lg font-medium"
            >
              {{ habit.name }} — {{ habit.progress }}
            </li>
            <li v-if="habits.length === 0" class="text-gray-600 text-center">Нет привычек</li>
          </ul>
        </section>
      </div>

      <section class="flex-1 bg-white/40 p-6 shadow-md">
        <h2 class="text-2xl font-bold mb-3 text-gray-800">Статистика</h2>
      </section>
    </main>

    <TaskModal 
      v-if="showTaskModal" 
      :task="newTask" 
      @close="showTaskModal = false" 
      @save="addTask"
    />
    <HabitModal 
      v-if="showHabitModal" 
      :habit="newHabit" 
      @close="showHabitModal = false" 
      @save="addHabit"
    />
    <TaskView
      :show="isModalOpen"
      :task="selectedTask"
      @close="isModalOpen = false"
    />
  </div>
  
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import TaskModal from '@/Components/TaskModal.vue'
import HabitModal from '@/Components/HabitModal.vue'

const props = defineProps({
  tasks: { type: Array, default: () => [] },
  habits: { type: Array, default: () => [] }
})

const newTask = ref({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  start_time: '',
  end_time: ''
})

const newHabit = ref({
  name: '',
  progress: '0%'
})

const showTaskModal = ref(false)
const showHabitModal = ref(false)


const addTask = () => {
  if (!newTask.value.title.trim()) return

  router.post('/tasks', newTask.value, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['tasks'] }) 
      newTask.value = { title: '', description: '', status: 'pending', priority: 'medium', start_time: '', end_time: '' }
      showTaskModal.value = false
    },
    onError: () => alert('Ошибка при добавлении задачи')
  })
}

const addHabit = () => {
  if (!newHabit.value.name.trim()) return

  router.post('/habits', newHabit.value, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['habits'] }) 
      newHabit.value = { name: '', progress: '0%' }
      showHabitModal.value = false
    },
    onError: () => alert('Ошибка при добавлении привычки')
  })
}

</script>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
