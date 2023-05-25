<template>
    <div>
        <!-- <b-alert v-if="errorSchool" class="text-center"
            variant="info" :show="dismissCountDown" dismissible 
            @dismissed="dismissCountDown=0" @dismiss-count-down="countDownChanged">
            La escuela seleccionada aun no tiene profesores asignados, por favor ve a la sección de profesores para agregar el profesor.
        </b-alert> -->
        <div v-if="step_1">
            <h5>PASO 1: CREAR EXAMEN</h5><hr>
            <form-datos :exam="exam" :edit="false" @exam_created="exam_created"></form-datos>
        </div>
        <div v-if="step_2">
            <h5>PASO 2: ELEGIR TEMAS</h5><hr>
            <form-topics :exam="exam" :edit="false" @topics_saved="topics_saved"></form-topics>
        </div>
        <div v-if="step_3">
            <h5>PASO 3: ELEGIR PREGUNTAS</h5><hr>
            <form-questions :exam="exam" @questions_created="questions_created"></form-questions>
        </div>
        <!-- <b-form @submit.prevent="onSubmit">
            <b-row>
                <b-col sm="5" class="container">
                    <div v-if="role.role == 'admin'">
                        <b-form-group label="Escuela">
                            <b-form-input v-model="sSchool" @keyup="show_schools()"
                                style="text-transform:uppercase;" required placeholder="Escribe el nombre de la escuela"
                            ></b-form-input>
                            <div class="list-group" v-if="schools.length" id="list-drop-down">
                                <a 
                                    href="#" class="list-group-item list-group-item-action" 
                                    v-for="(school, i) in schools" v-bind:key="i" 
                                    @click="select_school(school)">
                                    {{ school.school }}
                                </a>
                            </div>
                        </b-form-group>
                        <b-form-group label="Profesor">
                            <b-form-select v-model="exam.teacher_id" :options="teachers"
                                required :disabled="load || exam.school_id == null || teachers.length == 0">
                            </b-form-select>
                        </b-form-group>
                    </div>
                    <b-form-group label="Nombre del examen">
                        <b-form-input v-model="exam.name" required
                            style="text-transform:uppercase;"
                        ></b-form-input>
                    </b-form-group>
                    <b-row>
                        <b-col>
                            <b-form-group label="Margen de error por nivel">
                                <b-form-input v-model="exam.error_range" type="number"
                                    required :disabled="load">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-card v-if="!chooseTopics" class="mt-4">
                        <p><strong>Elegir reactivos</strong></p>
                        <b-row>
                            <b-col>
                                <b-button pill :disabled="load"
                                        id="btn-actions" block @click="choose_topics()">
                                    <b-icon-arrow-right-circle-fill></b-icon-arrow-right-circle-fill> Continuar
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-card>
                </b-col>
                <b-col v-if="chooseTopics" sm="7">
                    <select-topics :levels="levels" @selected_topics="selected_topics"></select-topics>
                </b-col>
            </b-row>
            <hr>
            <b-alert v-if="errorLevels" class="text-center"
                variant="warning" :show="dismissCountDown" dismissible 
                @dismissed="dismissCountDown=0" @dismiss-count-down="countDownChanged">
                Es necesario elegir mínimo un tema por cada nivel.
            </b-alert>
            <b-button type="submit" block pill :disabled="load"
                variant="success" class="container col-md-5 mt-2">
                <b-icon-check-circle></b-icon-check-circle> Guardar
            </b-button>
        </b-form> -->
    </div>
</template>

<script>
import searchSchool from '../../mixins/searchSchool';
export default {
    props: ['role'],
    mixins: [searchSchool],
    data(){
        return {
            teachers: [],
            load: false,
            exam: {
                id: null,
                school_id: null,
                group_id: null,
                teacher_id: null,
                name: null,
                number_reagents: 2,
                error_range: 2,
                duration: 40,
                topics: [],
                categories: []
            },
            topics: [], // REVISAR
            levels: [],
            chooseTopics: false,
            lastLevel: {},
            errorSchool: false,
            step_1: true,
            step_2: false,
            step_3: false 
        }
    },
    methods: {
        // EXAMEN CREADO
        exam_created(exam){
            this.exam.id = exam.id;
            this.set_step(false, true, false);
        },
        // TEMAS GUARDADOS
        topics_saved(exam){
            this.exam.id = exam.id;
            this.set_step(false, false, true);
        },
        // PREGUNTAS GUARDADAS
        questions_created(){
            this.set_step(false, false, false);
            this.$emit('exams_created', null);
        },
        set_step(uno, dos, tres){
            this.step_1 = uno;
            this.step_2 = dos;
            this.step_3 = tres;
            this.$emit('step_proccess', null);
        },
        // OBTENER A LOS PROFESORES DE LA ESCUELA SELECCIONADA
        select_school(school){
            axios.get('/schools/show', {params: {school_id: school.id}}).then(response => {
                if(response.data.teachers.length > 0){
                    let ts = response.data.teachers;
                    this.teachers = [];
                    this.teachers.push({
                        value: null, text: 'Selecciona al profesor', disabled: true
                    });
                    ts.forEach(t => {
                        this.teachers.push({
                            value: t.id, text: t.user.name
                        });
                    });
                    this.exam.school_id = school.id;
                    this.sSchool = school.school;
                } else {
                    this.errorSchool = true;
                    this.dismissCountDown = this.dismissSecs;
                }
                this.schools = [];
            });
        },
        
        
    }
}
</script>