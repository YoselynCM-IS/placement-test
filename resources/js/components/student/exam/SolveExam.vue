<template>
    <div>
        <b-card>
            <b-row>
                <b-col>
                    <h5>{{ exam.name }}</h5>
                    <p v-html="exam.instructions"></p>
                </b-col>
                <b-col sm="4" class="text-right">
                    <timer-component v-if="isRunning" :time="prettyTime"></timer-component>
                </b-col>
            </b-row>
        </b-card>
        <div v-if="isRunning && time > 0" class="mt-3">
            <b-form @submit.prevent="check_level">
                <b-card v-for="(instruction, i) in form.instructions" v-bind:key="i"
                    border-variant="light" class="mb-2">
                    <template #header>
                        <!-- <h5>{{ instruction.topic.topic }}</h5> -->
                        <p v-html="instruction.instruction"></p>
                    </template>
                    <track-component v-if="instruction.categorie_id == 2"
                        :link="instruction.link" :text="'Track'"></track-component>
                    <div v-for="(question, j) in instruction.questions" v-bind:key="j"
                            class="mb-1">
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
                                    <!-- :href="" target="_blank" -->
                                    <b-button @click="btn_disabled(question.id)" 
                                        :id="`btn-record${question.id}`"
                                        variant="secondary" pill :disabled="load">
                                        <b-icon-mic></b-icon-mic> Grabar
                                    </b-button>
                                </b-col>
                            </b-row>
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
        <b-alert v-else show variant="secondary" class="mt-3 text-center">
            <b-spinner variant="secondary"></b-spinner> 
            <b>Cargando examen..</b>
        </b-alert>
    </div>
</template>

<script>
import moment from 'moment';
import formatTime from '../../../mixins/formatTime';
export default {
    props: ['datos'],
    mixins: [formatTime],
    data() {
        const now = new Date();
        // FECHA ACTUAL
        const mon = now.getMonth() + 1;
        const month = this.check_time(mon);
        const dat = now.getDate();
        const date = this.check_time(dat);
        return {
            isRunning: false,
            minutes: 0,
            secondes: 0,
            time: this.datos['exam'].duration * 60,
            timer: null,
            load: false,
            form: {
                exam_id: this.datos['exam'].id,
                instructions: this.datos['instructions']
            },
            time_actual: '00:00:00',
            time_acheck: null,
            exam: this.datos['exam'],
            today: `${now.getFullYear()}-${month}-${date}`,
            examen_hora_inicio: `${this.datos['exam'].start_date} ${this.datos['exam'].start_time}`,
            examen_hora_termino: `${this.datos['exam'].start_date} ${this.datos['exam'].end_time}`,
            loadExam: false
        }
    },
    mounted: function () {
        if(!this.time_acheck)
            this.time_acheck = setInterval(this.updateTime, 1000);
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
        // OBTENER EN TIEMPO REAL LA HORA
        updateTime() {
            var cd = new Date();
            this.time_actual = this.zeroPadding(cd.getHours(), 2) + ':' + this.zeroPadding(cd.getMinutes(), 2) + ':' + this.zeroPadding(cd.getSeconds(), 2);
            let dia_hoy = `${this.today} ${this.time_actual}`;
            let dif_inicio = (moment(dia_hoy).diff(this.examen_hora_inicio, 'seconds')) / 60;
            let dif_termino = (moment(dia_hoy).diff(this.examen_hora_termino, 'seconds')) / 60;
            if (dif_inicio >= 0 && dif_termino < 0) {
                this.isRunning = true;
                if (!this.timer) {
                    this.timer = setInterval(() => {
                        if (this.time > 0) {
                            this.time--
                        } else {
                            clearInterval(this.timer)
                            this.time_out();
                        }
                    }, 1000)
                }
            } else {
                clearInterval(this.time_acheck)
                clearInterval(this.timer)
                this.time_out();
            }
        },
        // DETENER CRONOMETRO
        stop() {
            this.isRunning = false
            clearInterval(this.timer)
            this.timer = null
        },
        // REVISAR PREGUNTAS CONTESTADAS Y CONTINUAR
        check_level(){
            this.load = true;
            axios.post('/exams/check_level', this.form).then(response => {
                this.form.instructions = [];
                if (response.data.status == 1) {
                    this.form.instructions = response.data.datos.instructions;
                } else {
                    this.stop();
                    this.make_swal("El examen ha terminado.", "Presiona en Ver resultado.", 'results');
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
                this.make_swal("Tiempo agotado", "El tiempo para contestar el examen a terminado. Presiona en Ver resultado.", 'results');
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // MOSTRAR SWAL
        make_swal(title, text, url){
            swal({
                title: title,
                text: text,
                icon: "info",
                button: "Ver resultado",
            }).then((value) => {
                if (url == 'results')
                    location.href = `/student/results/${this.form.exam_id}`;
                else
                    this.time_out();
            });
        },
        btn_disabled(question_id) {
            var btnRecord = document.getElementById(`btn-record${question_id}`);
            btnRecord.disabled = true;
            window.open(`/exams/go_record/${this.exam.id}/${question_id}`, '_blank');
        }
	}
}
</script>

<style>

</style>