<template>
  <AuthCard title="Welcome Back!" button="Login" @login="login">
    <form class="flex flex-col" :class="{ 'shake-animation': isShaking }" @submit.prevent>
      <InputCard v-model="form.email" placeholder="Email" type="email" />
      <InputCard v-model="form.password" placeholder="Password" type="password" />
    </form>

    <p class="text-custom-first_text">
      Don't have an account?
      <NuxtLink class="text-custom-highlight" to="/register">
        Create one here!
      </NuxtLink>
    </p>
  </AuthCard>
</template>

<script setup>
import { ref } from 'vue'

definePageMeta({
  layout: 'auth'
})

const toast = useToast()
// Store both userId and token in the cookie for future requests
const authCookie = useCookie('auth_data', { 
  secure: true, 
  sameSite: 'strict',
  maxAge: 60 * 60 * 24 // 24 hours
})

const isShaking = ref(false)
const isLoading = ref(false)

const triggerShake = () => {
  isShaking.value = true
  setTimeout(() => {
    isShaking.value = false
  }, 400)
};

const form = ref({
  email: '',
  password: '',
})

const login = async () => {
  if (isLoading.value) return;

  const emptyFields = !form.value.email || !form.value.password;

  if (emptyFields) {
    triggerShake();
    toast.error({ title: 'Error!', message: 'Please fill in all fields!' });
    return;
  }

  isLoading.value = true

  try {
    const res = await fetch("/clubeo_php_api/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        email: form.value.email,
        password: form.value.password,
      })
    });

    const data = await res.json();

    if (!res.ok) {
      triggerShake();
      toast.error({ title: 'Error!', message: data.error || 'Invalid credentials!' })
      return;
    }

    // Save both the ID (for UI) and the Token (for Security/API calls)
    authCookie.value = {
      userId: data.userId,
      token: data.token
    }

    toast.success({ title: 'Success!', message: 'Login successful!' })
    navigateTo('/')

  } catch (error) {
    console.error(error);
    toast.error({ title: 'Error!', message: 'Connection error!' })
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
@keyframes shake {
  0%, 100% { transform: translateX(0) }
  20%, 60% { transform: translateX(-5px) }
  40%, 80% { transform: translateX(5px) }
}
.shake-animation {
  animation: shake 0.4s ease-in-out
}
</style>