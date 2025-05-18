<template>
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="text-xl">Crear Nuevo Evento</h2>
    </div>
    <div class="admin-card-body">
      <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
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
          <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea 
              v-model="form.description" 
              rows="3" 
              class="form-input" 
              placeholder="Describa su evento"
            ></textarea>
          </div>
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

        <div>
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
          <div class="form-group">
            <label class="form-label">Dirección</label>
            <input 
              v-model="form.address" 
              type="text" 
              class="form-input" 
              placeholder="Ingrese la dirección del lugar" 
            />
          </div>
          <div class="form-group">
            <label class="form-label">Capacidad</label>
            <input 
              v-model.number="form.capacity" 
              type="number" 
              class="form-input" 
              placeholder="Máximo de asistentes" 
            />
          </div>
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
          <!-- Nuevo campo Precio -->
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

        <div class="md:col-span-2">
          <button type="submit" class="vintage-button px-6 py-3 rounded-lg w-full md:w-auto">
            <i class="fas fa-calendar-plus mr-2"></i> 
            Crear Evento
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        title: "",
        description: "",
        date: "",
        city: "",
        address: "",
        capacity: null,
        visibility: "Público",
        price: null // nuevo campo precio
      },
    };
  },
  methods: {
    submitForm() {
      if (!this.form.title || !this.form.date || !this.form.city) {
        alert("Por favor, complete todos los campos obligatorios.");
        return;
      }

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

      fetch('http://localhost:8080/Apis/API_crear_evento.php', { // URL corregido
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
        this.$emit("event-created", { ...payload, id: data.evento_id });
        alert("¡Evento creado exitosamente!");
        window.location.reload(); // refrescar la página después de crear el evento
      })
      .catch(error => {
        alert(error.message);
      });
    },
  },
};
</script>

<style scoped>
/* Add any specific styles for this component if needed */
</style>