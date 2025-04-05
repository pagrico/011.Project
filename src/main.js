import { createApp } from "vue";
import App from "./App.vue";
import router from "./router"; // Si usas Vue Router
import store from "./store"; // Si usas Vuex
import "@/assets/main.css"; // Importar Tailwind CSS

createApp(App)
    .use(router) // Asegúrate de que el router esté configurado
    .use(store) // Asegúrate de que Vuex esté configurado
    .mount("#app");
