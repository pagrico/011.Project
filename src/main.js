import { createApp } from "vue";
import { reactive } from "vue"; // Importar reactive
import App from "./App.vue";
import router from "./router";
import store from "./store";
import "@/assets/main.css";

// Crear una variable global reactiva para el ID del usuario
const globalState = reactive({
    userId: null, // ID del usuario
});

const app = createApp(App);

// Proveer la variable global a toda la aplicación
app.provide("globalState", globalState);

app.use(router).use(store).mount("#app");
