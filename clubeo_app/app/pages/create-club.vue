<template>
  <div class="flex flex-col">
    <InputCard v-model="form.name" placeholder="Club name" />
    <InputCard v-model="form.description" placeholder="Description" />
    <button class="bg-custom-highlight text-white font-bold rounded-lg p-2" @click="createClub">Create</button>
  </div>
</template>

<script setup>
const form = ref({
  name: '',
  description: '',
})

const authCookie = useCookie('auth_data')

const createClub = async () => {
  if (!authCookie.value?.token) {
    alert("Tens de estar ligado para criar um clube!");
    return navigateTo('/login');
  }

  try {
    const res = await fetch("/clubeo_php_api/createClub.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${authCookie.value.token}`
      },
      body: JSON.stringify({
        name: form.value.name,
        description: form.value.description,
      })
    });

    const data = await res.json()

    if (res.ok) {
      alert("Clube criado com sucesso!")
      navigateTo('/discover')
    } else {
      alert(data.error || "Error creating club")
    }

  } catch (error) {
    console.error(error);
    alert("Connection error")
  }
}
</script>