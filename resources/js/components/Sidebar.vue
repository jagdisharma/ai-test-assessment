<template>
  <div>
    <div v-if="visible && isMobile" class="sidebar__overlay" @click="$emit('close')"></div>
    <nav :class="['sidebar', { 'sidebar--visible': visible }]">
      <div class="sidebar__logo">
        <i class="pi pi-ticket"></i>
        <span>SmartTicket</span></div>
      <ul class="sidebar__menu">
        <li class="sidebar__item">
          <router-link class="sidebar__link" to="/dashboard"><i class="pi pi-home"></i> Dashboard</router-link>
        </li>
        <li class="sidebar__item">
          <router-link class="sidebar__link" to="/tickets"><i class="pi pi-list"></i> Tickets</router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    isMobile() {
      return window.innerWidth <= 600;
    }
  }
};
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 220px;
  height: 100vh;
  background: var(--color-bg-gradient);
  color: var(--color-black);
  display: flex;
  flex-direction: column;   
  z-index: 2000;
  transition: width 0.3s, transform 0.3s;
  box-shadow: 2px 0 24px 0 var(--color-shadow);
}
@media (max-width: 900px) {
  .sidebar {
    width: 60px;
  }
}
@media (max-width: 600px) {
  .sidebar {
    display: none !important;
    width: 80vw;
    max-width: 320px;
    transform: translateX(-100%);
    box-shadow: none;
    height: 100vh;
  }
  .sidebar.sidebar--visible {
    display: flex !important;
    transform: translateX(0);
    box-shadow: 2px 0 16px var(--color-shadow-strong);
  }
}
.sidebar__overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: var(--color-shadow-strong);
  z-index: 1999;
  display: block;
}
.sidebar__logo {
  font-size: 2rem;
  font-weight: 800;
  padding: 2rem 1.5rem 1.5rem 1.5rem;
  letter-spacing: 1px;
  color: var(--color-accent);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.sidebar__logo span {
  color: var(--color-black);
  font-size: 1.2rem;
  font-weight: 700;
}
.sidebar__logo i {
  margin-right: 0.5rem;
  font-size: 1.5rem;
}
.sidebar__menu {
  list-style: none;
  padding: 0;
  margin: 0;
  flex: 1;
}
.sidebar__item {
  margin-bottom: 1rem;
}
.sidebar__link {
  display: block;
  padding: 1rem 1.5rem;
  color: var(--color-black);
  text-decoration: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.sidebar__link.router-link-exact-active {
  background: var(--color-accent);
  color: var(--color-primary);
  box-shadow: 0 2px 8px var(--color-shadow-strong);
}
.sidebar__link:hover {
  background: var(--color-secondary);
  color: var(--color-white);
  box-shadow: 0 2px 8px var(--color-shadow-strong);
}
.sidebar__link i {
  margin-right: 0.7rem;
  font-size: 1.1rem;
  vertical-align: middle;
}
@media (max-width: 900px) {
  .sidebar__logo {
    font-size: 1.1rem;
    padding: 1rem 0.5rem;
    text-align: center;
  }
  .sidebar__link {
    padding: 1rem 0.5rem;
    font-size: 0.95rem;
    text-align: center;
  }
  .sidebar__item {
    margin-bottom: 0.5rem;
  }
}
</style> 