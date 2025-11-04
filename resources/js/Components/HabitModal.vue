<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black/30 z-50">
    <div class="bg-white p-8 shadow-2xl w-96 max-w-full relative rounded-lg">
      <button 
        @click="$emit('close')" 
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold rounded-full h-8 w-8 leading-none flex items-center justify-center"
      >
        &times;
      </button>
      
      <h3 class="text-2xl font-bold mb-4 text-gray-800">Добавить привычку</h3>

      <input 
        v-model="habit.name" 
        type="text" 
        placeholder="Название привычки (например, 'Читать 30 минут')" 
        class="w-full p-3 border border-gray-300 mb-3 outline-none text-gray-700 rounded-md focus:border-green-600 transition" 
      />
      
      <input 
        v-model="habit.description" 
        type="text" 
        placeholder="Описание (необязательно)" 
        class="w-full p-3 border border-gray-300 mb-4 outline-none text-gray-700 rounded-md focus:border-green-600 transition" 
      />

      <h4 class="text-lg font-semibold mb-2 text-gray-700">Частота выполнения</h4>
      
      <div class="flex gap-3 mb-5">
        
        <select 
          v-model="habit.frequency_type" 
          class="w-1/2 p-3 border border-gray-300 outline-none text-gray-700 rounded-md appearance-none bg-white focus:border-green-600 transition"
        >
          <option value="day">В день</option>
          <option value="week">В неделю</option>
        </select>

        <input 
          v-model.number="habit.frequency_value" 
          type="number" 
          min="1" 
          :max="habit.frequency_type === 'day' ? 99 : 7"
          :placeholder="habit.frequency_type === 'day' ? 'Раз в день' : 'Раз в неделю'"
          class="w-1/2 p-3 border border-gray-300 outline-none text-gray-700 rounded-md focus:border-green-600 transition text-center" 
        />
      </div>
      
      <div class="flex justify-end gap-3">
        <button 
          @click="$emit('close')" 
          class="px-5 py-2 bg-gray-300 text-black hover:bg-gray-400 transition rounded-md"
        >
          Отмена
        </button>
        <button 
          @click="$emit('save')" 
          class="px-5 py-2 bg-green-800 text-white hover:bg-green-700 transition rounded-md shadow-md"
        >
          Добавить
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  habit: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      description: '', 
      frequency_type: 'day', 
      frequency_value: 1,   
    })
  }
})

const emit = defineEmits(['close', 'save'])
</script>