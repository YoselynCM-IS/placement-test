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
            <b-col sm="3" class="text-right">
                <b-button pill id="btn-actions" block :disabled="load"
                    @click="new_teacher()" class="mb-2 mt-4">
                    <b-icon-plus></b-icon-plus> Agregar profesor
                </b-button>
            </b-col>
        </b-row>
        <b-card no-body class="mt-2">
            <b-tabs pills card vertical nav-wrapper-class="w-25">
                <b-tab title="Inicio">
                </b-tab>
                <b-tab v-for="(school, i) in withTeachers" v-bind:key="i" :title="school.school">
                    <template #title>
                        <b-row>
                            <b-col @click="get_teachers(school.id)">
                                <a >{{ school.school }}</a>
                            </b-col>
                            <b-col sm="1">
                                <b-button id="btn-actions" size="sm" pill 
                                    :disabled="load" @click="get_teachers(school.id)">
                                    <b-icon-arrow-right-short></b-icon-arrow-right-short>
                                </b-button>
                            </b-col>
                        </b-row>
                    </template>
                    <!-- VER LISTA DE PROFESORES -->
                    <div v-if="!viewGroups">
                        <pagination size="default" :limit="1" :data="teachersData" 
                            @pagination-change-page="get_results">
                            <span slot="prev-nav">
                                <b-icon-chevron-left></b-icon-chevron-left>
                            </span>
                            <span slot="next-nav">
                                <b-icon-chevron-right></b-icon-chevron-right>
                            </span>
                        </pagination>
                        <b-table :items="teachersData.data" :fields="fieldsTeachers" responsive
                            :busy="load">
                            <template v-slot:cell(index)="data">
                                {{ data.index + 1 }}
                            </template>
                            <template v-slot:cell(name)="data">
                                {{ data.item.name }}
                            </template>
                            <template v-slot:cell(created_at)="data">
                                {{ data.item.created_at | moment('DD-MM-YYYY') }}
                            </template>
                            <template v-slot:cell(actions)="data" class="text-center">
                                <b-button id="btn-actions" pill size="sm"
                                    @click="edit_teacher(data.item)">
                                    <b-icon-pencil></b-icon-pencil>
                                </b-button>
                                <b-button id="btn-actions" pill size="sm"
                                    @click="delete_teacher(data.item)">
                                    <b-icon-x></b-icon-x>
                                </b-button>
                            </template>
                            <template v-slot:cell(send)="data">
                                <b-button id="btn-actions" pill size="sm" :disabled="load"
                                    @click="send_data(data.item)">
                                    <b-icon-arrow-right-short></b-icon-arrow-right-short>
                                </b-button><br>
                                <i>{{ data.item.send ? 'Enviado':'No enviado' }}</i>
                            </template>
                            <template v-slot:cell(groups)="data">
                                <b-button id="btn-actions" pill size="sm" :disabled="load"
                                    @click="show_groups(data.item)">
                                    <b-icon-ui-radios-grid></b-icon-ui-radios-grid>
                                </b-button>
                            </template>
                            <template #table-busy>
                                <div class="text-center text-info my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Cargando...</strong>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <div v-else>
                        <b-row class="mb-2">
                            <b-col><b>Grupos</b> - Profesor(a) {{ user.name }}</b-col>
                            <b-col sm="3" class="text-right">
                                <b-button pill @click="viewGroups = false" block>
                                    <b-icon-arrow-left-square></b-icon-arrow-left-square> Regresar
                                </b-button>
                            </b-col>
                        </b-row>
                        <list-groups :user="user"></list-groups>
                    </div>
                </b-tab>
            </b-tabs>
        </b-card>

        <!-- MODALS -->
        <!-- AGREGAR EDITAR PROFESOR -->
        <b-modal v-model="openNewT" :title="`${!editTeacher ? 'Agregar':'Editar'} profesor`" 
            hide-backdrop hide-footer>
            <create-edit-teacher :form="form" :edit="editTeacher"
                @teacher_created="teacher_created">
            </create-edit-teacher>
        </b-modal>
        <!-- BORRAR PROFESOR -->
        <b-modal ref="modal-delete_teacher" hide-footer size="sm" centered 
            class="text-center" title="Eliminar profesor">
            <p>¿Estás seguro de eliminar al profesor?</p>
            <b-button pill block variant="danger" @click="confirm_delete()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar profesor
            </b-button>
        </b-modal>
    </div>
