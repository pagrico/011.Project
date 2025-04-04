<script>
import axios from "axios";
import TAlert from "@/components/alerts/TAlert.vue";

export default {
  props: {
    isOpen: Boolean,
  },
  data() {
    return {
      form: {
        nombre: "",
        apellidos: "",
        email: "",
        telefono: "",
        login: "", // AÑADIDO
        password: "",
        confirmPassword: "",
      },
      errorMessage: "",
      successMessage: "",
    };
  },
  components: {
    TAlert,
  },
  methods: {
    closeModal() {
      this.$emit("close");
    },
    switchToLogin() {
      this.$emit("switchToLogin");
    },
    async submitForm() {
      this.errorMessage = "";
      this.successMessage = "";

      if (
        !this.form.nombre ||
        !this.form.apellidos ||
        !this.form.email ||
        !this.form.telefono ||
        !this.form.login || // AÑADIDO para comprobar el usuario
        !this.form.password ||
        !this.form.confirmPassword
      ) {
        this.errorMessage = "Todos los campos son obligatorios.";
        return;
      }

      if (this.form.password !== this.form.confirmPassword) {
        this.errorMessage = "Las contraseñas no coinciden.";
        return;
      }

      try {
        const formData = new FormData();
        for (const key in this.form) {
          formData.append(key, this.form[key]);
        }

        const response = await axios.post(
          "http://localhost:8080/Apis/registro.php",
          formData
        );
        console.log("Server response:", response.data);

        let data = response.data;
        if (typeof data === "string" && data.includes("Intentando conectar")) {
          const jsonIndex = data.indexOf("{");
          if (jsonIndex !== -1) {
            data = JSON.parse(data.substring(jsonIndex));
          }
        }
        if (data.success === true) {
          this.successMessage = data.message;
          this.errorMessage = "";
          // Limpiamos posibles errores previos
          this.errorMessage = "";

          // Reseteamos formulario
          this.form = {
            nombre: "",
            apellidos: "",
            email: "",
            login: "",
            password: "",
            confirmPassword: "",
          };
        } else {
          // Manejo de respuesta sin éxito
          this.successMessage = "";
          this.errorMessage = data.message;
        }
      } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = "Error al conectar con el servidor.";
        }
      }
    },
  },
};
</script>

<template>
  <div
    v-if="isOpen"
    class="fixed top-0 left-0 w-full h-full bg-opacity-50 backdrop-blur-md z-50 flex justify-center items-center p-4 overflow-y-auto"
  >
    <div class="relative w-full max-w-2xl">
      <div class="relative bg-[#A6BCCB] rounded-lg shadow-sm">
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-[#825336]">
          <h3 class="text-xl font-semibold text-[#431605]">Registro</h3>
          <button
            @click="closeModal"
            class="text-[#825336] bg-transparent hover:bg-[#C18F67] hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
          >
            <svg
              class="w-3 h-3"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 14 14"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
              />
            </svg>
            <span class="sr-only">Cerrar modal</span>
          </button>
        </div>
        <div class="p-4 md:p-5">
          <!-- Alertas con TAlert -->
          <TAlert
            v-if="errorMessage && errorMessage.length > 0"
            variant="danger"
            @dismiss="errorMessage = ''"
          >
            {{ errorMessage }}
          </TAlert>
          <TAlert
            v-if="successMessage && successMessage.length > 0"
            variant="success"
            @dismiss="successMessage = ''"
          >
            {{ successMessage }}
          </TAlert>

          <form @submit.prevent="submitForm" class="grid gap-4 sm:grid-cols-4">
            <!-- Primera línea: Nombre y Apellidos -->
            <div class="sm:col-span-2">
              <label
                for="first-name"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Nombre</label
              >
              <input
                v-model="form.nombre"
                type="text"
                id="first-name"
                placeholder="Juan"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>
            <div class="sm:col-span-2">
              <label
                for="last-name"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Apellidos</label
              >
              <input
                v-model="form.apellidos"
                type="text"
                id="last-name"
                placeholder="Pérez"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>

            <!-- Segunda línea: Correo y Teléfono -->
            <div class="sm:col-span-3">
              <label
                for="email"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Correo electrónico</label
              >
              <input
                v-model="form.email"
                type="email"
                id="email"
                placeholder="nombre@empresa.com"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>
            <div class="sm:col-span-1">
              <label
                for="phone"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Número de teléfono</label
              >
              <input
                v-model="form.telefono"
                type="tel"
                id="phone"
                placeholder="612345678"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>

            <!-- Tercera línea: Nombre de usuario -->
            <div class="sm:col-span-2">
              <label
                for="login"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Nombre de usuario</label
              >
              <input
                v-model="form.login"
                type="text"
                id="login"
                placeholder="usuario123"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>
            <div class="sm:col-span-2"></div>

            <!-- Cuarta línea: Contraseña y Confirmar contraseña -->
            <div class="sm:col-span-2">
              <label
                for="password"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Contraseña</label
              >
              <input
                v-model="form.password"
                type="password"
                id="password"
                placeholder="********"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>
            <div class="sm:col-span-2">
              <label
                for="confirm-password"
                class="block mb-2 text-sm font-medium text-[#431605]"
                >Confirmar contraseña</label
              >
              <input
                v-model="form.confirmPassword"
                type="password"
                id="confirm-password"
                placeholder="********"
                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5"
              />
            </div>

            <!-- Botón de envío -->
            <div class="sm:col-span-4">
              <button
                type="submit"
                class="w-full text-white bg-[#825336] hover:bg-[#C18F67] focus:ring-4 focus:outline-none focus:ring-[#431605] font-medium rounded-lg text-sm px-5 py-2.5 text-center"
              >
                Crear cuenta
              </button>
            </div>
            <div class="text-sm font-medium text-[#1F1E1E] sm:col-span-4">
              ¿Ya tienes cuenta?
              <a
                href="#"
                @click.prevent="switchToLogin"
                class="text-[#825336] hover:underline"
                >Inicia sesión</a
              >
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estilos opcionales */
</style>