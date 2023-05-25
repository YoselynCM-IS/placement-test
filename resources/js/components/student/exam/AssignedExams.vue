<template>
    <div>
        <div v-if="exams.length > 0">
            <b-list-group v-if="!openExam">
                <b-list-group-item v-for="(exam, i) in exams" v-bind:key="i">
                    <b-row>
                        <b-col>
                            <h5>{{ exam.name }}</h5>
                            <div v-if="exam.advances_count == 0">
                                <h6>
                                    <strong>Fecha de aplicación: </strong>
                                    {{ exam.start_date }}
                                </h6>
                                <h6>
                                    <strong>Hora de aplicación: </strong>
                                    {{ exam.start_time }} - {{ exam.end_time }}
                                </h6>
                                <h6><strong>Duración: </strong> {{ exam.duration }} minutos</h6>
                            </div>
                        </b-col>
                        <b-col>
                            <div v-if="exam.advances_count == 0">
                                <b-button id="btn-actions" 
                                    pill @click="start_exam(exam)">
                                    <b-icon-arrow-right></b-icon-arrow-right> Ingresar
                                </b-button>
                            </div>
                            <b-button v-else id="btn-actions" pill
                                :href="`/student/results/${exam.id}`">
                                <b-icon-exclamation-circle></b-icon-exclamation-circle> Ver resultado
                            </b-button>
                        </b-col>
                    </b-row>
                </b-list-group-item>
            </b-list-group>
            <div v-if="openExam">
                <solve-exam v-if="!load" :exam="exam"></solve-exam>
                <div v-else class="text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <b-spinner style="width: 3rem; height: 3rem;"></b-spinner>
                    </div>
                    <h4><b>Cargando examen...</b></h4>
                </div>
            </div>
        </div>
        <b-alert v-else variant="info" show class="text-center">
            Espera a que tu profesor asigne un examen a tu grupo.
        </b-alert>
    </div>
</template>

<script>
export default {
    props: ['registers'],
    data() {
        return {
            exams: this.registers,
            exam: {},
            openExam: false,
            load: false,
            message: null
        }
    },
    methods: {
        // INGRESAR EL EXAMEN
        start_exam(exam){
            this.openExam = true;
            this.load = true;
            axios.get('/exams/enter_exam', {params: {exam_id: exam.id}}).then(response => {
                if(response.data == 0){
                    this.exam = exam;
                } else {
                    this.openExam = false;
                    swal("Examen iniciado", "El examen ya ha sido iniciado anteriormente.", "warning");
                }
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
    }
}
</script>

<style>

</style>