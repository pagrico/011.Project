<template>
  <div>
    <!-- Botón para abrir/cerrar el panel de administración -->
    <button
      @click="toggleAdminPanel"
      class="vintage-button px-6 py-3 rounded-lg font-medium flex items-center"
    >
      <i class="fas fa-user-shield mr-3"></i>
      <span class="text-lg">Panel de Administración</span>
      <i
        :class="`fas fa-chevron-${isAdminPanelOpen ? 'up' : 'down'} ml-2 transition-transform duration-200`"
      ></i>
    </button>

    <!-- Panel de administración con pestañas -->
    <div v-if="isAdminPanelOpen" class="mt-4">
      <div class="flex border-b border-gray-200 mb-6">
        <!-- Pestañas para cambiar entre crear, gestionar y estadísticas -->
        <div
          v-for="tab in tabs"
          :key="tab.id"
          :class="['admin-tab', { active: activeTab === tab.id }]"
          @click="activeTab = tab.id"
        >
          {{ tab.name }}
        </div>
      </div>

      <!-- Formulario para crear eventos -->
      <div v-if="activeTab === 'create'">
        <CreateEventForm @event-created="addEvent" />
      </div>
      <!-- Gestión de eventos existentes -->
      <div v-if="activeTab === 'manage'">
        <ManageEvents :events="events" @update-events="updateEvents" />
      </div>
      <!-- Estadísticas de eventos -->
      <div v-if="activeTab === 'stats'">
        <EventStatistics :events="events" />
      </div>
    </div>
  </div>
</template>

<script>
import CreateEventForm from "../admin/CreateEventForm.vue";
import ManageEvents from "../admin/ManageEvent.vue";
import EventStatistics from "../admin/EventStatistics.vue";

export default {
  components: {
    CreateEventForm,
    ManageEvents,
    EventStatistics,
  },
  props: {
    events: Array,
  },
  data() {
    return {
      isAdminPanelOpen: false,
      activeTab: "create",
      tabs: [
        { id: "create", name: "Crear Evento" },
        { id: "manage", name: "Gestionar Eventos" },
        { id: "stats", name: "Estadísticas" },
      ],
    };
  },
  methods: {
    toggleAdminPanel() {
      this.isAdminPanelOpen = !this.isAdminPanelOpen;
    },
    addEvent(event) {
      this.$emit("update-events", [...this.events, event]);
    },
    updateEvents(events) {
      this.$emit("update-events", events);
    },
  },
};
</script>

<style scoped>
.vintage-button {
  background-color: #825336;
  color: white;
  transition: all 0.3s ease;
}
.vintage-button:hover {
  background-color: #431605;
  transform: translateY(-2px);
}
/* Añadido: aplica el color marrón a los botones de tipo submit en formularios de crear y actualizar */
form button[type="submit"],
button.update-btn {
  background-color: #825336 !important;
  color: white !important;
  transition: all 0.3s ease;
}
form button[type="submit"]:hover,
button.update-btn:hover {
  background-color: #431605 !important;
  color: white !important;
  transform: translateY(-2px);
}
.admin-tab {
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
  color: #431605;
  background: none;
}
.admin-tab.active {
  border-bottom-color: #825336;
  font-weight: 500;
  color: #431605;
}
.admin-tab:hover {
  background-color: rgba(219, 230, 237, 0.5);
}
.vintage-header {
  font-family: 'Playfair Display', serif;
  color: #1F1E1E;
}
.vintage-dark {
  background-color: #431605;
  color: white;
}
</style>