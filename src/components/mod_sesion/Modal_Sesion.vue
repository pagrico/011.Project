<script>
import TAlert from "../alerts/TAlert.vue";

export default {
    components: {
        TAlert,
    },
    props: {
        isOpen: Boolean, // Estado recibido del componente padre
    },
    data() {
        return {
            identifier: "", // Correo o login
            password: "",
            rememberMe: false, // Estado del checkbox "Recuérdame"
            errorMessage: "",
        };
    },
    methods: {
        closeModal() {
            this.$emit("close"); // Emite un evento para cerrar el modal
        },
        switchToRegister() {
            this.$emit("switchToRegister"); // Emite un evento para cambiar al modal de registro
        },
        async login() {
            try {
                const response = await fetch("http://localhost:8080/Apis/API_InicioSesion_Usuario.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        identifier: this.identifier,
                        password: this.password,
                    }),
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                // Registrar el texto de la respuesta para depuración
                const responseText = await response.text();
                console.log("Respuesta del servidor:", responseText);

                let data;
                try {
                    data = JSON.parse(responseText); // Intentar analizar como JSON
                } catch (jsonError) {
                    throw new Error("Respuesta JSON no válida");
                }

                if (data.success) {
                    localStorage.setItem("userData", JSON.stringify({
                        name: data.nombre,
                        apellidos: data.apellidos
                    }));

                    // Guardar en localStorage si "Recuérdame" está activado
                    if (this.rememberMe) {
                        localStorage.setItem("userSession", JSON.stringify({
                            identifier: this.identifier,
                            password: this.password,
                        }));
                    } else {
                        localStorage.removeItem("userSession");
                    }

                    this.closeModal();
                } else {
                    this.setErrorMessage(data.error || "Error al iniciar sesión");
                }
            } catch (error) {
                console.error("Error en login:", error); // Mostrar error en la consola
                this.setErrorMessage(error.message || "Error de conexión con el servidor");
            }
        },
        setErrorMessage(message) {
            this.errorMessage = message;
            setTimeout(() => {
                this.errorMessage = "";
            }, 3000); // El mensaje desaparece después de 3 segundos
        },
        loadSession() {
            const savedSession = localStorage.getItem("userSession");
            if (savedSession) {
                const { identifier, password } = JSON.parse(savedSession);
                this.identifier = identifier;
                this.password = password;
                this.rememberMe = true;
            }
        },
    },
    mounted() {
        this.loadSession(); // Cargar sesión guardada al montar el componente
    },
};
</script>

<template>
    <div v-if="isOpen"
        class="fixed top-0 left-0 w-full h-full bg-opacity-50 backdrop-blur-md z-50 flex justify-center items-center">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-[#A6BCCB] rounded-lg shadow-sm">
                <!-- Cabecera del modal -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-[#825336]">
                    <h3 class="text-xl font-semibold text-[#431605]">Iniciar sesión</h3>
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
                <!-- Cuerpo del modal -->
                <div class="p-4 md:p-5">
                    <form @submit.prevent="login" class="space-y-4">
                        <!-- Correo o login -->
                        <div>
                            <label for="identifier" class="block mb-2 text-sm font-medium text-[#431605]">Email o Nombre de usuario</label>
                            <input v-model="identifier" type="text" id="identifier" placeholder="name@company.com o usuario123" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <!-- Contraseña -->
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-[#431605]">Tu contraseña</label>
                            <input v-model="password" type="password" id="password" placeholder="••••••••" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <!-- Mensaje de error -->
                        <TAlert v-if="errorMessage" variant="danger" dismissible @dismiss="errorMessage = ''">
                            {{ errorMessage }}
                        </TAlert>
                        <!-- Botón de inicio de sesión -->
                        <button type="submit"
                            class="w-full text-white bg-[#825336] hover:bg-[#C18F67] focus:ring-4 focus:outline-none focus:ring-[#431605] font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Iniciar sesión
                        </button>
                        <!-- Recuérdame -->
                        <div class="flex justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input v-model="rememberMe" id="remember" type="checkbox"
                                        class="w-4 h-4 border border-[#825336] rounded-sm bg-[#B7CDDA] focus:ring-3 focus:ring-[#C18F67]" />
                                </div>
                                <label for="remember"
                                    class="ms-2 text-sm font-medium text-[#431605]">Recuérdame</label>
                            </div>
                            <a href="#" class="text-sm text-[#825336] hover:underline">¿Olvidaste tu contraseña?</a>
                        </div>
                        <!-- Cambiar a registro -->
                        <div class="text-sm font-medium text-[#1F1E1E]">
                            ¿No tienes cuenta?
                            <a href="#" @click.prevent="switchToRegister"
                                class="text-[#825336] hover:underline">Regístrate</a>
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
