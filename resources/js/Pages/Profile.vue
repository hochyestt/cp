<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10">
      <div class="bg-[#bedfdf] rounded-lg shadow-lg p-10 h-[85%]">
        <div class="flex items-center gap-8">
          <div class="w-32 h-32 rounded-full bg-gray-300 border border-gray-400"></div>
          <div>
            <h2 class="text-2xl font-semibold text-black">Имя Фамилия</h2>
            <ul class="mt-3 space-y-1 text-black text-lg">
              <li><a href="#" class="hover:underline">Посмотреть статистику</a></li>
              <li><a href="#" class="hover:underline">Настройки</a></li>
            </ul>
          </div>
        </div>
      </div>
          <button
          @click="logout"
          class="px-6 py-3 bg-red-500 border border-red-500 text-white rounded-lg shadow-sm hover:bg-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
        >
          Выйти
        </button>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
</script>
