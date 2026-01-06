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
  height: 100vh;
  overflow: hidden;
}

.sidebar {
  width: 300px;
  height: 100%;      
  flex-shrink: 0;         
  overflow-y: auto;
}

.content {
  height: 100%;
  overflow-y: auto;
  flex-grow: 1;           
  padding: 50px;
}
</style>