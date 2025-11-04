<template>
    <div class="flex h-screen font-['Tinos']">
        <Sidebar />

        <main class="flex-1 bg-[#9cc0bd] p-10 flex flex-col gap-10 text-white overflow-auto">
            
            <section class="bg-white/40 p-6 shadow-xl rounded-lg flex-grow">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-6 flex items-center">
    
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    Календарь
                </h1>

                

                <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-white">
                    <iframe
src="https://calendar.google.com/calendar/embed?src=example%40gmail.com&ctz=Asia%2FIrkutsk"
    style="border: 0"
    width="100%"
    height="800"
    frameborder="0"
    scrolling="no">
</iframe>
                </div>
            </section>

        </main>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
</script>

<style scoped>
iframe {
    border: none;
    width: 100%;
    height: 700px;
}
</style>
