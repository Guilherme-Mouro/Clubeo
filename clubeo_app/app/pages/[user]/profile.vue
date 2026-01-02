<template>
  <h1>{{ user.username }}</h1>
</template>

<script setup>
import { onMounted } from 'vue'

definePageMeta({
  layout: 'auth'
})

const authCookie = useCookie('auth_data')

const fetchUserData = async () => {
  if (!authCookie.value?.userId || !authCookie.value?.token) {
    navigateTo('/login');
    return;
  }

  try {
    const res = await fetch(`/clubeo_php_api/getUser.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: authCookie.value.userId,
        token: authCookie.value.token
      })
    });

    const data = await res.json();
    if (res.ok) {
      user.value = { ...user.value, ...data.user };
    } else {
      authCookie.value = null;
      navigateTo('/login');
    }
  } catch (error) {
    toast.error({ title: 'Error!', message: 'Connection error while fetching user data!' });
  }
}

onMounted(async () => {
  if (authCookie.value?.userId) {
    await fetchUserData();
    await fetchUserClubs(authCookie.value.userId);

  } else {
    navigateTo('/login');
  }
})
</script>