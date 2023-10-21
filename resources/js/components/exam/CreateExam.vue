<template>
    <div>
        <!-- <b-alert v-if="errorSchool" class="text-center"
            variant="info" :show="dismissCountDown" dismissible 
            @dismissed="dismissCountDown=0" @dismiss-count-down="countDownChanged">
            La escuela seleccionada aun no tiene profesores asignados, por favor ve a la sección de profesores para agregar el profesor.
        </b-alert> -->
        <b-row class="mb-2">
            <b-col>
                <h5 class="mb-3"><b>Crear examen</b></h5>
            </b-col>
            <b-col sm="2">
                <b-button pill id="btn-actions" :disabled="load" 
                    href="/teacher/exams">
                    <b-icon-arrow-left></b-icon-arrow-left> Mis examenes
                </b-button>
            </b-col>
        </b-row><hr>
        <div v-if="step_1">
            <h5>PASO 1: INGRESAR DATOS DEL EXAMEN</h5><hr>
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
    </div>
</template>

<script>
import searchSchool from '../../mixins/searchSchool';
export default {
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
            swal("OK", "El examen fue creado exitosamente. Presiona en OK para visualizarlo.", "success")
                .then((value) => {
                    location.href = `/teacher/exams`;
                });
        },
        set_step(uno, dos, tres){
            this.step_1 = uno;
            this.step_2 = dos;
            this.step_3 = tres;
            // this.$emit('step_proccess', null);
        },
        // OBTENER A LOS PROFESORES DE LA ESCUELA SELECCIONADA
        // PENDIENTE DE REVISAR
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