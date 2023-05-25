<template>
    <div>
        <div>
            <b-row>
                <b-col sm="3">
                    <div class="text-right">
                        <b-button v-if="!isAnswering" pill variant="dark" href="/student/home" block>
                            <b-icon-arrow-left-short></b-icon-arrow-left-short> Examenes
                        </b-button>
                    </div>
                    <b-list-group>
                        <!-- <b-list-group-item>
                            <label><strong>Fecha y hora actual:</strong></label>
                            <p>{{ today }} {{ time_actual }}</p>
                        </b-list-group-item> -->
                        <b-list-group-item>
                            <label><strong>Fecha de aplicación del examen:</strong></label>
                            <p>{{ exam.start_date }}</p>
                        </b-list-group-item>
                        <b-list-group-item>
                            <label><strong>Hora de inicio:</strong></label>
                            <p>{{ exam.start_time }}</p>
                        </b-list-group-item>
                        <b-list-group-item>
                            <label><strong>Hora de termino:</strong></label>
                            <p>{{ exam.end_time }}</p>
                        </b-list-group-item>
                    </b-list-group>
                </b-col>
                <b-col>
                    <div v-if="information.state" class="text-right">
                        <timer-component :time="prettyTime"></timer-component>
                    </div>
                    <div v-if="!isRunning && !isAnswering" class="text-left mt-2 mb-2">
                        <div v-if="information.state">
                            <b-row>
                                <b-col>
                                    <h6><strong>Indicaciones: </strong></h6>
                                    <p v-html="exam.indications"></p>
                                </b-col>
                                <b-col sm="3">
                                    <b-button variant="success" pill block
                                        @click="start" :disabled="load || !enable">
                                        Iniciar <b-icon-arrow-right-short></b-icon-arrow-right-short>
                                    </b-button>
                                </b-col>
                            </b-row>
                            <hr>
                            <div class="mt-5">
                                <p>{{ information.message }}</p>
                                <b-alert show class="mt-2" variant="info">
                                    <strong><b-icon-exclamation-circle></b-icon-exclamation-circle> Importante</strong><br>
                                    <p>Una vez iniciado el examen cuentas con <strong>{{ exam.duration }} minutos</strong> para resolverlo. En caso de salir del examen, lo que hayas contestado será guardado y no podrás ingresar de nuevo para continuar contestando.</p>
                                </b-alert>
                            </div>
                        </div>
                        <b-alert v-else show class="mt-2" variant="warning">
                            {{ information.message }}
                        </b-alert>
                    </div>
                    <div v-if="isAnswering">
                        <div v-if="isRunning && time > 0">
                            <b-form @submit.prevent="check_level">
                                <b-card v-for="(instruction, i) in form.instructions" v-bind:key="i"
                                    border-variant="light" class="mb-2">
                                    <template #header>
                                        <!-- <h5>{{ instruction.topic.topic }}</h5> -->
                                        <p v-html="instruction.instruction"></p>
                                    </template>
                                    <track-component v-if="instruction.categorie_id == 2"
                                        :link="instruction.link" :text="'Track'"></track-component>
                                    <div v-for="(question, j) in form.questions" v-bind:key="j"
                                            class="mb-1">
                                        <div v-if="question.instruction_id == instruction.id">
                                            <div v-if="instruction.categorie_id !== 3">
                                                <form-open-component v-if="question.type_id == 1"
                                                    :question="question" :load="load">
                                                </form-open-component>
                                                <form-select-component v-if="question.type_id == 2"
                                                    :question="question" :load="load">
                                                </form-select-component>
                                                <form-multiple-component v-if="question.type_id == 3"
                                                    :question="question" :load="load">
                                                </form-multiple-component>
                                            </div>
                                            <div v-else>
                                                <b-row>
                                                    <b-col>
                                                        <label v-html="question.question"></label>
                                                    </b-col>
                                                    <b-col sm="3">
                                                        <b-button @click="btn_disabled(question.id)" 
                                                            :id="`btn-record${question.id}`"
                                                            :href="`/exams/go_record/${exam.id}/${question.id}`" 
                                                            variant="secondary" pill target="_blank" :disabled="load">
                                                            <b-icon-mic></b-icon-mic> Grabar
                                                        </b-button>
                                                    </b-col>
                                                </b-row>
                                            </div>
                                        </div>
                                    </div>
                                </b-card>
                                <b-row class="mt-2">
                                    <b-col class="text-center">
                                        <b-alert v-if="load" show variant="info">
                                            <b-spinner variant="primary"></b-spinner> 
                                             Cargando..
                                        </b-alert>
                                    </b-col>
                                    <b-col sm="4" class="text-right ">
                                        <b-button id="btn-actions" pill type="submit"
                                            :disabled="load">
                                            <b-icon-arrow-right></b-icon-arrow-right> Continuar
                                        </b-button>
                                    </b-col>
                                </b-row>
                            </b-form>
                        </div>
                    </div>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
