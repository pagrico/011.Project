<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Editar Evento -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="text-xl">Editar Evento</h2>
      </div>
      <div class="admin-card-body">
        <form @submit.prevent="submitEditForm">
          <div class="form-group">
            <label class="form-label">Seleccione Evento *</label>
            <select v-model="selectedEventId" class="form-input" @change="loadEventToEdit">
              <option value="">Seleccione un evento</option>
              <option v-for="event in events" :key="event.id" :value="event.id">
                {{ event.title }} ({{ event.ciudad }})
              </option>
            </select>
          </div>

          <div v-if="editableEvent">
            <div class="form-group">
              <label class="form-label">Título *</label>
              <input v-model="editableEvent.title" type="text" required class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label">Descripción</label>
              <textarea v-model="editableEvent.description" rows="3" class="form-input"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Fecha y Hora *</label>
              <input v-model="editableEvent.date" type="datetime-local" required class="form-input" />
            </div>
            <!-- Nuevo campo: Ciudad -->
            <div class="form-group">
              <label class="form-label">Ciudad *</label>
              <input v-model="editableEvent.city" type="text" required class="form-input" />
            </div>
            <!-- Nuevo campo: Dirección -->
            <div class="form-group">
              <label class="form-label">Dirección</label>
              <input v-model="editableEvent.address" type="text" class="form-input" />
            </div>
            <!-- Nuevo campo: Visibilidad -->
            <div class="form-group">
              <label class="form-label">Visibilidad *</label>
              <select v-model="editableEvent.visibilidad" class="form-input" required>
                <option value="Público">Público</option>
                <option value="Privado">Privado</option>
              </select>
            </div>
            <!-- Nuevo campo Precio -->
            <div class="form-group">
              <label class="form-label">Precio *</label>
              <input v-model.number="editableEvent.price" type="number" required class="form-input" />
            </div>
            <button type="submit" class="vintage-button px-6 py-3 rounded-lg w-full mt-4">
              <i class="fas fa-save mr-2"></i> Actualizar Evento
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Eliminar Evento -->
    <div class="admin-card">
      <div class="admin-card-header bg-red-700">
        <h2 class="text-xl">Eliminar Evento</h2>
      </div>
      <div class="admin-card-body">
        <form @submit.prevent="submitDeleteForm">
          <div class="form-group">
            <label class="form-label">Seleccione Evento *</label>
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
              <!-- Se muestra la fecha del evento -->
              <p class="text-sm text-red-600 mt-1">{{ formattedDate(eventToDelete.date) }}</p>
              <!-- Nuevo campo Precio -->
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
</template>

<script>
export default {
  props: {
    events: Array,
  },
  data() {
    return {
      selectedEventId: null,
      editableEvent: null,
      eventToDeleteId: null,
      eventToDelete: null,
    };
  },
  methods: {
    loadEventToEdit() {
      const selected = this.events.find(event => event.id === this.selectedEventId);
      if (selected) {
        // Si el evento ya tiene un campo 'date' (por ejemplo, si fue editado antes), úsalo.
        // Si no, usa 'fecha_evento' y conviértelo al formato adecuado para datetime-local.
        let eventDate;
        if (selected.date) {
          // Si ya existe el campo 'date' (por ejemplo, tras una edición previa)
          eventDate = new Date(selected.date);
        } else if (selected.fecha_evento) {
          eventDate = new Date(selected.fecha_evento);
        } else {
          eventDate = null;
        }
        // Formatear la fecha para el input datetime-local (YYYY-MM-DDTHH:MM)
        const formattedDate = eventDate && !isNaN(eventDate.getTime())
          ? eventDate.toISOString().slice(0,16)
          : '';
        this.editableEvent = {
          ...selected,
          city: selected.ciudad,
          date: formattedDate,
          address: selected.address  // se agrega el mapeo de address
        };
      } else {
        this.editableEvent = null;
      }
    },
    loadEventToDelete() {
      this.eventToDelete = this.events.find(event => event.id === this.eventToDeleteId) || null;
    },
    submitEditForm() {
      if (this.editableEvent) {
        const payload = {
          id: this.editableEvent.id,
          titulo: this.editableEvent.title,
          fecha_evento: new Date(this.editableEvent.date).toISOString().slice(0,19).replace('T',' '),
          ciudad: this.editableEvent.city,
          price: this.editableEvent.price,
          descripcion: this.editableEvent.description,
          address: this.editableEvent.address || '', // se cambia de "calle" a "address"
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
              alert("¡Evento actualizado con éxito!");
              window.location.reload(); // refrescar la pantalla al actualizar
            } else {
              alert("Error: " + (data.message || "Error al actualizar evento"));
            }
          })
          .catch(error => {
            alert("Error: " + error.message);
          });
      }
    },
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
              alert("¡Evento eliminado con éxito!");
              window.location.reload(); // refrescar la pantalla al eliminar
            } else {
              alert("Error: " + (data.message || "No se pudo eliminar el evento"));
            }
          })
          .catch(error => {
            alert("Error: " + error.message);
          });
      }
    },
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