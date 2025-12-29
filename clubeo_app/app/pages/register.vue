<template>
  <AuthCard title="Join Us!" button="Register" @login="register">
    <form class="flex flex-col" :class="{ 'shake-animation': isShaking }" @submit.prevent>

      <InputCard v-model="form.username" placeholder="Username" :has-error="isUsernameInvalid" />
      <div v-if="isUsernameInvalid">
        <InputWarning :message="errorMessages.username" />
      </div>

      <InputCard v-model="form.email" placeholder="Email" type="email" :has-error="isEmailInvalid" />
      <div v-if="isEmailInvalid">
        <InputWarning :message="errorMessages.email" />
      </div>

      <InputCard v-model="form.password" placeholder="Password" type="password" :has-error="isPasswordInvalid" />
      <div v-if="isPasswordInvalid">
        <InputWarning :message="errorMessages.password" />
      </div>

      <InputCard v-model="form.confirmPassword" placeholder="Confirm Password" type="password"
        :has-error="isConfirmPassInvalid" />
      <div v-if="isConfirmPassInvalid">
        <InputWarning :message="errorMessages.confirmPass" />
      </div>

    </form>

    <p class="text-custom-first_text mt-5">
      Already have an account?
      <NuxtLink class="text-custom-highlight" to="/login">
        Login here!
      </NuxtLink>
    </p>

  </AuthCard>
</template>


<script setup>
import { ref, computed, watch } from 'vue'

definePageMeta({
  layout: 'auth'
})

const toast = useToast()

const isShaking = ref(false);

const triggerShake = () => {
  isShaking.value = true

  setTimeout(() => {
    isShaking.value = false
  }, 400)
};

const form = ref({
  username: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const serverErrors = ref({
  username: '',
  email: ''
})

watch(() => form.value.username, () => { serverErrors.value.username = '' })
watch(() => form.value.email, () => { serverErrors.value.email = '' })

const errorMessages = computed(() => {
  return {
    username: (form.value.username.length > 0 && form.value.username.length < 3)
      ? 'Username is too short'
      : serverErrors.value.username,

    email: (form.value.email.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email))
      ? 'Invalid email format'
      : serverErrors.value.email,

    password: (form.value.password.length > 0 && form.value.password.length < 6)
      ? 'Password must be at least 6 characters'
      : '',

    confirmPass: (form.value.password != form.value.confirmPassword && form.value.confirmPassword != '')
      ? 'Passwords must be the same'
      : ''
  }
})

const isUsernameInvalid = computed(() => errorMessages.value.username !== '')
const isEmailInvalid = computed(() => errorMessages.value.email !== '')
const isPasswordInvalid = computed(() => errorMessages.value.password !== '')
const isConfirmPassInvalid = computed(() => errorMessages.value.confirmPass !== '')

const register = async () => {
  const emptyFields = !form.value.username || !form.value.email || !form.value.password;
  
  if (emptyFields) {
    triggerShake();
    toast.error({ title: 'Error!', message: 'Please fill in all fields!' });
    return;
  }
  const hasErrors = isUsernameInvalid.value || isEmailInvalid.value || isPasswordInvalid.value || isConfirmPassInvalid.value

  if (hasErrors) {
    triggerShake()
    return
  }

  try {
    const res = await fetch("/clubeo_php_api/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        username: form.value.username,
        email: form.value.email,
        password: form.value.password,
      })
    })

    const data = await res.json();

    if (res.status === 409) {
      if (data.usernameTaken) serverErrors.value.username = 'Username already in use'
      if (data.emailTaken) serverErrors.value.email = 'Email already in use'
      triggerShake()
      return
    }

    if (!res.ok) {
      toast.error({ title: 'Error!', message: data.error || "An error occurred" })
      return;
    }

    toast.success({ title: 'Success!', message: 'Account created successfully!' })
    localStorage.setItem('userId', data.userId)
    navigateTo('/');

  } catch (error) {
    console.error(error);
    toast.error({ title: 'Error!', message: 'Connection error!' })
  }
}

</script>

<style scoped>
@keyframes shake {

  0%,
  100% {
    transform: translateX(0)
  }

  20%,
  60% {
    transform: translateX(-5px)
  }

  40%,
  80% {
    transform: translateX(5px)
  }
}

.shake-animation {
  animation: shake 0.4s ease-in-out
}
</style>