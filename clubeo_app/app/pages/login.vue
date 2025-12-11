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
        alert(data.error || "Login failed");
        return;
    }

    alert("Login successful!");
    navigateTo('/');

  } catch (error) {
    console.error(error);
    alert("Connection error");
  }
}
</script>