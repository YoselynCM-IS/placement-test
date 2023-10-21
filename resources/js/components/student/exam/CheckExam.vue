<template>
    <div>
        <b-row>
            <b-col sm="4">
                <information-exam :exam="exam" :show="false"></information-exam>
            </b-col>
            <b-col>
                <b-card>
                    <h6><strong>Indicaciones: </strong></h6>
                    <p v-html="exam.indications"></p>
                </b-card>
                <div v-if="information.state" class="text-left mt-5">
                    <p><b>{{ information.message }}</b></p>
                    <b-alert show class="mt-2 mb-2" variant="info">
                        <strong><b-icon-exclamation-circle></b-icon-exclamation-circle> Importante</strong><br>
                        <p>Una vez iniciado el examen cuentas con <strong>{{ exam.duration }} minutos</strong> para resolverlo. En caso de salir del examen, lo que hayas contestado será guardado y no podrás ingresar de nuevo para continuar contestando.</p>
                    </b-alert>
                    <b-button variant="success" pill :disabled="!enable"
                        :href="`/exams/start_exam/${exam.id}`">
                        Iniciar examen
                    </b-button>
                </div>
                <b-alert v-else show class="mt-2" variant="warning">
                    {{ information.message }}
                </b-alert>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import moment from 'moment';
import formatTime from '../../../mixins/formatTime';
import InformationExam from '../../exam/InformationExam.vue';
export default {
    props: ['exam', 'information'],
    components: { InformationExam },
    mixins: [formatTime],
    data() {
        const now = new Date();
        // FECHA ACTUAL
        const mon = now.getMonth() + 1;
        const month = this.check_time(mon);
        const dat = now.getDate();
        const date = this.check_time(dat);
        return {
            enable: false,
            today: `${now.getFullYear()}-${month}-${date}`,
            time_actual: '00:00:00',
            examen_hora_inicio: `${this.exam.start_date} ${this.exam.start_time}`,
            examen_hora_termino: `${this.exam.start_date} ${this.exam.end_time}`,
        }
    },
    mounted: function () {
        setInterval(this.updateTime, 1000);
    },
    methods: {
        // OBTENER EN TIEMPO REAL LA HORA
        updateTime() {
            var cd = new Date();
            this.time_actual = this.zeroPadding(cd.getHours(), 2) + ':' + this.zeroPadding(cd.getMinutes(), 2) + ':' + this.zeroPadding(cd.getSeconds(), 2);
            let dia_hoy = `${this.today} ${this.time_actual}`;
            let dif_inicio = (moment(dia_hoy).diff(this.examen_hora_inicio, 'seconds')) / 60;
            let dif_termino = (moment(dia_hoy).diff(this.examen_hora_termino, 'seconds')) / 60;
            
            if (dif_inicio >= 0 && dif_termino < 0)
                this.enable = true;
            else
                this.enable = false;
        }
    }
}
</script>

<style>

</style>