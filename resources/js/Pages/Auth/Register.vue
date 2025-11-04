<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <Head title="Регистрация" />

  <!-- Центровка -->
  <div class="flex items-center justify-center min-h-screen bg-gray-100 font-['Tinos']">
    <form
      @submit.prevent="submit"
      class="bg-[#9cc0bd] rounded-xl p-8 w-80 flex flex-col items-center shadow-lg"
    >
      <h1 class="text-2xl font-bold text-black mb-6">Регистрация</h1>

      <!-- Поля -->
      <input
        v-model="form.name"
        type="text"
        placeholder="Имя"
        required
        class="w-full mb-4 p-2 rounded bg-gray-200 border-none focus:ring-2 focus:ring-[#6b9b97] outline-none"
      />

      <input
        v-model="form.email"
        type="email"
        placeholder="Email"
        required
        class="w-full mb-4 p-2 rounded bg-gray-200 border-none focus:ring-2 focus:ring-[#6b9b97] outline-none"
      />

      <input
        v-model="form.password"
        type="password"
        placeholder="Пароль"
        required
        class="w-full mb-4 p-2 rounded bg-gray-200 border-none focus:ring-2 focus:ring-[#6b9b97] outline-none"
      />

      <input
        v-model="form.password_confirmation"
        type="password"
        placeholder="Подтвердите пароль"
        required
        class="w-full mb-6 p-2 rounded bg-gray-200 border-none focus:ring-2 focus:ring-[#6b9b97] outline-none"
      />

      <button
        type="submit"
        class="bg-white text-[#3b1b2b] font-bold px-4 py-2 rounded-lg hover:bg-gray-200 transition"
        :disabled="form.processing"
      >
        Зарегистрироваться
      </button>
    </form>
  </div>
</template>
