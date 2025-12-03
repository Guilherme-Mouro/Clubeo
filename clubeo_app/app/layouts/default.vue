<template>
  <div class="main-container">
    
    <aside class="sidebar">
      <Menu />
    </aside>

    <main class="content">
      <slot />
    </main>

  </div>
</template>

<style scoped>
.main-container {
  display: flex;           
  width: 100%;
  max-width: 1200px;     
  margin: 0 auto;          
  min-height: 100vh;       
}

.sidebar {
  width: 250px;         
  flex-shrink: 0;         
  padding: 20px;
}

.content {
  flex-grow: 1;           
  padding: 20px;
}
</style>