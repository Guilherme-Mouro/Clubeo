<template>
  <AuthCard title="Welcome Back!" button="Login" @login="login">
    <form class="flex flex-col" @submit.prevent>

      <InputCard v-model="form.email" placeholder="Email" type="email" />
      <div v-if="isEmailInvalid">
        <InputWarning :message="errorMessages.email" />
      </div>

      <InputCard v-model="form.password" placeholder="Password" type="password" />
      <div v-if="isEmailInvalid">
        <InputWarning :message="errorMessages.email" />
      </div>

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
definePageMeta({
  layout: 'auth'
})

const toast = useToast()

const form = ref({
  email: '',
  password: '',
})

const login = async () => {
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

    const data = await res.json()

    if (!res.ok) {
    toast.error({ title: 'Error!', message: 'Email or password are wrong!' })
      return;
    }

    localStorage.setItem('userId', data.user.id)

    toast.success({ title: 'Success!', message: 'Login successful!' })
    navigateTo('/')

  } catch (error) {
    console.error(error);
    toast.error({ title: 'Error!', message: 'Connection error!' })
  }
}
</script>

<style scoped>
@keyframes shake {

  0%,
  100% {
    transform: translateX(0)
  }

  20%,
  60% {
    transform: translateX(-5px)
  }

  40%,
  80% {
    transform: translateX(5px)
  }
}

.shake-animation {
  animation: shake 0.4s ease-in-out
}
</style>