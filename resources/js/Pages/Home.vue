<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10 flex gap-10 text-white">
      <div class="flex flex-col gap-6 w-1/2">
     
       <!-- Задачи +-->
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
  </ul>
</section>

<!-- Привычки +-->
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
      {{ habit.name }}
    </li>
  </ul>
</section>

      </div>

      <!-- Статистика -->
      <section class="flex-1 bg-white/40 p-6 shadow-md">
        <h2 class="text-2xl font-bold mb-3 text-gray-800">Статистика</h2>
    
      </section>
    </main>

    <!-- задачи + -->
    <div v-if="showTaskModal" class="fixed inset-0 flex items-center justify-center bg-black/30 z-50">
      <div class="bg-white p-8 shadow-2xl w-96 max-w-full relative">
        <button @click="showTaskModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold mb-4 text-gray-800">Добавить задачу</h3>

        <input v-model="newTask.title" type="text" placeholder="Название задачи" class="w-full p-3 border border-gray-300 mb-3 outline-none text-gray-700" />
        <input v-model="newTask.description" type="text" placeholder="Описание" class="w-full p-3 border border-gray-300 mb-3 outline-none text-gray-700" />

        <div class="flex gap-2 mb-4">
          <select v-model="newTask.status" class="flex-1 p-3 border border-gray-300 outline-none text-gray-700">
            <option value="pending">В ожидании</option>
            <option value="in_progress">В процессе</option>
            <option value="completed">Выполнено</option>
          </select>
          <select v-model="newTask.priority" class="flex-1 p-3 border border-gray-300 outline-none text-gray-700">
            <option value="low">Низкий</option>
            <option value="medium">Средний</option>
            <option value="high">Высокий</option>
          </select>
        </div>

        <div class="flex justify-end gap-3">
          <button @click="showTaskModal = false" class="px-5 py-2 bg-gray-300 text-black">Отмена</button>
          <button @click="addTask" class="px-5 py-2 bg-green-800 text-white">Добавить</button>
        </div>
      </div>
    </div>

    <!-- привычки + -->
    <div v-if="showHabitModal" class="fixed inset-0 flex items-center justify-center bg-black/30 z-50">
      <div class="bg-white p-8 shadow-2xl w-96 max-w-full relative">
        <button @click="showHabitModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold mb-4 text-gray-800">Добавить привычку</h3>

        <input v-model="newHabit.name" type="text" placeholder="Название привычки" class="w-full p-3 border border-gray-300 mb-3 outline-none text-gray-700" />
        <input v-model="newHabit.progress" type="text" placeholder="Прогресс (например, 0%)" class="w-full p-3 border border-gray-300 mb-4 outline-none text-gray-700" />

        <div class="flex justify-end gap-3">
          <button @click="showHabitModal = false" class="px-5 py-2 bg-gray-300 text-black">Отмена</button>
          <button @click="addHabit" class="px-5 py-2 bg-green-800 text-white">Добавить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => []
  },
  habits: {
    type: Array,
    default: () => []
  }
})

const tasks = ref(props.tasks)
const habits = ref(props.habits)

watch(() => props.tasks, (newTasks) => {
  tasks.value = newTasks
})

watch(() => props.habits, (newHabits) => {
  habits.value = newHabits
})

const newTask = ref({ title: '', description: '', status: 'pending', priority: 'medium' })
const newHabit = ref({ name: '', progress: '0%' })

const showTaskModal = ref(false)
const showHabitModal = ref(false)

const addTask = () => {
  if (!newTask.value.title.trim()) return
  
  router.post('/tasks', newTask.value, {
    preserveScroll: true,
    onSuccess: () => {
      newTask.value = { title: '', description: '', status: 'pending', priority: 'medium' }
      showTaskModal.value = false
    },
    onError: (errors) => {
      console.error('Ошибка добавления задачи:', errors)
      alert('Ошибка при добавлении задачи')
    }
  })
}

const addHabit = () => {
  if (!newHabit.value.name.trim()) return
  
  router.post('/habits', newHabit.value, {
    preserveScroll: true,
    onSuccess: () => {
     
      newHabit.value = { name: '', progress: '0%' }
      showHabitModal.value = false
    },
    onError: (errors) => {
      console.error('Ошибка добавления привычки:', errors)
      alert('Ошибка при добавлении привычки')
    }
  })
}

const translateStatus = s => ({ pending: 'В ожидании', in_progress: 'В процессе', completed: 'Выполнено' }[s] || s)
const translatePriority = p => ({ low: 'Низкий', medium: 'Средний', high: 'Высокий' }[p] || p)
</script>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>