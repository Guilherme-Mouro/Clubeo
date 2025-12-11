<template>
  <AuthCard title="Join Us!" button="Register" @login="register">
    <form class="flex flex-col">
      <InputCard v-model="form.username" value="Username"></InputCard>
      <InputCard v-model="form.email" value="Email"></InputCard>
      <InputCard v-model="form.password" value="Password"></InputCard>
    </form>
    <p class="text-custom-first_text">
      Already have an account?
      <NuxtLink class="text-custom-highlight" to="/login">
        Login here!
      </NuxtLink>
    </p>
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
  try {
    const res = await fetch("/clubeo_php_api/register.php", {
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

    const data = await res.json();

    if (!res.ok) {
        alert(data.error || "An unknown error occurred");
        return;
    }

    alert("Account created successfully! Please login.");
    navigateTo('/login');

  } catch (error) {
    console.error(error);
    alert("Connection error");
  }
}
</script>