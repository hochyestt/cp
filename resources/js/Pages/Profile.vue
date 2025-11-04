<template>
  <div class="flex h-screen font-['Tinos']">
    <Sidebar />

    <main class="flex-1 bg-[#9cc0bd] p-10">
      <div
        class="bg-[#bedfdf] rounded-lg shadow-lg p-10 h-[85%] flex flex-col justify-between"
      >
        <div>
          <div class="flex items-center gap-8">
            <div
              class="w-32 h-32 rounded-full bg-gray-300 border border-gray-400"
            ></div>
            <div>
              <h2 class="text-2xl font-semibold text-black">{{ user.name }}</h2>
              <ul class="mt-3 space-y-1 text-black text-lg">
                <li>
                  <a href="#" class="hover:underline">Посмотреть статистику</a>
                </li>
                <li><a href="#" class="hover:underline">Настройки</a></li>
              </ul>
            </div>
          </div>

          <div class="mt-10">
            <label class="block text-black font-semibold mb-2">Telegram ID:</label>

            <div v-if="savedTelegramId" class="flex items-center gap-3">
              <span class="text-lg text-black">{{ savedTelegramId }}</span>
              <button
                @click="editTelegram"
                class="px-3 py-1 bg-[#9cc0bd] text-white rounded-md hover:bg-[#8ab3ad] transition"
              >
                Изменить
              </button>
            </div>

            <div v-else class="flex items-center">
              <input
                v-model="telegramId"
                type="text"
                class="w-80 px-3 py-2 rounded-lg border border-gray-400 focus:outline-none focus:ring-2 focus:ring-[#9cc0bd]"
                placeholder="Введите ваш Telegram ID"
              />
              <button
                @click="saveTelegramId"
                class="ml-3 px-4 py-2 bg-[#9cc0bd] text-white rounded-lg hover:bg-[#8ab3ad] transition"
              >
                Сохранить
              </button>
            </div>
          </div>
        </div>

        <button
          @click="logout"
          class="px-6 py-3 bg-red-500 border border-red-500 text-white rounded-lg shadow-sm hover:bg-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 self-start"
        >
          Выйти
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import Sidebar from '@/Components/Sidebar.vue'

const user = usePage().props.auth.user
const telegramId = ref(user.telegram_id || '')
const savedTelegramId = ref(user.telegram_id || '')

const saveTelegramId = async () => {
  try {
    await axios.post(route('profile.telegram'), { telegram_id: telegramId.value })
    savedTelegramId.value = telegramId.value
    alert('✅ Telegram ID сохранён!')
  } catch (error) {
    console.error(error)
    alert('Ошибка при сохранении Telegram ID.')
  }
}

const editTelegram = () => {
  telegramId.value = savedTelegramId.value
  savedTelegramId.value = ''
}

const logout = () => {
  router.post(route('logout'))
}
</script>
