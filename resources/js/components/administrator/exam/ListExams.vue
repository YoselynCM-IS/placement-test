<template>
    <div>
        <b-row>
            <b-col>
                <b-form-group label="Buscar escuela">
                    <b-form-input v-model="sSchool" @keyup="show_schools()"
                        style="text-transform:uppercase;"
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
            </b-col>
            <b-col sm="3"></b-col>
            <b-col sm="3">
                <b-button pill id="btn-actions" block :disabled="load"
                    @click="create_exams()" class="mt-4">
                    <b-icon-plus></b-icon-plus> Crear examenes
                </b-button>
            </b-col>
        </b-row>
        <b-card no-body class="mt-2">
            <b-tabs pills card vertical nav-wrapper-class="w-25">
                <b-tab title="Inicio"></b-tab>
                <b-tab v-for="(school, i) in withExams" v-bind:key="i">
                    <template #title>
                        <b-row>
                            <b-col @click="get_exams(school.id)">
                                <a >{{ school.school }}</a>
                            </b-col>
                            <b-col sm="1">
                                <b-button id="btn-actions" size="sm" pill 
                                    :disabled="load" @click="get_exams(school.id)">
                                    <b-icon-arrow-right-short></b-icon-arrow-right-short>
                                </b-button>
                            </b-col>
                        </b-row>
                    </template>
                    <b-row>
                        <b-col>
                            <pagination size="default" :limit="1" :data="examsData" 
                                @pagination-change-page="get_results">
                                <span slot="prev-nav">
                                    <b-icon-chevron-left></b-icon-chevron-left>
                                </span>
                                <span slot="next-nav">
                                    <b-icon-chevron-right></b-icon-chevron-right>
                                </span>
                            </pagination>
                        </b-col>
                        <b-col>
                            <b-form-group>
                                <b-form-input v-model="sTeacher" @keyup="show_teachers()"
                                    style="text-transform:uppercase;" placeholder="Buscar profesor"
                                ></b-form-input>
                                <div class="list-group" v-if="teachers.length" id="list-drop-down">
                                    <a 
                                        href="#" class="list-group-item list-group-item-action" 
                                        v-for="(teacher, i) in teachers" v-bind:key="i" 
                                        @click="select_teacher(teacher)">
                                        {{ teacher.name }}
                                    </a>
                                </div>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-table :items="examsData.data" :fields="fieldsExam" responsive
                        :busy="load">
                        <template v-slot:cell(index)="data">
                            {{ data.index + 1 }}
                        </template>
                        <template v-slot:cell(created_at)="data">
                            {{ data.item.created_at | moment('DD-MM-YYYY') }}
                        </template>
                        <template v-slot:cell(view)="data" class="text-center">
                            <b-button pill id="btn-actions" size="sm" :disabled="load"
                                @click="show_exam(data.item)">
                                <b-icon-arrow-right-short></b-icon-arrow-right-short>
                            </b-button>
                        </template>
                        <template v-slot:cell(actions)="data" class="text-center">
                            <b-button pill id="btn-actions" class="text-white" size="sm"
                                @click="edit_exam(data.item)" :disabled="load">
                                <b-icon-pencil></b-icon-pencil>
                            </b-button>
                            <b-button pill id="btn-actions" @click="delete_exam(data.item)"
                                size="sm" :disabled="load">
                                <b-icon-x></b-icon-x>
                            </b-button>
                        </template>
                        <template v-slot:cell(send)="data">
                            <b-button v-if="!data.item.send" id="btn-actions" pill size="sm" 
                                @click="send_exam(data.item)">
                                <b-icon-arrow-up-right-circle-fill></b-icon-arrow-up-right-circle-fill>
                            </b-button>
                            <i v-else>Enviado</i>
                        </template>
                        <template #table-busy>
                            <div class="text-center text-info my-2">
                                <b-spinner class="align-middle"></b-spinner>
                                <strong>Cargando...</strong>
                            </div>
                        </template>
                    </b-table>
                </b-tab>
            </b-tabs>
        </b-card>
        <!-- MODALS -->
        <b-modal v-model="openCreate" title="Crear examen" size="xl" hide-footer>
            <create-exam :role="role" @exams_created="exams_created">
            </create-exam>
        </b-modal>
        <b-modal v-model="openDetails" size="xl" title="Examen (Vista del administrador)" hide-footer>
            <gral-details-exam :exam="examCom"></gral-details-exam>
        </b-modal>
        <b-modal hide-backdrop hide-footer title="Editar examen" v-model="openEditE"
            size="xl">
            <form-exam @updated_exam="updated_exam" :exam="exam"></form-exam>
        </b-modal>
        <b-modal ref="modal-delete_exam" hide-footer size="sm" centered 
            class="text-center" title="Eliminar examen">
            <p>¿Estás seguro de eliminar el examen?</p>
            <b-button pill block variant="danger" @click="confirm_delete()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar examen
            </b-button>
        </b-modal>
    </div>
</template>

<script>
import swal from 'sweetalert'
import searchSchool from '../../../mixins/searchSchool';

