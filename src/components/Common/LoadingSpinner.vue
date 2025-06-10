<template>
  <!-- Spinner de carga centrado -->
  <div class="flex justify-center items-center p-4">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-darkbutton"></div>
  </div>
</template>

<script setup>
// No needs script logic
</script>

<style scoped>
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

.rounded-full {
  border-radius: 9999px;
}

.h-12 {
  height: 3rem;
}

.w-12 {
  width: 3rem;
}

.border-b-2 {
  border-bottom-width: 2px;
  border-style: solid;
}

.border-darkbutton {
  border-color: #825336; /* Asegúrate de que este color coincida con tu variable darkbutton */
}
</style>
