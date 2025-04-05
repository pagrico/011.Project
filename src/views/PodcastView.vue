<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <VideoComponent 
            title="Ejemplo de Video" 
            description="Este es un video de ejemplo de YouTube." 
            duration="3:45" 
            videoSrc="https://www.youtube.com/watch?v=r6faxuqE6jc" 
        />
        <VideoComponent 
            title="Ejemplo de Video" 
            description="Este es un video de ejemplo de YouTube." 
            duration="3:45" 
            videoSrc="https://www.youtube.com/watch?v=r6faxuqE6jc" 
        />
        <VideoComponent 
            title="Ejemplo de Video" 
            description="Este es un video de ejemplo de YouTube." 
            duration="3:45" 
            videoSrc="https://www.youtube.com/watch?v=r6faxuqE6jc" 
        />
    </div>
</template>

<script setup>
import VideoComponent from '../components/Podcast/Video.vue';
</script>

<style scoped>
/* Se asegura un espaciado uniforme entre las tarjetas */
.grid {
    margin: 1rem;
}
</style>