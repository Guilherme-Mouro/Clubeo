<template>
  <div v-for="post in posts" :key="post.id">
    <PostCard :username="post.username" :user_avatar="post.avatar_url" :created_at="post.created_at"
      :content="post.content" :likes_num="post.likes_num" @likePost="likePost(post.id)" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const authCookie = useCookie('auth_data')
const toast = useToast()

const posts = ref([])

const fetchHomePosts = async () => {
  try {
    const res = await fetch(`/clubeo_php_api/getHomePosts.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${authCookie.value.token}`
      },
    });

    const data = await res.json();

    if (res.ok) {
      posts.value = data
    }

  } catch (error) {
    toast.error({ title: 'Error!', message: 'Connection error!' })
  }
}

const likePost = async (postId) => {
  try {
    const res = await fetch(`/clubeo_php_api/likePost.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${authCookie.value.token}`
      },
      body: JSON.stringify({ postId: postId })
    });

    if (res.ok) fetchHomePosts();
  } catch (error) {
    console.error(error)
  }
}

onMounted(async () => {
  fetchHomePosts()
})
</script>