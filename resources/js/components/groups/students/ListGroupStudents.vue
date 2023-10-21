<template>
    <div>
        <b-row class="mb-2">
            <b-col>
                <h6><strong>Grupo:</strong> {{ group.group }}</h6>
                <pagination size="default" :limit="1" :data="studentsData" 
                    @pagination-change-page="get_results">
                    <span slot="prev-nav">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </span>
                    <span slot="next-nav">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </span>
                </pagination>
            </b-col>
            <b-col sm="3" class="text-right">
                <b-button v-if="studentsLength > 0"
                    id="btn-actions" pill :disabled="load || selected.length === 0"
                    v-b-tooltip.hover :title="msgEnviar" @click="send_access()">
                    <b-icon-check></b-icon-check> Enviar accesos<br>Página {{ currentPage }}
                </b-button>
            </b-col>
        </b-row>
        <div v-if="!load">
            <b-table v-if="studentsLength > 0"
                :items="studentsData.data" :fields="fieldsStudents"
                :select-mode="selectMode" responsive="sm" ref="selectableTable"
                selectable @row-selected="onRowSelected"
                :per-page="perPage" :current-page="currentPage">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(actions)="data" class="text-center">
                    <b-button pill variant="warning" size="sm"
                        @click="edit_student(data.item.user)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-button>
                    <b-button pill variant="danger" size="sm"
                        @click="delete_student(data.item.user)">
                        <b-icon-x></b-icon-x>
                    </b-button>
                    <b-button v-if="role_id == 1" pill variant="dark" size="sm"
                        @click="delete_exam(data.item.user)">
                        <b-icon-arrow-repeat></b-icon-arrow-repeat>
                    </b-button>
                </template>
                <template v-slot:cell(send)="data">
                    <i>{{ !data.item.user.send ? 'No enviado':'Enviado' }}</i>
                </template>
                <template #cell(selected)="{ rowSelected }">
                    <template v-if="rowSelected">
                        <span aria-hidden="true">&check;</span>
                        <span class="sr-only">Selected</span>
                    </template>
                    <template v-else>
                        <span aria-hidden="true">&nbsp;</span>
                        <span class="sr-only">Not selected</span>
                    </template>
                </template>
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="5" class="text-right">Seleccionar todo</b-th>
                        <b-th>
                            <b-button variant="outline-success" @click="selectAllRows"
                                size="sm" pill v-b-tooltip.hover :title="msgSelect">
                                <b-icon-check></b-icon-check>
                            </b-button>
                        </b-th>
                    </b-tr>
                </template>
            </b-table>
            <b-alert v-else show variant="secondary">
                No se encontraron alumnos registrados en este grupo.
            </b-alert>
        </div>
        <div v-else class="text-center text-info my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Cargando...</strong>
        </div>
        <!-- MODALS -->
        <!-- EDITAR ALUMNO -->
        <b-modal v-model="openEditS" hide-footer hide-backdrop title="Editar alumno">
            <add-edit-student :form="form" @student_created="student_created" :edit="true"></add-edit-student>
        </b-modal>
        <!-- CONFIRMAR ELIMINACIÓN -->
        <b-modal ref="modal-delete_student" hide-footer size="sm" centered 
            class="text-center" title="Eliminar alumno">
            <p>¿Estás seguro de eliminar al alumno?</p>
            <b-button pill block variant="danger" @click="confirm_delete()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar alumno
            </b-button>
        </b-modal>
        <!-- ELIMINAR EXAMEN CONTESTADO DEL ALUMNO -->
        <b-modal ref="modal-delete_exam" hide-footer centered 
                class="text-center" title="Reiniciar examen">
            <b-table :items="userExams.exams" :fields="fieldsExams">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(actions)="data">
                    <b-button variant="dark" pill @click="confirmDelExam(data.item)">
                        <b-icon-arrow-repeat></b-icon-arrow-repeat>
                    </b-button>
                </template>
            </b-table>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: ['group', 'role_id'],
    data(){
        return {
            fieldsStudents: [
                { key: 'index', label: 'N.' },
                { key: 'user.name', label: 'Nombre' },
                { key: 'user.email', label: 'Correo electrónico' },
                { key: 'actions', label: 'Editar / Eliminar' },
                { key: 'send', label: 'Acceso' },
                { key: 'selected', label: 'Enviar acceso' }
            ],
            load: false,
            selectMode: 'multi',
            selected: [],
            msgSelect: 'Para seleccionar de manera individual, presiona en Enviar acceso sobre cada alumno.',
            msgEnviar: 'Enviar al correo electrónico de cada alumno los datos para acceder a la plataforma y realizar el examen de colocación.',
            perPage: 20,
            currentPage: 1,
            studentsData: {},
            studentsLength: 0,
            actual_page: 1,
            form: {},
            openEditS: false,
            user_id: null,
            userExams: { user_id: null, exams: [] },
            fieldsExams: [
                { key: 'index', label: 'N.' },
                { key: 'name', label: 'Examen' },
                { key: 'actions', label: 'Eliminar' },
            ]
        }
    },
    created: function(){
        this.get_results();
    },
    methods: {
        // OBTENER TODOS LOS REGISTROS
        get_results(page = 1){
            this.http_student(page);
        },
        // HTTP STUDENTS
        http_student(page = 1){
            this.load = true;
            axios.get(`/groups/list_students?page=${page}`, {params: {group_id: this.group.id}}).then(response => {
                this.studentsData = response.data;
                this.studentsLength = this.studentsData.data.length;
                this.actual_page = page;
                this.load = false;
            });
        },
        // SELECCION DE REGISTROS EN LA TABLA
        onRowSelected(items) {
            this.selected = items
        },
        // SELECCIONAR TODOS LOS REGISTROS
        selectAllRows() {
            this.$refs.selectableTable.selectAllRows()
        },
        // ENVIAR CORREOS
        send_access(){
            this.load = true;
            let access = { selected: this.selected };
            axios.put('/groups/send_access', access).then(response => {
                this.http_student(this.actual_page);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // EDITAR ESTUDIANTE
        edit_student(user){
            this.form.id = user.id;
            this.form.name = user.name;
            this.form.email = user.email;
            this.openEditS = true;
        },
        // STUDENT ACTUALIZADO
        student_created(student){
            this.http_student(this.actual_page);
            this.openEditS = false;
            swal("OK", "El alumno se actualizo correctamente.", "success");
        },
        // ELIMINAR ALUMNO
        delete_student(user){
            axios.get('/groups/student_assignments', {params: {user_id: user.id}}).then(response => {
                if(response.data.exams.length == 0){
                    this.user_id = user.id;
                    this.$refs['modal-delete_student'].show();
                } else {
                    swal("", "El alumno no puede ser eliminado ya que tiene un examen asignado.", "warning");
                }
            }).catch(error => { });
        },
        // CONFIRMAR ELIMINACIÓN
        confirm_delete(){
            this.load = true;
            axios.delete('/groups/delete_student', {params: {user_id: this.user_id}}).then(response => {
                this.$refs['modal-delete_student'].hide();
                swal("OK", "El alumno ha sido eliminado correctamente.", "success");
                this.http_student(this.actual_page);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        delete_exam(user) {
            this.userExams = { user_id: null, exams: [] };
            axios.get('/groups/student_assignments', { params: { user_id: user.id } }).then(response => {
                if (response.data.exams.length > 0) {
                    this.userExams.user_id = user.id;
                    this.userExams.exams = response.data.exams;
                    this.$refs['modal-delete_exam'].show();
                } else {
                    swal("", "El alumno no tiene algún examen asignado.", "warning");
                }
            }).catch(error => { });
        },
        confirmDelExam(exam) {
            this.load = true;
            let reiniciar = { user_id: this.userExams.user_id, exam_id: exam.id };
            axios.put('/exams/student_delete', reiniciar).then(response => {
                this.userExams = { user_id: null, exams: [] };
                this.$refs['modal-delete_exam'].hide();
                swal("OK", "El examen se reinició correctamente.", "success");
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>

<style>

</style>