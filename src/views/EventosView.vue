<template>
  <div id="app" class="min-h-screen p-6 mt-10">
    <div class="container mx-auto">
      <!-- Admin Section -->
      <AdminPanel v-if="isAdmin" class="mb-6" :events="events" @update-events="updateEvents" />

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Events List -->
        <div class="lg:col-span-3">
          <EventFilter :cities="cities" v-model="selectedCity" />
          <EventList :events="filteredEvents" @add-to-cart="addToCart" />
        </div>

        <!-- Shopping Cart -->
        <ShoppingCart :cart="cart" @remove-item="removeItemFromCart" @checkout="openCheckoutModal" />
      </div>
    </div>

    <!-- Checkout Modal -->
    <CheckoutModal v-if="isCheckoutModalOpen" :cart-total="cartTotal" @close="closeCheckoutModal" />
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import AdminPanel from "../components/Eventos/AdminPanel.vue";
import EventFilter from "../components/Eventos/EventFilter.vue";
import EventList from "../components/Eventos/EventList.vue";
import ShoppingCart from "../components/Eventos/ShoppingCart.vue";
import CheckoutModal from "../components/Eventos/CheckoutModal.vue";

export default {
  components: {
    AdminPanel,
    EventFilter,
    EventList,
    ShoppingCart,
    CheckoutModal,
  },
  setup() {
    // Se reemplaza el array estático por uno vacío
    const events = ref([]);
    
    const selectedCity = ref("");
    const cart = ref([]);
    const isCheckoutModalOpen = ref(false);

    // Obtener eventos desde la API y mapear "precio" a "price" para compatibilidad
    const fetchEvents = async () => {
      try {
        const res = await fetch("http://localhost:8080/Apis/API_Lista_de_eventos.php");
        const data = await res.json();
        events.value = data.map(e => ({
          id: e.id,
          title: e.titulo,         // mapeo de titulo a title
          description: e.descripcion, // mapeo de descripcion a description
          date: e.fecha_evento,       // mapeo de fecha_evento a date
          ciudad: e.ciudad, // se cambia de "city" a "ciudad" para que funcione el filtro
          visibilidad: e.visibilidad,
          price: parseFloat(e.precio) || 0, // aseguramos que sea numérico
          capacity: parseInt(e.capacidad_maxima) || 0, // nuevo mapeo de capacidad
          address: e.address || ""  // se asigna address vacío si no existe
        }));
      } catch (error) {
        console.error("Error al cargar eventos", error);
      }
    };

    onMounted(() => {
      fetchEvents();
    });

    const cities = computed(() =>
      [...new Set(events.value.map((event) => event.ciudad))]
    );

    const filteredEvents = computed(() =>
      selectedCity.value
        ? events.value.filter(
            (event) =>
              event.ciudad === selectedCity.value && event.visibilidad === "Público"
          )
        : events.value.filter((event) => event.visibilidad === "Público")
    );

    const cartTotal = computed(() =>
      cart.value.reduce((total, item) => total + item.event.price * item.quantity, 0)
    );

    const addToCart = (event) => {
      const existingItem = cart.value.find((item) => item.event.id === event.id);
      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.value.push({ event, quantity: 1 });
      }
    };

    const removeItemFromCart = (index) => {
      cart.value.splice(index, 1);
    };

    const openCheckoutModal = () => {
      isCheckoutModalOpen.value = true;
    };

    const closeCheckoutModal = () => {
      isCheckoutModalOpen.value = false;
    };

    // Se mantiene updateEvents por si el AdminPanel necesita actualizar los eventos
    const updateEvents = (newEvents) => {
      events.value = newEvents;
    };

    const isAdmin = computed(() => {
      const userData = JSON.parse(localStorage.getItem("userData") || "{}");
      return userData.role === 1;
    });

    return {
      events,
      selectedCity,
      cities,
      filteredEvents,
      cart,
      cartTotal,
      isCheckoutModalOpen,
      addToCart,
      removeItemFromCart,
      openCheckoutModal,
      closeCheckoutModal,
      updateEvents,
      isAdmin,
    };
  },
};
</script>