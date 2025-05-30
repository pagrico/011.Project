<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div v-if="events.length === 0" class="text-gray-500 italic col-span-3">
      No se encontraron eventos
    </div>

    <div
      v-for="event in events"
      :key="event.id"
      class="vintage-card rounded-lg overflow-hidden"
    >
      <!-- Se agrega flex column y altura completa al contenedor interno -->
      <div class="p-4 flex flex-col h-full">
        <h3 class="text-xl font-bold vintage-header mb-2">{{ event.title }}</h3>
        <p class="text-gray-700 mb-3">{{ event.description }}</p>
        <div class="flex items-center text-sm text-gray-600 mb-2">
          <i class="fas fa-calendar-day mr-2"></i>
          <span>{{ formattedDate(event.date) }}</span>
        </div>
        <div class="flex items-center text-sm text-gray-600 mb-4">
          <i class="fas fa-map-marker-alt mr-2"></i>
          <span>{{ event.address ? event.address + ', ' : '' }}{{ event.ciudad }}</span>
        </div>
        <!-- Se envuelve el botón para mantenerlo en la parte inferior -->
        <div class="mt-auto">
          <button
            @click="$emit('add-to-cart', event)"
            class="vintage-button px-4 py-2 rounded-lg w-full"
          >
            <i class="fas fa-ticket-alt mr-2"></i> 
            Comprar entrada (€{{ event.price.toFixed(2) }})
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    events: Array,
  },
  methods: {
    formattedDate(date) {
      const eventDate = new Date(date);
      return eventDate.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
  },
};
</script>

<style scoped>
.vintage-card {
  border: 1px solid #BCAAA1;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  background-color: #B7CDDA;
}
.vintage-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
.vintage-button {
  background-color: #825336;
  color: white;
  transition: all 0.3s ease;
}
.vintage-button:hover {
  background-color: #431605;
  transform: translateY(-2px);
}
.vintage-header {
  font-family: 'Playfair Display', serif;
  color: #1F1E1E;
}
</style>