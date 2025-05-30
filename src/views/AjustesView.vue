<template>
  <div class="p-6 mt-20">
    <div class="max-w-6xl mx-auto">
      <!-- Datos personales y direcciones -->
      <DatosPersonales />
      
      <!-- Admin Panel - Visible solo para admin -->
      <AdminPanel v-if="isAdmin" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import DatosPersonales from '../components/Ajustes/DatosPersonales.vue';
import AdminPanel from '../components/Ajustes/AdminPanel.vue';

// Verificar si el usuario es administrador
const isAdmin = computed(() => {
  try {
    const userData = JSON.parse(localStorage.getItem('userData') || '{}');
    return userData && userData.role === 1;
  } catch {
    return false;
  }
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Open+Sans:wght@300;400;600&display=swap');

body {
  font-family: 'Open Sans', sans-serif;
  background-color: #DBE6ED;
  color: #1F1E1E;
}

.vintage-input {
  background-color: rgba(255, 255, 255, 0.7);
  border: 1px solid #BCAEA1;
  border-radius: 4px;
  padding: 8px 12px;
  font-size: 14px;
  transition: all 0.3s;
}

.vintage-input:focus {
  outline: none;
  border-color: #825336;
  box-shadow: 0 0 0 2px rgba(193, 143, 103, 0.3);
}

.vintage-card {
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(183, 205, 218, 0.5);
  transition: all 0.3s;
}

.vintage-card:hover {
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}

.vintage-button {
  background-color: #C18F67;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 8px 16px;
  font-size: 14px;
  transition: all 0.3s;
  font-weight: 600;
}

.vintage-button:hover {
  background-color: #825336;
  transform: translateY(-1px);
}

.vintage-button-secondary {
  background-color: #B7CDDA;
  color: #1F1E1E;
}

.vintage-button-secondary:hover {
  background-color: #9ab7c7;
}

.section-title {
  font-family: 'Cormorant Garamond', serif;
  font-weight: 600;
  color: #431605;
  position: relative;
  display: inline-block;
}

.section-title::after {
  content: "";
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 100%;
  height: 1px;
  background-color: #C18F67;
}
</style>
