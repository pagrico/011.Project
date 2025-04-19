export default {
  namespaced: true, // Agregado para habilitar el namespacing
  state: {
    usuario: null,
    feedback_sessions: {}, // Agregado para manejar sesiones de feedback
    // ...existing state...
  },
  mutations: {
    setUsuario(state, payload) {
      state.usuario = payload;
    },
    setFeedbackSession(state, { id, data }) {
      state.feedback_sessions[id] = data; // Actualiza o agrega una sesión de feedback
    },
    // ...existing mutations...
  },
  actions: {
    setUsuario({ commit }, payload) {
      commit("setUsuario", payload);
    },
    async createFeedbackSession({ commit }, { id, data }) {
      try {
        // Simula una operación asincrónica, como una llamada a una API
        console.log("Creando sesión de feedback:", id, data);
        // Aquí podrías realizar una llamada a una API si es necesario
        commit("setFeedbackSession", { id, data }); // Actualiza el estado con los datos
      } catch (error) {
        console.error("Error al crear la sesión de feedback:", error);
      }
    },
    // ...existing actions...
  },
  getters: {
    usuario: (state) => state.usuario,
    getFeedbackSession: (state) => (id) => {
      return state.feedback_sessions[id]; // Retorna una sesión específica por ID
    },
    // ...existing getters...
  },
};