</template>

<script>
import searchSchool from '../../../../mixins/searchSchool';
export default {
    props: ['registers'],
    mixins: [searchSchool],
    data(){
        return {
            withTeachers: this.registers,
            fieldsTeachers: [
                { key: 'index', label: 'N.' },
                { key: 'name', label: 'Nombre' },
                { key: 'email', label: 'Correo electrónico' },
                { key: 'actions', label: 'Editar/Eliminar' },
                { key: 'send', label: 'Enviar datos' },
                { key: 'groups', label: 'Grupos' },
            ],
            load: false,
            openNewT: false,
            teachersData: {},
            school_id: null,
            editTeacher: false,
            form: { 
                id: null, name: null, email: null, school_id: null, school_name: null 
            },
            actual_page: 1,
            user: {},
            viewGroups: false
        }
    },
    methods: {
        select_school(school){
            this.school_id = school.id;
            this.sSchool = school.name;
            this.withTeachers = [];
            this.withTeachers.push(school);
            this.schools = [];
            this.http_teachers();
        },
        // OBTENER RESULTADOS POR PAGINA
        get_results(page = 1){
            this.http_teachers(page);
        },
        // AGREGAR PROFESOR
        new_teacher(){
            this.editTeacher = false;
            this.form = { id: null, name: null, email: null, school_id: null, school_name: null };
            this.openNewT = true;
        },
        // PROFESOR CREADO
        teacher_created(teacher){
            this.openNewT = false;
            if(!this.editTeacher){
                swal("OK", "El nuevo profesor se guardo correctamente.", "success")
                .then((value) => {
                    location.href = `/administrator/teachers`;
                });
            }
            else {
                this.http_teachers(this.actual_page);
                swal("OK", "El profesor se actualizo correctamente.", "success")
                .then((value) => {
                    if(this.teachersData.data.length == 0){
                        location.href = `/administrator/teachers`;
                    }
                });
                
            }
        },
        // MOSTRAR PROFESORES DE LA ESCUELA SELECCIONADA
        get_teachers(school_id){
            this.teachersData = {};
            this.school_id = school_id;
            this.viewGroups = false;
            this.http_teachers();
        },
        // CONSULTA HTTP DE PROFESORES
        http_teachers(page = 1){
            this.load = true;
            axios.get(`/schools/show_teachers?page=${page}`, {params: {school_id: this.school_id}}).then(response => {
                this.teachersData = response.data;
                this.actual_page = page;
                this.load = false;
            });
        },
        // EDITAR PROFESOR
        edit_teacher(user){
            this.form = { 
                id: user.id, 
                name: user.name, 
                email: user.email, 
                school_id: user.teacher.school_id,
                school_name: user.teacher.school.school
            };
            this.editTeacher = true;
            this.openNewT = true;
        },
        // BORRAR PROFESOR
        delete_teacher(user){
            axios.get('/administrator/teacher_assignments', {params: {user_id: user.id}}).then(response => {
                if(response.data.exams_count == 0 && response.data.groups_count == 0){
                    this.user_id = user.id;
                    this.$refs['modal-delete_teacher'].show();
                } else {
                    swal("", "El profesor no puede ser eliminado ya puede tener asignados exámenes o grupos.", "warning");
                }
            });
        },
        // CONFIRMAR ELIMINACIÓN
        confirm_delete(){
            this.load = true;
            axios.delete('/administrator/delete_teacher', {params: {user_id: this.user_id}}).then(response => {
                this.$refs['modal-delete_teacher'].hide();
                this.http_teachers(this.actual_page);
                swal("OK", "El profesor ha sido eliminado correctamente.", "success")
                .then((value) => {
                    if(this.teachersData.data.length == 0){
                        location.href = `/administrator/teachers`;
                    }
                });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // ENVIAR DATOS AL CORREO
        send_data(user){
            this.load = true;
            let form_id = { user_id: user.id }
            axios.put('/administrator/send_data', form_id).then(response => {
                swal("OK", "Los datos fueron enviados exitosamente.", "success");
                this.http_teachers(this.actual_page);
                this.load = false;
            }).catch(error => { 
                this.load = false;
            });
        },
        // MOSTRAR LOS GRUPOS
        show_groups(user){
            this.user = user;
            this.viewGroups = true;
        },
    }
}
</script>

<style scoped>
    #list-drop-down{
        position: absolute;
        z-index: 100;
    }
</style>