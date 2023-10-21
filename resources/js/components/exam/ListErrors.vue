<template>
    <div>
        <div v-if="errors && campo">
            <ul v-for="(error, i) in campo" v-bind:key="i" class="text-danger">
                <li>{{ error }}</li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    props: ['errors', 'campo']
}
</script>

<style>

</style>