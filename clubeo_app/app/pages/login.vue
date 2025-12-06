<template>
    <AuthCard title="Welcome Back!" button="Login" @login="login">
        <form>
            <input v-model="form.username" placeholder="Username"></input>
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
    password: '',
})

const login = async () => {
}
</script>