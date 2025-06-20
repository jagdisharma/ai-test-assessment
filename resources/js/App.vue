<template>
  <div class="app-layout" :class="theme">
    <button
      v-if="showSidebarToggle"
      class="sidebar-toggle"
      @click="sidebarVisible = true"
    >
      <i class="pi pi-bars sidebar-toggle__icon"></i>
    </button>
    <button class="theme-toggle" @click="toggleTheme">
      <i :class="theme === 'dark' ? 'pi pi-sun' : 'pi pi-moon'"></i>
      {{ theme === 'dark' ? 'Light' : 'Dark' }} Mode
    </button>
    <Sidebar
      v-if="showSidebar"
      :visible="sidebarVisible || !isMobile"
      @close="sidebarVisible = false"
    />
    <div class="app-layout__content" @click="isMobile && sidebarVisible ? sidebarVisible = false : null">
      <router-view />
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Sidebar from './components/Sidebar.vue';
export default {
  name: 'App',
  components: { Sidebar },
  setup() {
    const sidebarVisible = ref(false);
    const windowWidth = ref(window.innerWidth);
    const isMobile = computed(() => windowWidth.value <= 600);
    const showSidebar = computed(() => windowWidth.value > 600 || sidebarVisible.value);
    const showSidebarToggle = computed(() => windowWidth.value <= 600 && !sidebarVisible.value);
    const handleResize = () => {
      windowWidth.value = window.innerWidth;
      if (window.innerWidth > 600) sidebarVisible.value = false;
    };
    const theme = ref(localStorage.getItem('theme') || 'light');
    const toggleTheme = () => {
      theme.value = theme.value === 'light' ? 'dark' : 'light';
      localStorage.setItem('theme', theme.value);
      document.documentElement.setAttribute('data-theme', theme.value);
    };
    onMounted(() => {
      window.addEventListener('resize', handleResize);
      document.documentElement.setAttribute('data-theme', theme.value);
    });
    onUnmounted(() => {
      window.removeEventListener('resize', handleResize);
    });
    return { sidebarVisible, isMobile, showSidebar, showSidebarToggle, theme, toggleTheme };
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
:global(body) {
  background: var(--color-bg-gradient) !important;
  font-family: 'Poppins', Arial, Helvetica, sans-serif;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  color: var(--color-text);
}
.app-layout {
  font-family: 'Poppins', Arial, Helvetica, sans-serif;
  min-height: 100vh;
  display: flex;
}
.theme-toggle {
  position: fixed;
  top: 1.2rem;
  right: 1.2rem;
  z-index: 2100;
  background: var(--color-primary);
  color: var(--color-white);
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  padding: 0.5rem 1.2rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 2px 8px var(--color-shadow-strong);
  transition: background 0.2s, box-shadow 0.2s;
  cursor: pointer;
}
.theme-toggle:hover {
  background: var(--color-secondary);
  color: var(--color-primary);
  box-shadow: 0 4px 16px var(--color-shadow-strong);
}
.sidebar-toggle {
  position: fixed;
  top: 1.2rem;
  left: 1.2rem;
  z-index: 2001;
  background: var(--color-primary);
  color: var(--color-white);
  border: none;
  border-radius: 12px;
  font-size: 2rem;
  width: 48px;
  height: 48px;
  display: none;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px var(--color-shadow-strong);
  transition: background 0.2s, box-shadow 0.2s;
}
.sidebar-toggle__icon {
  font-size: 2rem;
}
.sidebar-toggle:hover {
  background: var(--color-secondary);
  color: var(--color-primary);
  box-shadow: 0 4px 16px var(--color-shadow-strong);
}
@media (max-width: 600px) {
  .sidebar-toggle {
    display: flex;
  }
}
.app-layout__content {
  min-height: 100vh;
  width: 100%;
  padding: 2rem 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  transition: margin-left 0.3s;
  overflow-x: hidden;
  background: rgba(255,255,255,0.7);
  background: var(--color-accent, #fff);
  box-shadow: 0 0 32px 0 var(--color-shadow);
  margin-left: 220px;
}
@media (max-width: 900px) {
  .app-layout__content {
    margin-left: 60px;
    padding: 1rem 0;
    border-radius: 1rem 0 0 1rem;
  }
}
@media (max-width: 600px) {
  .app-layout__content {
    margin-left: 0;
    padding: 1rem 0.5rem;
    border-radius: 0;
  }
}
</style>

/* Move theme variables to global scope */
<style>
:root {
  font-family: 'Poppins', Arial, Helvetica, sans-serif;
  --color-bg: #FAEAB1;
  --color-bg-gradient: linear-gradient(135deg, #FAEAB1 0%, #E5BA73 100%);
  --color-primary: #C58940;
  --color-secondary: #E5BA73;
  --color-accent: #FAEAB1;
  --color-white: #fff;
  --color-black: #222;
  --color-shadow: rgba(197,137,64,0.08);
  --color-shadow-strong: rgba(197,137,64,0.18);
  --color-text: #222;
}
[data-theme='dark'] {
  --color-bg: #23272f;
  --color-bg-gradient: linear-gradient(135deg, #23272f 0%, #343a40 100%);
  --color-primary: #E5BA73;
  --color-secondary: #C58940;
  --color-accent: #343a40;
  --color-white: #23272f;
  --color-black: #fff;
  --color-shadow: rgba(229,186,115,0.08);
  --color-shadow-strong: rgba(229,186,115,0.18);
  --color-text: #fff;
}
</style>
