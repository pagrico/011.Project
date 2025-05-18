<template>
  <div>
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

    <div v-if="isAdminPanelOpen" class="mt-4">
      <div class="flex border-b border-gray-200 mb-6">
        <div
          v-for="tab in tabs"
          :key="tab.id"
          :class="['admin-tab', { active: activeTab === tab.id }]"
          @click="activeTab = tab.id"
        >
          {{ tab.name }}
        </div>
      </div>

      <div v-if="activeTab === 'create'">
        <CreateEventForm @event-created="addEvent" />
      </div>
      <div v-if="activeTab === 'manage'">
        <ManageEvents :events="events" @update-events="updateEvents" />
      </div>
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