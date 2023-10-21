<template>
    <div>
        <div v-if="exams.length > 0">
            <b-list-group>
                <b-list-group-item v-for="(exam, i) in exams" v-bind:key="i">
                    <b-row>
                        <b-col>
                            <h5>{{ exam.name }}</h5>
                            <h6>
                                <strong>Fecha de aplicación: </strong>
                                {{ exam.start_date }}
                            </h6>
                            <h6>
                                <strong>Hora de aplicación: </strong>
                                {{ exam.start_time }} - {{ exam.end_time }}
                            </h6>
                            <h6><strong>Duración: </strong> {{ exam.duration }} minutos</h6>
                        </b-col>
                        <b-col sm="4">
                            <b-button id="btn-actions" pill
                                :href="`/student/exam/${exam.id}`">
                                <b-icon-arrow-right></b-icon-arrow-right> Ingresar
                            </b-button>
                        </b-col>
                    </b-row>
                </b-list-group-item>
            </b-list-group>
        </div>
        <b-alert v-else variant="info" show class="text-center">
            Espera a que tu profesor asigne un examen a tu grupo.
        </b-alert>
    </div>
</template>

<script>
export default {
    props: ['exams']
}
</script>