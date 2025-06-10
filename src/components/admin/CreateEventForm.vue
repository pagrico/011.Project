<template>
  <!-- Tarjeta principal del formulario de creación de eventos -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="text-xl">Crear Nuevo Evento</h2>
    </div>
    <div class="admin-card-body">
      <!-- Formulario de creación de evento -->
      <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Columna izquierda del formulario -->
        <div>
          <!-- Campo: Título del evento -->
          <div class="form-group">
            <label class="form-label">Título del evento *</label>
            <input 
              v-model="form.title" 
              type="text" 
              required 
              class="form-input" 
              placeholder="Ingrese el título del evento" 
            />
          </div>
          <!-- Campo: Descripción del evento -->
          <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea 
              v-model="form.description" 
              rows="3" 
              class="form-input" 
              placeholder="Describa su evento"
            ></textarea>
          </div>
          <!-- Campo: Fecha y hora del evento -->
          <div class="form-group">
            <label class="form-label">Fecha y hora *</label>
            <input 
              v-model="form.date" 
              type="datetime-local" 
              required 
              class="form-input" 
            />
          </div>
        </div>

        <!-- Columna derecha del formulario -->
        <div>
          <!-- Campo: Ciudad -->
          <div class="form-group">
            <label class="form-label">Ciudad *</label>
            <input 
              v-model="form.city" 
              type="text" 
              required 
              class="form-input" 
              placeholder="Ingrese la ciudad" 
            />
          </div>
          <!-- Campo: Dirección -->
          <div class="form-group">
            <label class="form-label">Dirección</label>
            <input 
              v-model="form.address" 
              type="text" 
              class="form-input" 
              placeholder="Ingrese la dirección del lugar" 
            />
          </div>
          <!-- Campo: Capacidad máxima -->
          <div class="form-group">
            <label class="form-label">Capacidad</label>
            <input 
              v-model.number="form.capacity" 
              type="number" 
              class="form-input" 
              placeholder="Máximo de asistentes" 
            />
          </div>
          <!-- Campo: Visibilidad del evento -->
          <div class="form-group">
            <label class="form-label">Visibilidad *</label>
            <select 
              v-model="form.visibility" 
              required 
              class="form-input"
            >
              <option value="Público">Público</option>
              <option value="Privado">Privado</option>
            </select>
          </div>
          <!-- Campo: Precio del evento -->
          <div class="form-group">
            <label class="form-label">Precio *</label>
            <input 
              v-model.number="form.price" 
              type="number" 
              required 
              class="form-input" 
              placeholder="Ingrese el precio del evento" 
            />
          </div>
        </div>

        <!-- Botón para enviar el formulario -->
        <div class="md:col-span-2">
          <div class="flex justify-center mt-4">
            <button type="submit" class="vintage-button enhanced-btn rounded-lg">
              <i class="fas fa-calendar-plus mr-2"></i> 
              Crear Evento
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
// Componente para crear un nuevo evento desde el panel de administración
export default {
  data() {
    return {
      // Estado reactivo del formulario
      form: {
        title: "",         // Título del evento
        description: "",   // Descripción del evento
        date: "",          // Fecha y hora del evento
        city: "",          // Ciudad donde se realiza el evento
        address: "",       // Dirección del evento
        capacity: null,    // Capacidad máxima de asistentes
        visibility: "Público", // Visibilidad por defecto
        price: null        // Precio del evento
      },
    };
  },
  methods: {
    // Método para enviar el formulario y crear el evento
    submitForm() {
      // Validación de campos obligatorios
      if (!this.form.title || !this.form.date || !this.form.city) {
        alert("Por favor, complete todos los campos obligatorios.");
        return;
      }

      // Construcción del payload para la API
      const payload = {
        titulo: this.form.title,
        descripcion: this.form.description,
        fecha_evento: this.form.date,
        ciudad: this.form.city,
        calle: this.form.address,
        capacidad_maxima: this.form.capacity,
        visibilidad: this.form.visibility,
        price: this.form.price
      };

      // Llamada a la API para crear el evento
      fetch('http://localhost:8080/Apis/API_crear_evento.php', { // URL de la API
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Error en la creación del evento');
        }
        return response.json();
      })
      .then(data => {
        // Emitir evento al padre con los datos del nuevo evento
        this.$emit("event-created", { ...payload, id: data.evento_id });
        alert("¡Evento creado exitosamente!");
        window.location.reload(); // Refrescar la página tras crear el evento
      })
      .catch(error => {
        alert(error.message);
      });
    },
  },
};
</script>

<style scoped>
/* Estilos para el botón vintage principal */
.vintage-button {
  background-color: #825336;
  color: white;
  transition: all 0.3s cubic-bezier(.4,2,.3,1);
  border-radius: 1rem;
  box-shadow: 0 4px 16px rgba(130, 83, 54, 0.13);
  font-size: 1.15rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
}
.vintage-button:hover {
  background-color: #431605;
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 8px 24px rgba(67, 22, 5, 0.18);
}
.vintage-button:active {
  background-color: #6b3e22;
  transform: scale(0.97);
  box-shadow: 0 2px 8px rgba(130, 83, 54, 0.18);
}
/* Estética mejorada para el botón de crear */
.enhanced-btn {
  width: 95%;
  min-height: 3.5rem;
  font-size: 1.15rem;
  font-weight: 500;
  margin: 0.5rem 0;
  box-shadow: none;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.enhanced-btn:active {
  box-shadow: none;
  transform: scale(0.98);
}
</style>