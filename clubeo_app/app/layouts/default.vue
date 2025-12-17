<template>
  <div class="main-container">
    
    <aside class="sidebar bg-custom-cards_menu">
      <Menu />
    </aside>

    <main class="content bg-custom-background">
      <slot />
    </main>

  </div>
</template>

<style scoped>
.main-container {
  display: flex;
  width: 100%;
  min-height: 100vh;
}

.sidebar {
  width: 300px;         
  flex-shrink: 0;         
  padding: 20px;
}

.content {
  flex-grow: 1;           
  padding: 20px;
}
</style>