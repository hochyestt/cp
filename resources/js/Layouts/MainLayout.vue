<template>
  <div class="flex h-screen">
    <Sidebar />
    <main class="flex-1 bg-[#9cc4c2] p-8 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
</script>