// // CONFIRMAR ANTES DE RECARGAR
// window.onbeforeunload = function() {
//   return "¿Desea recargar la página web?";
// };
import moment from 'moment';
import formatTime from '../../../mixins/formatTime';
export default {
    props: ['exam'],
    mixins: [formatTime],
	data() {
        const now = new Date();
        // FECHA ACTUAL
        const mon   = now.getMonth() + 1;
        const month = this.check_time(mon);
        const dat   = now.getDate();
        const date  = this.check_time(dat);
        return {
            isRunning: false,
            isAnswering: false,
            minutes: 0,
            secondes: 0,
            time: this.exam.duration * 60,
            timer: null,
            load: false,
            form: {
                exam_id: this.exam.id,
                start_time: null, 
                duration: null, instructions: [],
                questions: []
            },
            time_actual: '00:00:00',
            enable: false,
            today: `${now.getFullYear()}-${month}-${date}`,
            examen_hora_inicio: `${this.exam.start_date} ${this.exam.start_time}`,
            examen_hora_termino: `${this.exam.start_date} ${this.exam.end_time}`,
            hora_inicio: null,
            information: { state: true, message: ''}
        }
    },
    created: function() {
        this.format_time();
    },
    mounted: function(){
        setInterval(this.updateTime, 1000);
    },
	computed: {
		prettyTime () {
            let time = this.time / 60
            this.minutes = parseInt(time)
            this.secondes = Math.round((time - this.minutes) * 60)
            return this.minutes+":"+this.secondes
		},
	},
	methods: {
        // OBTENER LA FECHA ACTUAL Y COMPARAR
        format_time(){
            let number_days = moment().diff(this.exam.start_date, 'days');
            if(number_days > 1) {
                this.information.message = `El examen fue hace ${number_days} día(s).`;
                this.information.state = false;
            }
            if(number_days == 1) {
                this.information.message = 'El examen fue el día de ayer';
                this.information.state = false;
            }
            if(number_days == 0 && this.exam.start_date == this.today) {
                this.information.message = 'El examen es el día de hoy.';
                this.information.state = true;
            }
            if(number_days == 0 && this.exam.start_date != this.today) {
                this.information.message = 'El examen es el día de mañana.';
                this.information.state = true;
            }
        },
        // OBTENER EN TIEMPO REAL LA HORA
        updateTime() {
            var cd = new Date();
            this.time_actual =  this.zeroPadding(cd.getHours(), 2) + ':' + this.zeroPadding(cd.getMinutes(), 2) + ':' + this.zeroPadding(cd.getSeconds(), 2);
            let dia_hoy = `${this.today} ${this.time_actual}`;
            
            let dif_inicio = (moment(dia_hoy).diff(this.examen_hora_inicio, 'seconds')) / 60;
            let dif_termino = (moment(dia_hoy).diff(this.examen_hora_termino, 'seconds')) / 60;
            
            if(dif_inicio >= 0 && dif_termino < 0) 
                this.enable = true; 
            else {
                if(this.timer != null) this.stop();
                this.enable = false;
            }
        },
        zeroPadding(num, digit) {
            var zero = '';
            for(var i = 0; i < digit; i++) {
                zero += '0';
            }
            return (zero + num).slice(-digit);
        },
        // INICIAR EXAMEN
		start () {
            this.load = true;
            axios.post('/exams/start_exam', this.exam).then(response => {
                this.isRunning = true;
                this.isAnswering = true;
                this.hora_inicio = this.time_actual;
                if (!this.timer) {
                    this.timer = setInterval( () => {
                        if (this.time > 0) {
                            this.time--
                        } else {
                            clearInterval(this.timer)
                            this.time_out();
                        }
                    }, 1000)
                }
                this.form.instructions = response.data.instructions;
                this.form.questions = response.data.questions;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // DETENER CRONOMETRO
        stop() {
            this.isRunning = false
            clearInterval(this.timer)
            this.timer = null
        },
        // reset () {
        //     this.stop()
        //     this.time = 0
        //     this.secondes = 0
        //     this.minutes = 0
        // },
        // REVISAR PREGUNTAS CONTESTADAS Y CONTINUAR
        check_level(){
            this.load = true;
            axios.post('/exams/check_level', this.form).then(response => {
                if(response.data.status == 1){
                    // this.form.instructions = response.data.datos;
                    this.form.instructions = response.data.datos.instructions;
                    this.form.questions = response.data.datos.questions;
                } else {
                    this.stop();
                    this.make_swal("El examen ha terminado.", "Presiona en Ver resultado.");
                }
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // TIEMPO AGOTADO
        time_out(){
            this.load = true;
            axios.post('/exams/time_out', this.form).then(response => {
                this.make_swal("Tiempo agotado", "El tiempo para contestar el examen a terminado. Presiona en Ver resultado.");
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // MOSTRAR SWAL
        make_swal(title, text){
            swal({
                title: title,
                text: text,
                icon: "info",
                button: "Ver resultado",
            }).then((value) => {
                location.href = `/student/results/${this.form.exam_id}`;
            });
        },
        btn_disabled(question_id){
            var btnRecord = document.getElementById(`btn-record${question_id}`);
            btnRecord.disabled = true;
        }
	}
}
</script>

<style>

</style>