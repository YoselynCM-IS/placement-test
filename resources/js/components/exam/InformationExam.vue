<template>
    <div>
        <b-list-group>
            <b-list-group-item>
                <label><strong>Nombre del examen:</strong></label>
                <p>{{ exam.name }}</p>
            </b-list-group-item>
            <b-list-group-item>
                <label><strong>Profesor:</strong></label>
                <p>{{ exam.teacher.user.name }}</p>
            </b-list-group-item>
            <b-list-group-item>
                <label><strong>Grupo: </strong></label>
                <p>{{ exam.group ? exam.group.group : 'No definido' }}</p>
            </b-list-group-item>
            <b-list-group-item>
                <label><strong>Fecha de aplicación: </strong></label>
                <p>{{ exam.start_date ? exam.start_date : 'No definido' }}</p>
            </b-list-group-item>
            <b-list-group-item>
                <label><strong>Hora de inicio: </strong></label>
                <p>{{ exam.start_time ? exam.start_time : 'No definido' }}</p>
            </b-list-group-item>
            <b-list-group-item>
                <label><strong>Hora de termino: </strong></label>
                <p>{{ exam.end_time ? exam.end_time : 'No definido' }}</p>
            </b-list-group-item>
            <b-list-group-item>
                <label><strong>Duración: </strong></label>
                <p>{{ exam.duration }} minutos</p>
            </b-list-group-item>
            <b-list-group-item v-if="show">
                <label><strong>Indicaciones: </strong></label>
                <p v-html="exam.indications ? exam.indications : 'No definido'"></p>
            </b-list-group-item>
        </b-list-group>
    </div>
</template>

<script>
export default {
    props: ['exam', 'show']
}
</script>

<style>

</style>