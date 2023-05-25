<template>
    <div>
        <b-row class="mt-2">
            <b-col sm="3" class="text-right">
                <strong>{{ text }}</strong>
            </b-col>
            <b-col>
                <audio controls>
                    <source :src="link" type="audio/mp3">
                        Tu navegador no soporta audio HTML5.
                </audio>
            </b-col>
        </b-row>
    </div>
</template>

<script>
export default {
    props: ['link', 'text']
}
</script>