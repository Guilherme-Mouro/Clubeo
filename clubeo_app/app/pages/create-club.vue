<template>
    <InputCard v-model="form.name" placeholder="Club name"/>
    <InputCard v-model="form.description" placeholder="Description"/>
    <button @click="createClub">Create</button>
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