<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Sección para editar un evento existente -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="text-xl">Editar Evento</h2>
      </div>
      <div class="admin-card-body">
        <!-- Formulario de edición de evento -->
        <form @submit.prevent="submitEditForm">
          <div class="form-group">
            <label class="form-label">Seleccione Evento *</label>
            <!-- Selector de evento a editar -->
            <select v-model="selectedEventId" class="form-input" @change="loadEventToEdit">
              <option value="">Seleccione un evento</option>
              <option v-for="event in events" :key="event.id" :value="event.id">
                {{ event.title }} ({{ event.ciudad }})
              </option>
            </select>
          </div>

          <div v-if="editableEvent">
            <!-- Campo para editar el título del evento -->
            <div class="form-group">
              <label class="form-label">Título *</label>
              <input v-model="editableEvent.title" type="text" required class="form-input" />
            </div>
            <!-- Campo para editar la descripción -->
            <div class="form-group">
              <label class="form-label">Descripción</label>
              <textarea v-model="editableEvent.description" rows="3" class="form-input"></textarea>
            </div>
            <!-- Campo para editar la fecha y hora -->
            <div class="form-group">
              <label class="form-label">Fecha y Hora *</label>
              <input v-model="editableEvent.date" type="datetime-local" required class="form-input" />
            </div>
            <!-- Campo para editar la ciudad -->
            <div class="form-group">
              <label class="form-label">Ciudad *</label>
              <input v-model="editableEvent.city" type="text" required class="form-input" />
            </div>
            <!-- Campo para editar la dirección -->
            <div class="form-group">
              <label class="form-label">Dirección</label>
              <input v-model="editableEvent.address" type="text" class="form-input" />
            </div>
            <!-- Campo para editar la visibilidad -->
            <div class="form-group">
              <label class="form-label">Visibilidad *</label>
              <select v-model="editableEvent.visibilidad" class="form-input" required>
                <option value="Público">Público</option>
                <option value="Privado">Privado</option>
              </select>
            </div>
            <!-- Campo para editar el precio -->
            <div class="form-group">
              <label class="form-label">Precio *</label>
              <input v-model.number="editableEvent.price" type="number" required class="form-input" />
            </div>
            <div class="flex justify-center mt-4">
              <button
                type="submit"
                class="vintage-button enhanced-btn"
              >
                <i class="fas fa-save mr-2"></i> Actualizar Evento
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Sección para eliminar un evento existente -->
    <div class="admin-card">
      <div class="admin-card-header bg-red-700">
        <h2 class="text-xl">Eliminar Evento</h2>
      </div>
      <div class="admin-card-body">
        <!-- Formulario de eliminación de evento -->
        <form @submit.prevent="submitDeleteForm">
          <div class="form-group">
            <label class="form-label">Seleccione Evento *</label>
            <!-- Selector de evento a eliminar -->
            <select v-model="eventToDeleteId" class="form-input" @change="loadEventToDelete">
              <option value="">Seleccione un evento</option>
              <option v-for="event in events" :key="event.id" :value="event.id">
                {{ event.title }} ({{ event.ciudad }})
              </option>
            </select>
          </div>

          <div v-if="eventToDelete">
            <div class="mt-4 p-4 border border-red-200 bg-red-50 rounded-lg">
              <h4 class="font-bold text-red-800">{{ eventToDelete.title }}</h4>
              <!-- Muestra la fecha del evento a eliminar -->
              <p class="text-sm text-red-600 mt-1">{{ formattedDate(eventToDelete.date) }}</p>
              <!-- Muestra el precio del evento a eliminar -->
              <p class="text-sm text-gray-600 mt-1">Precio: {{ eventToDelete.price || "N/A" }}</p>
              <p class="text-sm text-gray-600 mt-2">Esta acción no se puede deshacer.</p>
              <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg w-full mt-4 hover:bg-red-700 transition">
                <i class="fas fa-trash-alt mr-2"></i> Confirmar Eliminación
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal de feedback para mostrar mensajes de éxito o error -->
  <div v-if="modalVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 text-center">
      <div class="flex flex-col items-center mb-4">
        <span v-if="modalSuccess" class="text-green-600 text-5xl mb-2">
          <i class="fas fa-check-circle"></i>
        </span>
        <span v-else class="text-red-600 text-5xl mb-2">
          <i class="fas fa-times-circle"></i>
        </span>
        <div class="text-xl font-bold mb-2" :class="modalSuccess ? 'text-green-700' : 'text-red-700'">
          {{ modalTitle }}
        </div>
        <div class="text-gray-700">{{ modalMsg }}</div>
      </div>
      <button @click="closeModal" class="btn-secondary px-6 py-2 rounded-md font-medium mt-2">Cerrar</button>
    </div>
  </div>
