<template>
    <AuthCard title="Join Us!" button="Register" @login="register">
        <form>
            <input v-model="form.username" placeholder="Username"></input>
            <input v-model="form.email" placeholder="Email"></input>
            <input v-model="form.password" placeholder="Password"></input>

        </form>
    </AuthCard>
</template>

<script setup>
definePageMeta({
    layout: 'auth'
})

const form = ref({
    username: '',
    email: '',
    password: '',
})

const register = async () => {
  const res = await fetch("/clubeo_php_api/authUser?action=register", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      username: form.value.username,
      email: form.value.email,
      password: form.value.password,
    })
  });
}
</script>