export default {
    props: ['registers', 'role'],
    mixins: [searchSchool],
    data(){
        return {
            openCreate: false,
            schools: [],
            withExams: this.registers,
            examsData: {},
            fieldsExam: [
                { key: 'index', label: 'N.' },
                { key: 'teacher.user.name', label: 'Profesor' },
                { key: 'name', label: 'Examen' },
                { key: 'created_at', label: 'Creado el' },
                { key: 'view', label: 'Mostrar' },
                { key: 'actions', label: 'Editar/Eliminar' },
                { key: 'send', label: 'Enviar' }
            ],
            load: false,
            openDetails: false,
            examCom: {},
            exam: {
                id: null, teacher_id: null, name: '', group_id: '', start_date: '', 
                start_time: '', end_time: '', duration: null, indications: '', categories: []
            },
            exam_id: null,
            school_id: null,
            openEditE: false,
            actual_page: 1,
            sTeacher: null,
            teacher_id: null,
            teachers: []
        }
    },
    methods: {
        // CREAR EXAMEN
        create_exams() {
            this.openCreate = true;
        },
        // EXAMENES CREADOS
        exams_created(school){
            swal("OK", "El examen fue creado exitosamente. Presiona en OK para visualizarlo.", "success")
            .then((value) => {
                location.href = `/administrator/exams`;
            });
        },
        // OBTENER PAGINACIÓN DE EXAMENES
        get_results(page = 1){
            this.actual_page = page;
            this.type_search();
        },
        type_search(){
            if(this.teacher_id == null){
                this.http_exams(this.actual_page);
            } else {
                this.http_byteacher(this.actual_page);
            }
        },
        // OBTENER EXAMENES
        get_exams(school_id){
            this.examsData = {};
            this.school_id = school_id;
            this.teacher_id = null;
            this.sTeacher = null;
            this.http_exams();
        },
        // MOSTRAR EXAMANES (HTTP)
        http_exams(page = 1){
            this.load = true;
            axios.get(`/exams/by_school?page=${page}`, {params: {school_id: this.school_id}}).then(response => {
                this.examsData = response.data;
                this.actual_page = page;
                this.load = false;
            });
        },
        // MOSTRAR DETALLES DEL EXAMEN
        show_exam(exam){
            axios.get('/exams/show', {params: {exam_id: exam.id}}).then(response => {
                this.examCom = response.data;
                this.openDetails = true;
            });
        },
        // EDITAR EXAMEN
        edit_exam(exam){
            this.exam = {
                id: exam.id, teacher_id: exam.teacher_id, name: exam.name, group_id: exam.group_id, 
                start_date: exam.start_date, start_time: exam.start_time, end_time: exam.end_time,
                error_range: exam.error_range, duration: exam.duration, indications: exam.indications,
                categories: []
            };
            
            this.openEditE = true;
        },
        // ELIMINAR EXAMEN
        delete_exam(exam){
            axios.get('/exams/check_students', {params: {exam_id: exam.id}}).then(response => {
                if(response.data == 0){
                    this.exam_id = exam.id;
                    this.$refs['modal-delete_exam'].show();
                } else {
                    swal("", "El examen no puede ser eliminado ya que ha sido asignado a un grupo de alumnos.", "warning");
                }
            });
        },
        // CONFIRMAR ELIMINAR EXAMEN
        confirm_delete(){
            this.load = true;
            axios.delete('/exams/delete', {params: {exam_id: this.exam_id}}).then(response => {
                this.$refs['modal-delete_exam'].hide();
                this.type_search();
                swal("OK", "El examen ha sido eliminado correctamente.", "success")
                .then((value) => {
                    if(this.examsData.data.length == 0){
                        location.href = `/administrator/exams`;
                    }
                });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // SELECCIONAR ESCUELA
        select_school(school){
            this.school_id = school.id;
            this.sSchool = school.name;
            this.withExams = [];
            this.withExams.push(school);
            this.schools = [];
            this.http_exams();
        },
        // ENVIAR EXAMEN
        send_exam(exam){
            this.load = true;
            let formData = { exam_id: exam.id };
            axios.put('/exams/send', formData).then(response => {
                this.load = false;
                this.type_search();
            })
            .catch(error => {
                this.load = false;
            });
        },
        // EXAMEN ACTUALIZADO
        updated_exam(exam){
            this.openEditE = false;
            swal("OK", "El examen se actualizo correctamente.", "success");
            this.type_search();
        },
        // MOSTRAR LISTA DE PROFESORES
        show_teachers(){
            if(this.sTeacher.length > 0 && this.sTeacher !== ' '){
                axios.get('/schools/by_teacher', {params: {school_id: this.school_id, teacher: this.sTeacher}}).then(response => {
                    this.teachers = response.data;
                });
            } else {
                this.teachers = [];
            }
        },
        // SELECCIONAR PROFESOR
        select_teacher(teacher){
            this.teacher_id = teacher.teacher_id;
            this.http_byteacher();
            this.sTeacher = teacher.name;
            this.teachers = [];
        },
        // HTTP DE BY TEACHER
        http_byteacher(page = 1){
            this.load = true;
            axios.get(`/exams/by_teacher?page=${page}`, {params: {teacher_id: this.teacher_id}}).then(response => {
                this.examsData = response.data;
                this.load = false;
            });
        }
    }
}
</script>

<style scoped>
    #list-drop-down{
        position: absolute;
        z-index: 100;
    }
</style>