</template>

<script>
export default {
  // Recibe la lista de eventos como propiedad
  props: {
    events: Array,
  },
  data() {
    return {
      // ID del evento seleccionado para editar
      selectedEventId: null,
      // Objeto editable del evento
      editableEvent: null,
      // ID del evento seleccionado para eliminar
      eventToDeleteId: null,
      // Objeto del evento a eliminar
      eventToDelete: null,
      // Estado del modal de feedback
      modalVisible: false,
      modalMsg: '',
      modalSuccess: false,
      modalTitle: ''
    };
  },
  methods: {
    // Carga los datos del evento seleccionado para editar
    loadEventToEdit() {
      const selected = this.events.find(event => event.id === this.selectedEventId);
      if (selected) {
        // Maneja el formato de la fecha para el input datetime-local
        let eventDate;
        if (selected.date) {
          eventDate = new Date(selected.date);
        } else if (selected.fecha_evento) {
          eventDate = new Date(selected.fecha_evento);
        } else {
          eventDate = null;
        }
        // Formatea la fecha para el input
        const formattedDate = eventDate && !isNaN(eventDate.getTime())
          ? eventDate.toISOString().slice(0,16)
          : '';
        this.editableEvent = {
          ...selected,
          city: selected.ciudad,
          date: formattedDate,
          address: selected.address  // mapeo de dirección
        };
      } else {
        this.editableEvent = null;
      }
    },
    // Carga el evento seleccionado para eliminar
    loadEventToDelete() {
      this.eventToDelete = this.events.find(event => event.id === this.eventToDeleteId) || null;
    },
    // Envía el formulario de edición
    submitEditForm() {
      if (this.editableEvent) {
        const payload = {
          id: this.editableEvent.id,
          titulo: this.editableEvent.title,
          fecha_evento: new Date(this.editableEvent.date).toISOString().slice(0,19).replace('T',' '),
          ciudad: this.editableEvent.city,
          price: this.editableEvent.price,
          descripcion: this.editableEvent.description,
          address: this.editableEvent.address || '', // dirección
          capacidad_maxima: this.editableEvent.capacidad_maxima || '',
          visibilidad: this.editableEvent.visibilidad || 'Público'
        };
        fetch('http://localhost:8080/Apis/API_modificar_evento.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              this.showModal("El evento se ha actualizado correctamente.", true, "¡Evento actualizado!");
            } else {
              this.showModal("Error: " + (data.message || "Error al actualizar evento"), false, "Error al actualizar");
            }
          })
          .catch(error => {
            this.showModal("Error: " + error.message, false, "Error de red");
          });
      }
    },
    // Envía el formulario de eliminación
    submitDeleteForm() {
      if (this.eventToDelete) {
        const payload = { id: this.eventToDelete.id };
        fetch('http://localhost:8080/Apis/Api_eliminar_evento.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              this.showModal("El evento se ha eliminado correctamente.", true, "¡Evento eliminado!");
            } else {
              this.showModal("Error: " + (data.message || "No se pudo eliminar el evento"), false, "Error al eliminar");
            }
          })
          .catch(error => {
            this.showModal("Error: " + error.message, false, "Error de red");
          });
      }
    },
    // Muestra el modal de feedback
    showModal(msg, success = false, title = '') {
      this.modalMsg = msg;
      this.modalSuccess = success;
      this.modalTitle = title || (success ? '¡Operación exitosa!' : 'Error');
      this.modalVisible = true;
    },
    // Cierra el modal y recarga la página si la operación fue exitosa
    closeModal() {
      this.modalVisible = false;
      if (this.modalSuccess) {
        window.location.reload();
      }
    },
    // Formatea la fecha a un formato legible en español
    formattedDate(date) {
      const eventDate = new Date(date);
      return eventDate.toLocaleDateString("es-ES", {
        month: "long",
        day: "numeric",
        year: "numeric",
      });
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
/* Estética mejorada para el botón de actualizar */
.enhanced-btn {
  width: 95%;
  min-height: 3.5rem;
  font-size: 1.15rem;
  font-weight: 500;
  margin: 0.5rem 0;
  box-shadow: 0 4px 16px rgba(130, 83, 54, 0.10);
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.enhanced-btn:active {
  box-shadow: 0 2px 8px rgba(130, 83, 54, 0.15);
  transform: scale(0.98);
}
</style>