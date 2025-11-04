<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10 text-white overflow-hidden min-h-0">
      <h1 class="text-3xl font-bold mb-6 text-center">История</h1>

      <div class="grid grid-cols-2 gap-8 h-full min-h-0">
        <section class="bg-white/40 p-6 rounded-xl flex flex-col min-h-0">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Задачи</h2>

          <ul class="space-y-2 flex-1 overflow-y-auto pr-2 min-h-0">
            <li
              v-for="task in localTasks"
              :key="task.id"
              class="bg-white/70 text-black p-3 rounded hover:bg-white/90 transition"
              role="button"
              tabindex="0"
            >
              <div class="flex justify-between items-start">
                <div @click="openTask(task)" class="flex-1 cursor-pointer">
                  <h3 class="font-bold text-lg">{{ task.title }}</h3>
                  <p v-if="task.description" class="text-sm text-gray-700">{{ shortDesc(task.description) }}</p>
                </div>

                <div class="flex items-center ml-4">
                  <div class="text-sm text-gray-600 ml-4 text-right whitespace-nowrap">
                    <div class="font-medium text-gray-800">
                      {{ formatDateTime(task.start_time) }}
                    </div>
                    </div>
                  
                  <button 
                    @click="deleteTask(task.id)" 
                    class="ml-3 p-1 text-red-600 hover:text-red-800 transition font-medium text-sm"
                    title="Удалить задачу"
                  >
                    Удалить
                  </button>
                </div>
              </div>
            </li>

            <li v-if="localTasks.length === 0" class="text-center text-gray-600 p-4">
              Нет задач
            </li>
          </ul>
        </section>

        <section class="bg-white/40 p-6 rounded-xl flex flex-col min-h-0">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Привычки</h2>

          <ul class="space-y-2 flex-1 overflow-y-auto pr-2 min-h-0">
            <li
              v-for="habit in localHabits"
              :key="habit.id"
              class="bg-white/70 text-black p-3 rounded hover:bg-white/90 transition"
              role="button"
              tabindex="0"
            >
              <div class="flex justify-between items-center">
                <div @click="openHabit(habit)" class="flex-1 cursor-pointer">
                  <h3 class="font-bold text-lg">{{ habit.name }}</h3>
                  <p class="text-sm text-gray-700">
                    {{ formatHabitProgress(habit) }}
                  </p>
                </div>

                <div class="flex items-center ml-4">
                  <div class="text-lg font-semibold text-green-700 mr-4 whitespace-nowrap">
                    {{ calculateHabitPercentage(habit) }}%
                  </div>
                  
                  <button 
                    @click="deleteHabit(habit.id)" 
                    class="ml-3 p-1 text-red-600 hover:text-red-800 transition font-medium text-sm"
                    title="Удалить привычку"
                  >
                    Удалить
                  </button>
                </div>
              </div>
            </li>

            <li v-if="localHabits.length === 0" class="text-center text-gray-600 p-4">
              Нет привычек
            </li>
          </ul>
        </section>
      </div>
    </main>

    <TaskView
      :show="isTaskModalOpen"
      :task="selectedTask"
      @close="closeTaskModal"
      @delete="deleteTask" 
    />
    
    <HabitView
      :show="isHabitModalOpen"
      :habit="selectedHabit"
      @close="closeHabitModal"
      @done="handleHabitDone"
      @delete="deleteHabit" 
    />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3' 
import Sidebar from '@/Components/Sidebar.vue'
import TaskView from '@/Components/TaskView.vue'
import HabitView from '@/Components/HabitView.vue'

const props = defineProps({
  tasks: { type: Array, default: () => [] },
  habits: { type: Array, default: () => [] }
})

const localTasks = ref([...props.tasks])
const localHabits = ref([...props.habits])
watch(() => props.tasks, (v) => localTasks.value = [...(v || [])])
watch(() => props.habits, (v) => localHabits.value = [...(v || [])])



function deleteTask(taskId) {
  router.delete(`/tasks/${taskId}`, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['tasks'] }) 
      closeTaskModal()
    },
    onError: () => alert('Ошибка при удалении задачи в БД!')
  })
}

function deleteHabit(habitId) {
  router.delete(`/habits/${habitId}`, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['habits'] }) 
      closeHabitModal()
    },
    onError: () => alert('Ошибка при удалении привычки в БД!')
  })
}



function handleHabitDone(habitId) {
    console.log(`[TODO] Отметка выполнения привычки ID: ${habitId}`);
    
    const habitIndex = localHabits.value.findIndex(h => h.id === habitId);
    if (habitIndex !== -1) {
        localHabits.value[habitIndex].times_done_since_reset = 
            (localHabits.value[habitIndex].times_done_since_reset || 0) + 1;
    }
}



const isTaskModalOpen = ref(false)
const selectedTask = ref({}) 
function openTask(task) { selectedTask.value = { ...task }; isTaskModalOpen.value = true }
function closeTaskModal() { isTaskModalOpen.value = false }

const isHabitModalOpen = ref(false)
const selectedHabit = ref({}) 
function openHabit(habit) { selectedHabit.value = { ...habit }; isHabitModalOpen.value = true }
function closeHabitModal() { isHabitModalOpen.value = false }


function shortDesc(text) {
  if (!text) return ''
  return text.length > 80 ? text.slice(0, 77) + '...' : text
}


function formatDateTime(dateTimeString) {
    if (!dateTimeString) return 'Нет даты'
    try {
      const date = new Date(dateTimeString);
      return new Intl.DateTimeFormat('ru-RU', { 
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' 
      }).format(date);
    } catch (e) {
      return 'Неверный формат';
    }
}

function calculateHabitPercentage(habit) {
    const done = habit.times_done_since_reset || 0;
    const required = habit.frequency_value || 1;
    if (required === 0) return 0;
    const percentage = Math.min(100, (done / required) * 100);
    return Math.round(percentage);
}

function formatHabitProgress(habit) {
    const done = habit.times_done_since_reset || 0;
    const required = habit.frequency_value || 1;
    const period = habit.frequency_type === 'day' ? 'день' : 'неделю';
    
    if (done >= required) {
        return `Выполнено! (${done} из ${required} в ${period})`; 
    }
    return `${done} из ${required} раз в ${period}`;
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


