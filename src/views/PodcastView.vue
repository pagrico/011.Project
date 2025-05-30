<template>
    <div class="font-sans text-warm-black min-h-screen py-12 px-4 sm:px-6 lg:px-8 vintage-bg mt-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="font-serif text-4xl md:text-5xl font-semibold text-center mb-2 text-medium-brown mb-6">Emprende Podcast</h1>
            <p class="text-center text-lg text-[#8C7B6A] mb-12 max-w-3xl mx-auto">
                Historias inspiradoras de emprendimiento, innovación y crecimiento personal.
            </p>
            <div v-if="cargando" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <div v-for="n in 6" :key="n" class="video-card bg-white rounded-xl overflow-hidden shadow animate-pulse">
                    <div class="thumbnail-container aspect-video bg-soft-blue"></div>
                    <div class="p-6">
                        <div class="h-6 bg-soft-blue rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-soft-blue rounded w-1/2 mb-4"></div>
                        <div class="h-4 bg-soft-blue rounded w-full mb-2"></div>
                        <div class="h-4 bg-soft-blue rounded w-5/6"></div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    <VideoComponent
                        v-for="video in videosPaginados"
                        :key="video.idVideo"
                        :description="video.descripcion"
                        :duration="video.duracion"
                        :videoSrc="`https://www.youtube.com/watch?v=${video.idVideo}`"
                        class="video-card bg-white rounded-xl overflow-hidden hover:shadow-lg"
                    />
                </div>
                <div class="flex justify-center items-center space-x-2">
                    <button
                        @click="paginaAnterior"
                        :disabled="paginaActual === 1"
                        class="pagination-btn w-10 h-10 rounded-full flex items-center justify-center border border-soft-blue text-medium-brown hover:border-terracotta"
                    >
                        <i class="fas fa-chevron-left text-xs"></i>
                    </button>
                    <button
                        v-for="pagina in numerosPaginas"
                        :key="pagina"
                        @click="irAPagina(pagina)"
                        :class="[
                            'pagination-btn w-10 h-10 rounded-full flex items-center justify-center border border-soft-blue font-medium text-medium-brown',
                            { 'active': pagina === paginaActual }
                        ]"
                    >
                        {{ pagina }}
                    </button>
                    <button
                        @click="paginaSiguiente"
                        :disabled="paginaActual === totalPaginas"
                        class="pagination-btn w-10 h-10 rounded-full flex items-center justify-center border border-soft-blue text-medium-brown hover:border-terracotta"
                    >
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
                <p v-if="videos.length === 0" class="text-center text-warm-beige mt-4">No se encontraron videos para este canal.</p>
                <p v-if="mensajeError" class="text-center text-red-500 mt-4">{{ mensajeError }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import VideoComponent from '../components/Podcast/Video.vue';

const videos = ref([]);
const mensajeError = ref('');
const cargando = ref(true); // Nuevo estado para controlar el skeleton loader
const idCanal = 'UC9D4KMFefAZj1ra3h-BX2fg'; // ID del canal proporcionado
const paginaActual = ref(1);
const videosPorPagina = 12; // Cambiado de 9 a 12

// Función para convertir la duración ISO 8601 a segundos
const convertirDuracion = (duracion) => {
    const match = duracion.match(/PT(\d+H)?(\d+M)?(\d+S)?/);
    const horas = parseInt(match[1]) || 0;
    const minutos = parseInt(match[2]) || 0;
    const segundos = parseInt(match[3]) || 0;
    return horas * 3600 + minutos * 60 + segundos;
};

// Función para obtener todos los videos del canal de YouTube
const obtenerVideos = async (idCanal) => {
    const claveApi = 'AIzaSyAf-ou_chtCIqypaE3fBk5LFyh3twFUnHo'; // Reemplaza con tu clave de API de YouTube
    let tokenPagina = '';
    const todosLosVideos = [];

    try {
        do {
            const urlBusqueda = `https://www.googleapis.com/youtube/v3/search?key=${claveApi}&channelId=${idCanal}&part=snippet&type=video&order=date&maxResults=50&pageToken=${tokenPagina}`;
            const respuestaBusqueda = await fetch(urlBusqueda);
            if (!respuestaBusqueda.ok) {
                throw new Error(`Error en la solicitud: ${respuestaBusqueda.statusText}`);
            }
            const datosBusqueda = await respuestaBusqueda.json();
            tokenPagina = datosBusqueda.nextPageToken || null;

            const idsVideos = datosBusqueda.items.map(item => item.id.videoId).join(',');
            const urlVideos = `https://www.googleapis.com/youtube/v3/videos?key=${claveApi}&id=${idsVideos}&part=contentDetails,snippet`;
            const respuestaVideos = await fetch(urlVideos);
            if (!respuestaVideos.ok) {
                throw new Error(`Error en la solicitud: ${respuestaVideos.statusText}`);
            }
            const datosVideos = await respuestaVideos.json();
            const videosFiltrados = datosVideos.items
                .filter(item => convertirDuracion(item.contentDetails.duration) > 60) // Filtra videos con duración > 60 segundos
                .map(item => ({
                    titulo: item.snippet.title,
                    descripcion: item.snippet.description,
                    idVideo: item.id,
                    duracion: item.contentDetails.duration, // Duración en formato ISO 8601
                }));
            todosLosVideos.push(...videosFiltrados);
        } while (tokenPagina);

        videos.value = todosLosVideos;
    } catch (error) {
        console.error('Error al obtener los videos de YouTube:', error);
        mensajeError.value = 'Hubo un problema al cargar los videos. Por favor, inténtalo de nuevo más tarde.';
    }
};

// Computed para obtener los videos de la página actual
const videosPaginados = computed(() => {
    const inicio = (paginaActual.value - 1) * videosPorPagina;
    const fin = inicio + videosPorPagina;
    return videos.value.slice(inicio, fin);
});

// Computed para calcular el total de páginas
const totalPaginas = computed(() => Math.ceil(videos.value.length / videosPorPagina));

// Computed para generar los números de las páginas
const numerosPaginas = computed(() => {
    const paginas = [];
    for (let i = 1; i <= totalPaginas.value; i++) {
        paginas.push(i);
    }
    return paginas;
});

// Funciones para cambiar de página
const paginaSiguiente = () => {
    if (paginaActual.value < totalPaginas.value) {
        paginaActual.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Desplaza la vista al principio
    }
};

const paginaAnterior = () => {
    if (paginaActual.value > 1) {
        paginaActual.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Desplaza la vista al principio
    }
};

const irAPagina = (pagina) => {
    paginaActual.value = pagina;
    window.scrollTo({ top: 0, behavior: 'smooth' }); // Desplaza la vista al principio
};

onMounted(async () => {
    cargando.value = true; // Activa el skeleton loader
    await obtenerVideos(idCanal);
    cargando.value = false; // Desactiva el skeleton loader
});
</script>

<style scoped>
/* Fondo vintage y fuentes */
.vintage-bg {
    background-color: #DBE6ED;
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2z' fill='%23b7cdda' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
}
.font-serif {
    font-family: 'Playfair Display', serif;
}
.font-sans {
    font-family: 'Work Sans', sans-serif;
}

/* Video Card */
.video-card {
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}
.video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
}

/* Thumbnail */
.thumbnail-container {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}
.thumbnail-container::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0) 60%, rgba(0,0,0,0.1) 100%);
    z-index: 1;
}

/* Duration badge */
.duration-badge {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background-color: rgba(31, 30, 30, 0.85);
    backdrop-filter: blur(4px);
    z-index: 2;
}

/* Pagination */
.pagination-btn {
    transition: all 0.2s ease;
}
.pagination-btn:hover {
    background-color: #C18F67;
    color: white;
}
.pagination-btn.active {
    background-color: #825336;
    color: white;
}

/* Read more link */
.read-more {
    position: relative;
}
.read-more::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 1px;
    background-color: #825336;
    transition: width 0.3s ease;
}
.read-more:hover::after {
    width: 100%;
}

/* Colores personalizados */
.text-warm-beige { color: #BCAEA1; }
.text-medium-brown { color: #825336; }
.text-dark-chocolate { color: #431605; }
.bg-soft-blue { background-color: #B7CDDA; }
.bg-vintage-blue { background-color: #DBE6ED; }
.bg-terracotta { background-color: #C18F67; }
.text-warm-black { color: #1F1E1E; }

/* Skeleton loader */
.animate-pulse {
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>