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

const createClub = async () => {
  try {
    const res = await fetch("/clubeo_php_api/createClub.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        name: form.value.name,
        description: form.value.description,
        adminId: localStorage.getItem('userId'),
      })
    });

    const data = await res.json()

    navigateTo('/discover')

    if (!res.ok) {
      alert(data.error || "Error creating club")
      return;
    }

  } catch (error) {
    console.error(error);
    alert("Connection error")
  }
}
</script>