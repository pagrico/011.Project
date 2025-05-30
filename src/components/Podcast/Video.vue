<template>
    <div class="video-card bg-white rounded-xl overflow-hidden hover:shadow-lg transition-all">
        <div class="thumbnail-container aspect-video">
            <a :href="youtubeUrl" target="_blank" rel="noopener">
                <img
                    :src="thumbnailUrl"
                    alt="Miniatura del video"
                    class="w-full h-full object-cover object-center" />
                <div class="duration-badge px-3 py-1 rounded-full text-white text-sm flex items-center">
                    <i class="far fa-clock mr-1 text-xs"></i>
                    <span>{{ formattedDuration }}</span>
                </div>
            </a>
        </div>
        <div class="p-6">
            <h3 class="font-serif text-xl font-semibold mb-2 text-dark-chocolate line-clamp-2">
                <a :href="youtubeUrl" target="_blank" rel="noopener" class="hover:underline">{{ title }}</a>
            </h3>
            <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ truncatedDescription }}</p>
            <a v-if="showReadMore" :href="youtubeUrl" target="_blank" rel="noopener" class="read-more text-terracotta text-sm font-medium inline-flex items-center">
                Ver más
                <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'VideoComponent',
    props: {
        videoSrc: { type: String, required: true },
        description: { type: String, required: true },
        duration: { type: String, required: true }
    },
    computed: {
        youtubeUrl() {
            return this.videoSrc;
        },
        thumbnailUrl() {
            const videoId = this.extractVideoId(this.videoSrc);
            return `https://img.youtube.com/vi/${videoId}/0.jpg`;
        },
        title() {
            return this.$attrs.title || this.description.split('\n')[0].slice(0, 80);
        },
        truncatedDescription() {
            if (this.description.length <= 180) return this.description;
            return this.description.slice(0, 180).trim() + '...';
        },
        showReadMore() {
            return this.description.length > 180;
        },
        formattedDuration() {
            const match = this.duration.match(/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/);
            const h = parseInt(match[1]) || 0;
            const m = parseInt(match[2]) || 0;
            const s = parseInt(match[3]) || 0;
            if (h > 0) {
                return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            }
            return `${m}:${s.toString().padStart(2, '0')}`;
        }
    },
    methods: {
        extractVideoId(url) {
            const regex = /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&]+)|youtu\.be\/([^?&]+)/;
            const match = url.match(regex);
            return match ? (match[1] || match[2]) : '';
        }
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

.video-card {
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}
.video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
}
.thumbnail-container {
    position: relative;
    overflow: hidden;
    /* Solo bordes redondeados arriba */
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #B7CDDA;
}
.thumbnail-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center center;
    display: block;
}
.thumbnail-container::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0) 60%, rgba(0,0,0,0.1) 100%);
    z-index: 1;
}
.duration-badge {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background-color: rgba(31, 30, 30, 0.85);
    backdrop-filter: blur(4px);
    z-index: 2;
}
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
.text-dark-chocolate { color: #431605; }
.text-terracotta { color: #C18F67; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

