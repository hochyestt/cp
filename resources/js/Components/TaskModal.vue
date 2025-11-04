<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black/30 z-50">
    <div class="bg-white p-8 shadow-2xl w-96 max-w-full relative rounded-lg">
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold rounded-full h-8 w-8 leading-none flex items-center justify-center">&times;</button>
      
      <h3 class="text-2xl font-bold mb-4 text-gray-800">Добавить новую задачу</h3>

      <input 
        v-model="task.title" 
        type="text" 
        placeholder="Название задачи" 
        class="w-full p-3 border border-gray-300 rounded-md mb-3 outline-none focus:border-green-600 transition text-gray-700" 
      />
      
      <input 
        v-model="task.description" 
        type="text" 
        placeholder="Описание (необязательно)" 
        class="w-full p-3 border border-gray-300 rounded-md mb-3 outline-none focus:border-green-600 transition text-gray-700" 
      />
      
      <label class="text-gray-600 text-sm block mb-1">Дата и время начала</label>
      <input 
        v-model="task.start_time" 
        type="datetime-local" 
        class="w-full p-3 border border-gray-300 rounded-md mb-4 outline-none focus:border-green-600 transition text-gray-700" 
      />

    <div class="flex justify-end gap-3">
        <button @click="$emit('close')" class="px-5 py-2 bg-gray-300 text-black hover:bg-gray-400 transition rounded-md">Отмена</button>
        <button @click="$emit('save')" class="px-5 py-2 bg-green-800 text-white hover:bg-green-700 transition rounded-md">Добавить</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  task: Object
})
</script>