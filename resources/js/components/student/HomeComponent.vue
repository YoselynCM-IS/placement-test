<template>
    <div>
        <b-list-group>
            <b-list-group-item v-for="(exam, i) in exams" v-bind:key="i">
                <b-row>
                    <b-col></b-col>
                </b-row>
            </b-list-group-item>
        </b-list-group>
            
    </div>
</template>

<script>
export default {
    props: ['exams']

}
</script>

<style>

</style>