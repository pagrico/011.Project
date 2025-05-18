<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-lg w-full relative animate-fade-in">
      <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">
        <i class="fas fa-times"></i>
      </button>
      <h2 class="text-2xl font-bold text-[#431605] mb-4">Detalle de Solicitud</h2>
      <div class="mb-4" v-if="solicitud">
        <div class="mb-2"><span class="font-semibold">Servicio:</span> {{ extraerServicio(solicitud.SOL_MENSAJE) }}</div>
        <div class="mb-2"><span class="font-semibold">Cliente:</span> {{ solicitud.SOL_NOMBRE }}</div>
        <div class="mb-2"><span class="font-semibold">Correo:</span> {{ solicitud.SOL_CORREO }}</div>
        <div class="mb-2">
          <span class="font-semibold">Fecha:</span>
          <!-- Muestra la fecha formateada y la original para depuración -->
          <span>{{ mostrarFecha(solicitud.SOL_FECHA) }}</span>
          <!-- <span class="text-xs text-gray-400 ml-2">(raw: {{ solicitud.SOL_FECHA }})</span> -->
        </div>
        <div class="mb-2"><span class="font-semibold">Estado:</span> {{ solicitud.SOL_ESTADO }}</div>
        <div class="mb-2"><span class="font-semibold">Mensaje:</span>
          <div class="bg-[#DBE6ED] rounded p-3 mt-1 whitespace-pre-line">{{ limpiarMensaje(solicitud.SOL_MENSAJE) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps(['solicitud'])
function mostrarFecha(fecha) {
  if (!fecha) return ''
  // Si ya es un número, asume timestamp
  if (!isNaN(Number(fecha))) {
    const d = new Date(Number(fecha))
    if (!isNaN(d.getTime())) return d.toLocaleString('es-ES')
  }
  // Si es string tipo '2024-06-07 13:45:00', reemplaza espacio por T
  let f = fecha
  if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(fecha)) {
    f = fecha.replace(' ', 'T')
  }
  const d = new Date(f)
  if (!isNaN(d.getTime())) return d.toLocaleString('es-ES')
  // Si sigue fallando, muestra el valor original
  return fecha
}
function extraerServicio(mensaje) {
  if (!mensaje) return ''
  const match = mensaje.match(/^\[Servicio: (.+?)\]/)
  return match ? match[1] : 'General'
}
function limpiarMensaje(mensaje) {
  if (!mensaje) return ''
  return mensaje.replace(/^\[Servicio: .+?\]\n?/, '')
}
</script>
<style scoped>
.animate-fade-in {
  animation: fadeIn .2s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px);}
  to { opacity: 1; transform: none;}
}
</style>
