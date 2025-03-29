<script>
export default {
    props: {
        isOpen: Boolean, // Estado recibido del componente padre
    },
    methods: {
        closeModal() {
            this.$emit("close"); // Emite un evento para cerrar el modal
        },
        switchToLogin() {
            this.$emit("switchToLogin"); // Emite un evento para cambiar al modal de sesión
        },
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
                <!-- Cuerpo del modal -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label for="first-name"
                                class="block mb-2 text-sm font-medium text-[#431605]">Nombre</label>
                            <input type="text" id="first-name" placeholder="Juan" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <!-- Apellidos -->
                        <div>
                            <label for="last-name"
                                class="block mb-2 text-sm font-medium text-[#431605]">Apellidos</label>
                            <input type="text" id="last-name" placeholder="Pérez" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <!-- Correo -->
                        <div>
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-[#431605]">Correo
                                electrónico</label>
                            <input type="email" id="email" placeholder="nombre@empresa.com" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <!-- Teléfono -->
                        <div>
                            <label for="phone"
                                class="block mb-2 text-sm font-medium text-[#431605]">Número de
                                teléfono</label>
                            <input type="tel" id="phone" placeholder="+34 612 345 678" required
                                class="bg-[#B7CDDA] border border-[#825336] text-[#1F1E1E] text-sm rounded-lg focus:ring-[#C18F67] focus:border-[#C18F67] block w-full p-2.5" />
                        </div>
                        <div class="flex justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" type="checkbox" value=""
                                        class="w-4 h-4 border border-[#825336] rounded-sm bg-[#B7CDDA] focus:ring-3 focus:ring-[#C18F67]" />
                                </div>
                                <label for="terms"
                                    class="ms-2 text-sm font-medium text-[#431605]">Acepto los
                                    términos y condiciones</label>
                            </div>
                        </div>
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
