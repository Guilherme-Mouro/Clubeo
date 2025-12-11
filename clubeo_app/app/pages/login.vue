<template>
    <AuthCard title="Welcome Back!" button="Login" @login="login">
        <form class="flex flex-col">
            <InputCard v-model="form.email" value="Email"></InputCard>
            <InputCard v-model="form.password" value="Password"></InputCard>
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

const form = ref({
    email: '',
    password: '',
})

const login = async () => {
  const res = await fetch("/clubeo_php_api/authUser?action=login", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      email: form.value.email,
      password: form.value.password,
    })
  });
}
</script>