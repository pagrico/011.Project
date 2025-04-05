import { createStore } from "vuex";

export default createStore({
  state: {
    user: null, // Estado global para el usuario
  },
  mutations: {
    setUser(state, user) {
      state.user = user; // Actualiza el estado del usuario
    },
  },
  actions: {
    setGlobalUser({ commit }, user) {
      commit("setUser", user); // Llama a la mutación para actualizar el usuario
    },
  },
  getters: {
    getUser(state) {
      return state.user; // Devuelve el estado del usuario
    },
  },
});
