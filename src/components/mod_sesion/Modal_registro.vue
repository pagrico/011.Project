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

            if (!this.form.nombre || !this.form.apellidos || !this.form.email
                || !this.form.telefono || !this.form.password || !this.form.confirmPassword) {
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
                    'http://localhost:8080/Apis/registro.php',
                    formData
                );

                if (response.data.success) {
                    this.successMessage = response.data.message;
                    this.form = { nombre: "", apellidos: "", email: "", telefono: "", password: "", confirmPassword: "" }; // Limpiar formulario
                } else {
                    this.errorMessage = response.data.message;
                }
            } catch (error) {
                this.errorMessage = "Error al conectar con el servidor.";
            }
        },
    },
};
</script>

<template>
    <div v-if="isOpen"
        class="fixed top-0 left-0 w-full h-full bg-opacity-50 backdrop-blur-md z-50 flex justify-center items-center">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-[#A6BCCB] rounded-lg shadow-sm">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-[#825336]">
                    <h3 class="text-xl font-semibold text-[#431605]">Registro</h3>
                    <button @click="closeModal"
                        class="text-[#825336] bg-transparent hover:bg-[#C18F67] hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <!-- Alertas con TAlert -->
                    
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label for="first-name" class="block mb-2 text-sm font-medium text-[#431605]">Nombre</label>
                            <input v-model="form.nombre" type="text" id="first-name" placeholder="Juan" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <div>
                            <label for="last-name" class="block mb-2 text-sm font-medium text-[#431605]">Apellidos</label>
                            <input v-model="form.apellidos" type="text" id="last-name" placeholder="Pérez" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-[#431605]">Correo electrónico</label>
                            <input v-model="form.email" type="email" id="email" placeholder="nombre@empresa.com" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <div>
                            <label for="phone" class="block mb-2 text-sm font-medium text-[#431605]">Número de teléfono</label>
                            <input v-model="form.telefono" type="tel" id="phone" placeholder="+34 612 345 678" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-[#431605]">Contraseña</label>
                            <input v-model="form.password" type="password" id="password" placeholder="********" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <div>
                            <label for="confirm-password" class="block mb-2 text-sm font-medium text-[#431605]">Confirmar contraseña</label>
                            <input v-model="form.confirmPassword" type="password" id="confirm-password" placeholder="********" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <TAlert v-if="errorMessage" variant="danger" @dismiss="errorMessage = ''">
                        {{ errorMessage }}
                    </TAlert>
                    <TAlert v-if="successMessage" variant="success" @dismiss="successMessage = ''">
                        {{ successMessage }}
                    </TAlert>
                        <button type="submit"
                            class="w-full text-white bg-[#825336] hover:bg-[#C18F67] focus:ring-4 focus:outline-none focus:ring-[#431605] font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Crear cuenta
                        </button>
                        <div class="text-sm font-medium text-[#1F1E1E]">
                            ¿Ya tienes cuenta?
                            <a href="#" @click.prevent="switchToLogin"
                                class="text-[#825336] hover:underline">Inicia sesión</a>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Opcional: mejora de estilos */
</style>
