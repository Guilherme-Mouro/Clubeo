<template>
  <div class="container">

    <h1>Gestão de Utilizadores</h1>

    <form @submit.prevent="createUser" class="form">
      <input
        type="text"
        v-model="name"
        placeholder="Nome"
        required
      />

      <input
        type="email"
        v-model="email"
        placeholder="Email"
        required
      />

      <button type="submit">Criar Utilizador</button>
    </form>

    <hr />

    <h2>Utilizadores Existentes</h2>

    <div v-if="users.length === 0">
      Nenhum utilizador encontrado.
    </div>

    <ul>
      <li v-for="user in users" :key="user.id">
        <strong>{{ user.name }}</strong> — {{ user.email }}
      </li>
    </ul>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const name = ref('')
const email = ref('')
const users = ref([])

async function loadUsers() {
  const res = await fetch('/php/list_users.php')
  users.value = await res.json()
}

async function createUser() {
  const newUser = { name: name.value, email: email.value }

  const res = await fetch('/php/create_user.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(newUser)
  })

  const data = await res.json()

  await loadUsers()

  name.value = ''
  email.value = ''
}

onMounted(() => {
  loadUsers()
})
</script>

<style scoped>
.container {
  max-width: 500px;
  margin: 40px auto;
  font-family: Arial, sans-serif;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

input {
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #bbb;
  font-size: 1rem;
}

button {
  padding: 10px;
  background: #0070f3;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}

button:hover {
  background: #005bd1;
}

ul {
  list-style: none;
  padding-left: 0;
}

li {
  margin-bottom: 8px;
}
</style>
