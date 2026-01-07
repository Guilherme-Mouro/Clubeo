<template>
  <div class="max-w-2xl mx-auto p-6 space-y-6">
    <h2 class="text-2xl font-bold text-custom-highlight text-4xl">Profile</h2>

    <div class="flex flex-col items-center gap-4">
      <div class="relative group cursor-pointer" @click="$refs.fileInput.click()">
        <Avatar class="w-48 h-48" :image="user.avatar_url" />
        <div
          class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
          <span class="text-white font-bold text-sm">Change Photo</span>
        </div>
      </div>
      <input type="file" ref="fileInput" class="hidden" @change="onFileSelected" accept="image/*" />
    </div>

    <div class="grid grid-cols-1 gap-4">
      <label class="block">
        <span class="text-custom-first_text font-semibold">Username</span>
        <InputCard v-model="user.username" placeholder="Username" />
      </label>

      <label class="block">
        <span class="text-custom-first_text font-semibold">Description</span>
        <InputCard v-model="user.description" placeholder="A bit about you..." />
      </label>

      <label class="block">
        <span class="text-custom-first_text font-semibold">Email</span>
        <InputCard v-model="user.email" placeholder="Email" type="email" />
      </label>

    </div>

    <button @click="updateUserData" :disabled="isLoading"
      class="w-full bg-custom-highlight text-white font-bold rounded-lg p-3 hover:opacity-90 transition-opacity disabled:bg-gray-500">
      {{ isLoading ? 'Saving...' : 'Save Profile' }}
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const authCookie = useCookie('auth_data')
const toast = useToast()

const isLoading = ref(false)
const selectedFile = ref(null)

const user = ref({
  id: null,
  username: '',
  description: '',
  email: '',
  avatar_url: ''
})

const fetchUserData = async () => {
  try {
    const res = await fetch(`/clubeo_php_api/getUser.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${authCookie.value.token}`
      },
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

const onFileSelected = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    user.value.avatar_url = URL.createObjectURL(file)
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

const updateUserData = async () => {
  if (isLoading.value) return;
  isLoading.value = true

  try {
    let finalAvatarUrl = user.value.avatar_url

    if (selectedFile.value) {
      finalAvatarUrl = await uploadToCloudinary()
    }

    const res = await fetch(`/clubeo_php_api/updateUser.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${authCookie.value.token}`
      },
      body: JSON.stringify({
        username: user.value.username,
        description: user.value.description,
        email: user.value.email,
        avatar_url: finalAvatarUrl,
      })
    });

    const data = await res.json();

    if (res.ok) {
      toast.success({ title: 'Success', message: 'Profile updated!' })
      user.value.password = ''
      selectedFile.value = null
    } else {
      toast.error({ title: 'Error!', message: data.error || 'Update failed' });
    }

  } catch (error) {
    console.error(error);
    toast.error({ title: 'Error!', message: 'Failed to update profile' });
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  if (authCookie.value?.userId) {
    await fetchUserData();
  } else {
    navigateTo('/login');
  }
})
</script>