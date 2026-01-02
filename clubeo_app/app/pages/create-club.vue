<template>
  <div class="flex flex-row items-center">

    <div class="flex flex-col items-center gap-4">
      <div class="relative group cursor-pointer" @click="$refs.fileInput.click()">
        <Avatar class="w-48 h-48" :image="previewUrl" />
        <div
          class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
          <span class="text-white font-bold text-sm">Change Photo</span>
        </div>
      </div>
      <input type="file" ref="fileInput" class="hidden" @change="onFileSelected" accept="image/*" />
    </div>

    <div class="flex flex-col">
      <InputCard v-model="form.name" placeholder="Club name" />
      <InputCard v-model="form.description" placeholder="Description" />
      <button class="bg-custom-highlight text-white font-bold rounded-lg p-2" @click="createClub">Create</button>
    </div>
  </div>
</template>

<script setup>
const form = ref({
  name: '',
  description: '',
})

const authCookie = useCookie('auth_data')

const selectedFile = ref(null)
const previewUrl = ref(null)

const onFileSelected = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const uploadToCloudinary = async () => {
  const formData = new FormData()
  formData.append('file', selectedFile.value)
  formData.append('upload_preset', 'clubeo_preset')

  const res = await fetch('https://api.cloudinary.com/v1_1/dk4s1jyeo/image/upload', {
    method: 'POST',
    body: formData
  })

  if (!res.ok) throw new Error("Erro no upload para o Cloudinary");

  const data = await res.json()
  return data.secure_url
}

const createClub = async () => {
  if (!authCookie.value?.token) {
    alert("Tens de estar ligado para criar um clube!");
    return navigateTo('/login');
  }

  try {
    if (selectedFile.value) {
      finalImageUrl = await uploadToCloudinary()
    }

    const res = await fetch("/clubeo_php_api/createClub.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${authCookie.value.token}`
      },
      body: JSON.stringify({
        name: form.value.name,
        description: form.value.description,
        imgUrl: finalImageUrl
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