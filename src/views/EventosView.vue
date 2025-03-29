<template>
  <div>
    <h1>Prueba de conexión con el backend</h1>
    <div v-if="response">
      <p><strong>Estado:</strong> {{ response.status }}</p>
      <p><strong>Mensaje:</strong> {{ response.message }}</p>
    </div>
    <div v-else>
      <p>Cargando...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Estado reactivo para almacenar la respuesta del backend
const response = ref(null);

// Función para obtener datos del backend
const fetchData = async () => {
  try {
    const res = await axios.get('http://localhost:8000/backend/api.php'); // Cambia la URL si es necesario
    response.value = res.data; // Almacena la respuesta en el estado
  } catch (error) {
    console.error('Error al conectar con el backend:', error);
    response.value = { status: 'error', message: 'No se pudo conectar con el backend' };
  }
};

// Llamar a la función al montar el componente
onMounted(() => {
  fetchData();
});
</script>

<style scoped>
h1 {
  font-size: 1.5rem;
  color: #431605;
}

p {
  font-size: 1rem;
  color: #1F1E1E;
}
</style>