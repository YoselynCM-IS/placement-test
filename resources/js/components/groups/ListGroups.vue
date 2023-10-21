<template>
    <div class="mb-5">
        <b-row class="mb-2">
            <b-col>
                <pagination size="default" :limit="1" :data="groupsData" 
                    @pagination-change-page="get_results">
                    <span slot="prev-nav">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </span>
                    <span slot="next-nav">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </span>
                </pagination>
            </b-col>
            <b-col class="text-right" sm="3">
                <b-button pill variant="dark" block v-b-tooltip.hover 
                            title="Descargar plantilla para subir lista de alumnos"
                            href="/groups/download_template">
                    <b-icon-download></b-icon-download> Plantilla
                </b-button>
            </b-col>
            <b-col class="text-right" sm="3">
                <b-button pill id="btn-actions" block @click="new_group()">
                    <b-icon-plus></b-icon-plus> Crear grupo
                </b-button>
            </b-col>
        </b-row>
        <div v-if="!load">
            <b-list-group v-if="groupsData.data.length > 0">
                <b-list-group-item v-for="(group, i) in groupsData.data" v-bind:key="i">
                    <b-row>
                        <b-col>{{ group.group }}</b-col>
                        <b-col class="text-right">
                            <b-button pill id="btn-actions" v-b-tooltip.hover size="sm"
                                title="Agregar alumno" @click="add_student(group)">
                                <b-icon-person-plus></b-icon-person-plus>
                            </b-button>
                            <b-button pill id="btn-actions" v-b-tooltip.hover size="sm"
                                title="Subir lista de alumnos" @click="upload_file(group)">
                                <b-icon-upload></b-icon-upload>
                            </b-button>
                            <b-button pill id="btn-actions" v-b-tooltip.hover @click="list_students(group)"
                                title="Lista de alumnos" :disabled="group.students_count == 0" size="sm">
                                <b-icon-people></b-icon-people>
                            </b-button>
                            <b-button pill id="btn-actions" class="text-white" 
                                v-b-tooltip.hover title="Editar grupo" size="sm"
                                @click="edit_group(group)">
                                <b-icon-pencil></b-icon-pencil>
                            </b-button>
                            <b-button pill id="btn-actions" v-b-tooltip.hover 
                                title="Eliminar grupo" size="sm" @click="delete_group(group)">
                                <b-icon-x></b-icon-x>
                            </b-button>
                        </b-col>
                    </b-row>
                </b-list-group-item>
            </b-list-group>
            <b-alert v-else show variant="secondary">
                No se han creado grupos
            </b-alert>
        </div>
        <div v-else class="text-center text-info my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Cargando...</strong>
        </div>
        <!-- MODALS -->
        <b-modal v-model="openNEGroup" :title="`${!editGroup ? 'Agregar':'Editar'} grupo`"
            hide-backdrop hide-footer>
            <create-edit-group :form="group" :edit="editGroup"
                @group_created="group_created">
            </create-edit-group>
        </b-modal>
        <b-modal hide-footer hide-backdrop title="Subir lista de alumnos" v-model="openUpload">
            <upload-students :group="group" @upload_students="upload_students"></upload-students>
        </b-modal>
        <b-modal hide-footer hide-backdrop title="Agregar alumno" v-model="openAdd">
            <add-edit-student :form="form" @student_created="student_created" :edit="false"></add-edit-student>
        </b-modal>
        <b-modal hide-footer title="Lista de alumnos" 
            v-model="openList" size="xl" scrollable>
            <list-group-student :group="group" :role_id="role_id"></list-group-student>
        </b-modal>
        <!-- CONFIRMAR ELIMINACIÓN -->
        <b-modal ref="modal-delete_group" hide-footer size="sm" centered 
            class="text-center" title="Eliminar grupo">
            <p>¿Estás seguro de eliminar el grupo?</p>
            <b-button pill block variant="danger" @click="confirm_delete()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar grupo
            </b-button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: ['user', 'role_id'],
    data(){
        return {
            groupsData: {},
            openNEGroup: false,
            editGroup: false,
            openUpload: false,
            group: { id: null, group: null, user_id: this.user.id },
            openList: false,
            openAssign: false,
            exams: [],
            load: false,
            group_id: null,
            openAdd: false,
            form: {}
        }
    },
    created: function(){
        this.get_results();
    },
    methods: {
        // OBTENER TODOS LOS REGISTROS
        get_results(page = 1){
            this.http_groups(page);
        },
        http_groups(page = 1){
            this.load = true;
            axios.get(`/groups/by_user?page=${page}`, {params: {teacher_id: this.user.teacher.id}}).then(response => {
                this.groupsData = response.data;
                this.load = false;
            });
        },
        // CREAR GRUPO
        new_group(){
            this.group.id = null;
            this.group.group = null;
            this.openNEGroup = true;
            this.editGroup = false;
        },
        // GRUPO GUARDADO
        group_created(group){
            this.openNEGroup = false;
            if(!this.editGroup)
                swal("OK", "El grupo se guardo correctamente.", "success");
            else 
                swal("OK", "El grupo se actualizo correctamente.", "success");
            
            this.http_groups();
        },
        // SUBIR ARCHIVO
        upload_file(group){
            if(group.students_count < 50){
                this.group.id = group.id;
                this.group.name = group.group;
                this.openUpload = true;
            } else {
                swal("OK", "El grupo ya cuenta el máximo de alumnos permitido.", "info");
            }
        },
        // AGREGAR ALUMNO
        add_student(group){
            if(group.students_count < 50){
                this.form.name = null;
                this.form.email = null;
                this.form.group_id = group.id;
                this.openAdd = true;
            } else {
                swal("OK", "El grupo ya cuenta el máximo de alumnos permitido.", "info");
            }
        },
        // STUDENTS GUARDADOS
        upload_students(){
            this.openUpload = false;
            swal("OK", "El archivo se subio exitosamente. Presiona en OK para actualizar la información del grupo.", "success")
            .then((value) => {
                location.href = '/teacher/groups';
            });
        },
        // ALUMNO GUARDADO
        student_created(){
            this.openAdd = false;
            swal("OK", "El alumno se guardo correctamente.", "success")
            .then((value) => {
                location.href = '/teacher/groups';
            });
        },
        // MOSTRAR LISTA DE ALUMNOS
        list_students(group){
            this.group.id = group.id;
            this.group.group = group.group;
            this.openList = true;
        },
        // EDITAR GRUPO
        edit_group(group){
            this.group.id = group.id;
            this.group.group = group.group;
            this.openNEGroup = true;
            this.editGroup = true;
        },
        // ELIMINAR GRUPO
        delete_group(group){
            axios.get('/groups/exam_assignments', {params: {group_id: group.id}}).then(response => {
                if(response.data.exams_count == 0 && response.data.students_count == 0){
                    this.group_id = group.id;
                    this.$refs['modal-delete_group'].show();
                } else {
                    swal("", "El grupo no puede ser eliminado ya que tiene un examen asignado y/o una lista de alumnos.", "warning");
                }
            }).catch(error => { });
        },
        // CONFIRMAR ELIMINACIÓN
        confirm_delete(){
            this.load = true;
            axios.delete('/groups/delete', {params: {group_id: this.group_id}}).then(response => {
                this.$refs['modal-delete_group'].hide();
                swal("OK", "El grupo ha sido eliminado correctamente.", "success");
                this.http_groups(this.actual_page);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>