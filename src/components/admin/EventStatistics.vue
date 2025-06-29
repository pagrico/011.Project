<template>
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="text-xl">Estadísticas de Eventos</h2>
    </div>
    <div class="admin-card-body">
      <!-- Tarjetas de resumen de eventos -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total de eventos -->
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
          <div class="text-blue-800 font-bold text-3xl mb-2">{{ totalEvents }}</div>
          <div class="text-blue-600">Total de Eventos</div>
        </div>
        <!-- Eventos públicos -->
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
          <div class="text-green-800 font-bold text-3xl mb-2">{{ publicEvents }}</div>
          <div class="text-green-600">Eventos Públicos</div>
        </div>
        <!-- Eventos privados -->
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
          <div class="text-purple-800 font-bold text-3xl mb-2">{{ privateEvents }}</div>
          <div class="text-purple-600">Eventos Privados</div>
        </div>
      </div>

      <!-- Tabla de próximos eventos -->
      <div class="mt-8">
        <h3 class="text-lg font-semibold mb-4">Próximos Eventos</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
              <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Evento
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Localización
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aforo
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Precio
              </th>
              </tr>
            </thead>
            <tbody id="upcomingEventsTable" class="divide-y divide-gray-200">
              <!-- Itera sobre los próximos eventos ordenados -->
              <tr v-for="event in sortedUpcomingEvents" :key="event.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium">{{ event.title }}</div>
                  <div class="text-sm text-gray-500">{{ event.visibilidad }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formattedDate(event.date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ event.ciudad }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ event.capacity || "Unlimited" }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ event.price || "N/A" }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  // Recibe la lista de eventos como propiedad
  props: {
    events: Array,
  },
  computed: {
    // Calcula el total de eventos
    totalEvents() {
      return this.events.length;
    },
    // Calcula la cantidad de eventos públicos
    publicEvents() {
      return this.events.filter((event) => event.visibilidad === "Público").length;
    },
    // Calcula la cantidad de eventos privados
    privateEvents() {
      return this.events.filter((event) => event.visibilidad === "Privado").length;
    },
    // Devuelve los próximos 5 eventos ordenados por fecha
    sortedUpcomingEvents() {
      return [...this.events]
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(0, 5);
    },
  },
  methods: {
    // Formatea la fecha del evento a un formato legible
    formattedDate(date) {
      const eventDate = new Date(date);
      return eventDate.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
  },
};
</script>