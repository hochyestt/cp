<template>
  <aside class="w-64 bg-white h-screen p-6 flex flex-col justify-between border-r border-gray-200">
    <div>
      <div class="flex justify-center mb-10 cursor-pointer" @click="goHome">
        <img src="/logo.png" alt="CHAOS Planner" class="w-42 hover:scale-105 transition-transform" />
      </div>

      <nav class="flex flex-col gap-12 text-gray-800 text-[24px] font-bold">
        <Link href="/profile">Личный кабинет</Link>
        <Link href="/history">История</Link>
        <Link href="/calendar">Календарь</Link>
        <Link href="/telegrambot">Телеграм-бот</Link>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'

const goHome = () => {
  router.visit('/')
}

const goCabinet = async () => {
  try {
    const res = await fetch('/api/user', { credentials: 'include' })
    if (res.ok) {
      router.visit('/cabinet') 
    } else {
      router.visit('/login')
    }
  } catch (err) {
    console.error(err)
    router.visit('/login')
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Tinos:wght@400;700&display=swap');

* {
  font-family: 'Tinos', serif;
}
</style>
