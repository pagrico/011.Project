import { createStore } from "vuex";
import usuario from "./modules/usuario";
import feedbackSessionStore from "./modules/feedbackSessionStore"; // Importa el nuevo módulo

export default createStore({
  modules: {
    usuario,
    
  },
});
