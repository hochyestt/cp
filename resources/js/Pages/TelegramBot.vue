<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10 flex items-center justify-center text-white">
      
      <section class="bg-white/40 p-10 rounded-xl shadow-2xl max-w-lg w-full text-center">
        
        <h1 class="text-3xl font-bold mb-4 text-gray-800">
          Получайте уведомления и отслеживайте прогресс!
        </h1>
        
        <p class="text-lg text-black mb-8">
          Для того чтобы получать напоминания о задачах и привычках, а также отмечать свой успех на ходу, перейдите в нашего Telegram-бота и запустите его.
        </p>

        <div class="mb-8">
          <svg class="w-16 h-16 text-[#0088cc] mx-auto" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.94 2C6.45 2 2 6.45 2 11.94C2 17.43 6.45 22 11.94 22C17.43 22 22 17.43 22 11.94C22 6.45 17.55 2 11.94 2Z" fill="#0088CC"/>
            <path d="M17.48 7.42L5.86 11.89C5.4 12.06 5.4 12.3 5.86 12.47L7.96 13.23L17.78 7.82C18.14 7.62 17.9 7.38 17.54 7.58L9.04 15.15L9.36 17.61C9.48 18.06 9.8 18.06 10.02 17.84L12.34 15.53L15.34 18.04C15.7 18.3 16.02 18.17 16.14 17.72L18.6 8.3C18.72 7.85 18.26 7.23 17.48 7.42Z" fill="#FFFFFF"/>
          </svg>
        </div>

        <a 
          :href="telegramLink" 
          target="_blank" 
          class="inline-block px-10 py-3 bg-[#0088cc] text-white font-bold rounded-lg text-xl hover:bg-[#0077b3]"
        >
          Запустить Telegram-бота
        </a>
        
      

      </section>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'

const BOT_USERNAME = 'chaosplanner_bot' 

const telegramLink = computed(() => {
  return `https://t.me/${BOT_USERNAME}`
})


</script>