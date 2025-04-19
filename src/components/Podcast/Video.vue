<template>
    <div 
        class="video-component bg-white rounded-lg shadow-lg p-4 w-full max-w-full mx-auto"
        @mouseenter="isHovered = true" 
        @mouseleave="isHovered = false"
    >
        <div>
            <div class="relative">
                <a :href="youtubeUrl" target="_blank">
                    <img 
                        :src="thumbnailUrl" 
                        alt="Video Thumbnail" 
                        class="w-full h-auto rounded-t-lg object-cover">
                </a>
            </div>
            <div class="mt-4">
                <!-- Texto introductorio -->
                <div class="flex flex-col text-left">
                    <a :href="youtubeUrl" target="_blank" class="text-base font-bold text-[#5A2D0C]">
                        {{ videoData.title }}
                    </a>
                    <!-- Duración debajo del título -->
                    <span class="text-sm text-[#825336] mt-1">{{ videoData.duration }}</span>
                </div>
            </div>
            <!-- Descripción con truncado y enlace de "Leer más" en una línea separada -->
            <p class="text-sm text-[#1F1E1E] text-left mt-4">
                {{ truncatedDescription }}
            </p>
            <p v-if="showReadMore" class="text-sm text-[#C18F67] text-left cursor-pointer mt-1">
                <a :href="youtubeUrl" target="_blank">Leer más</a>
            </p>
            <div class="mt-4 flex justify-end space-x-4">
                <button @click="toggleFavorite" class="cursor-pointer">
                    <span 
                        class="material-symbols-outlined text-red-500" 
                        :style="{'font-variation-settings': isFavorite ? '\'FILL\' 1, \'wght\' 400, \'GRAD\' 0, \'opsz\' 24' : '\'FILL\' 0, \'wght\' 400, \'GRAD\' 0, \'opsz\' 24'}"
                    >
                        favorite
                    </span>
                </button>
                <button @click="toggleBookmark" class="cursor-pointer">
                    <span 
                        class="material-symbols-outlined text-yellow-500" 
                        :style="{'font-variation-settings': isBookmarked ? '\'FILL\' 1, \'wght\' 400, \'GRAD\' 0, \'opsz\' 24' : '\'FILL\' 0, \'wght\' 400, \'GRAD\' 0, \'opsz\' 24'}"
                    >
                        bookmark
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'VideoComponent',
    props: {
        videoSrc: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            videoData: {
                title: '',
                description: '',
                duration: ''
            },
            isHovered: false, // Controla si el mouse está sobre el componente
            isAdmin: false, // Determina si el usuario es administrador
            isDescriptionExpanded: false, // Controla si la descripción está expandida
            isFavorite: false, // Estado para el botón de favorito
            isBookmarked: false // Estado para el botón de marcador
        };
    },
    computed: {
        youtubeUrl() {
            return this.videoSrc;
        },
        thumbnailUrl() {
            const videoId = this.extractVideoId(this.videoSrc);
            return `https://img.youtube.com/vi/${videoId}/0.jpg`;
        },
        truncatedDescription() {
            if (this.isDescriptionExpanded || this.videoData.description.length <= 200) {
                return this.videoData.description;
            }
            return this.videoData.description.slice(0, 200).trim() + '...';
        },
        showReadMore() {
            return !this.isDescriptionExpanded && this.videoData.description.length > 200;
        }
    },
    methods: {
        extractVideoId(url) {
            const regex = /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&]+)|youtu\.be\/([^?&]+)/;
            const match = url.match(regex);
            return match ? (match[1] || match[2]) : '';
        },
        async fetchVideoData() {
            const videoId = this.extractVideoId(this.videoSrc);
            if (!videoId) {
                console.error('Error: No se pudo extraer el ID del video.');
                return;
            }

            const apiKey = 'AIzaSyAf-ou_chtCIqypaE3fBk5LFyh3twFUnHo'; // Reemplaza con tu clave de API de YouTube
            const apiUrl = `https://www.googleapis.com/youtube/v3/videos?id=${videoId}&part=snippet,contentDetails&key=${apiKey}`;
            
            try {
                const response = await axios.get(apiUrl);
                if (response.data.items && response.data.items.length > 0) {
                    const video = response.data.items[0];
                    this.videoData.title = video.snippet.title;
                    this.videoData.description = video.snippet.description;
                    this.videoData.duration = this.formatDuration(video.contentDetails.duration);
                } else {
                    console.error('Error: No se encontraron datos para el video.');
                }
            } catch (error) {
                console.error('Error fetching video data:', error.message);
                console.error('Detalles del error:', error.response?.data || error);
            }
        },
        formatDuration(duration) {
            return duration
                .replace('PT', '')
                .replace(/H/, 'h ')
                .replace(/M.*/, 'm') // Elimina todo después de los minutos
                .trim();
        },
        expandDescription() {
            this.isDescriptionExpanded = true;
        },
        async fetchUsuario() {
            try {
                // Simulación de una llamada para obtener los datos del usuario
                const usuario = await axios.get('/api/usuario'); // Reemplaza con la URL real de tu API
                this.isAdmin = usuario.data.rol === 'administrador';
                console.log('Rol del usuario:', usuario.data.rol); // Muestra el rol del usuario en la consola
            } catch (error) {
                console.error('Error al obtener los datos del usuario:', error.message);
            }
        },
        toggleFavorite() {
            this.isFavorite = !this.isFavorite;
        },
        toggleBookmark() {
            this.isBookmarked = !this.isBookmarked;
        }
    },
    mounted() {
        this.fetchVideoData();
        this.fetchUsuario(); // Llama a la función para obtener los datos del usuario
    }
};
</script>

<style scoped>
.material-symbols-outlined {
    font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24;
    display: inline-block;
    vertical-align: middle;
}

.video-component {
    max-width: 100%; /* Asegura que el componente ocupe todo el ancho disponible */
}

img {
    width: 100%; /* Imagen ocupa todo el ancho del contenedor */
    height: auto; /* Mantiene la proporción de la imagen */
    object-fit: cover; /* Asegura que la imagen se ajuste correctamente */
}

@media (min-width: 2560px) {
    .video-component {
        max-width: 75%; /* Limita el ancho en pantallas 2K o más grandes */
    }
}
</style>

