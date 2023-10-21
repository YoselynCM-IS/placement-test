<template>
    <div>
        <b-row class="mt-2">
            <b-col sm="3" class="text-right">
                <strong>{{ text }}</strong>
            </b-col>
            <b-col>
                <audio id="audioIdE"></audio>
                <b-button @click="playAudio()" pill size="sm" variant="dark"><b><b-icon-play></b-icon-play> Play</b></b-button>
                <!-- <b-button @click="pauseAudio()" pill size="sm" variant="dark"><b-icon-pause></b-icon-pause></b-button> -->
            </b-col>
        </b-row>
    </div>
</template>

<script>
export default {
    props: ['link', 'text'],
    methods: {
        playAudio() {
            var audio = document.getElementById('audioIdE');
            audio.pause();
            audio.currentTime = 0;
            audio.src = this.link;
            audio.play();
        },
        pauseAudio() {
            var audio = document.getElementById('audioIdE');
            audio.pause();
        }
    }
}
</script>