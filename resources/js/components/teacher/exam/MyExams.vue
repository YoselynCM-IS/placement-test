<template>
    <div>
        <div v-if="!openDetails">
            <b-row>
                <b-col><h4>Mis examenes</h4></b-col>
                <b-col sm="3">
                    <b-button pill id="btn-actions" block :disabled="load"
                        href="/exams/create">
                        <b-icon-plus></b-icon-plus> Crear examen
                    </b-button>
                </b-col>
            </b-row>
            <b-list-group v-if="exams.length > 0" class="mt-2">
                <b-list-group-item v-for="(exam, i) in exams" v-bind:key="i">
                    <b-row>
                        <b-col>
                            <strong>{{ exam.name }}</strong> <br>
                            <label id="font-1">
                                <strong>Fecha de aplicación: </strong>
                                {{ exam.start_date }} {{ exam.start_time }}
                                <strong>Duración: </strong> {{ exam.duration }} minutos
                            </label>
                        </b-col>
                        <b-col>
                            <b-button pill id="btn-actions" size="sm"
                                v-b-tooltip.hover title="Visualizar examen"
                                :href="`/exams/get_details/${exam.id}`">
                                <!-- @click="details_exam(exam)" -->
                                <b-icon-eye></b-icon-eye>
                            </b-button>
                            <b-button pill id="btn-actions" size="sm"
                                v-b-tooltip.hover title="Descargar examen"
                                @click="download_exam(exam)">
                                <b-icon-download></b-icon-download>
                            </b-button>
                            <b-button pill id="btn-actions" size="sm"
                                v-b-tooltip.hover title="Editar examen"
                                @click="edit_exam(exam)">
                                <b-icon-pencil></b-icon-pencil>
                            </b-button>
                            <b-button pill id="btn-actions" size="sm"
                                v-b-tooltip.hover title="Eliminar examen"
                                @click="delete_exam(exam)">
                                <b-icon-x></b-icon-x>
                            </b-button>
                            <b-button v-if="exam.start_date" 
                                pill id="btn-actions" size="sm"
                                v-b-tooltip.hover title="Enviar notificación de examen"
                                @click="notification_exam(exam)">
                                <b-icon-box-arrow-right></b-icon-box-arrow-right>
                            </b-button>
                        </b-col>
                    </b-row>
                </b-list-group-item>
            </b-list-group>
            <b-alert v-else variant="info" show class="mt-5">
                Aun no hay exámenes creados.
            </b-alert>
        </div>
        <div v-if="openDetails">
            <div v-if="!load">
                <gral-details-exam :exam="examCom"></gral-details-exam>
            </div>
            <div v-else class="text-center">
                <div class="d-flex justify-content-center mb-3">
                    <b-spinner style="width: 3rem; height: 3rem;"></b-spinner>
                </div>
                <h4><b>Cargando examen...</b></h4>
            </div> 
        </div>
        <!-- MODALS -->
        <b-modal hide-backdrop hide-footer title="Editar examen" v-model="openEditE"
            size="xl" scrollable>
            <form-exam @updated_exam="updated_exam" :exam="exam" :topics="topics"></form-exam>
        </b-modal>
        <!-- CONFIRMAR ELIMINACIÓN DE EXAMEN -->
        <b-modal ref="modal-delete_exam" hide-footer size="sm" centered 
            class="text-center" title="Eliminar examen">
            <p>¿Estás seguro de eliminar el examen?</p>
            <b-button pill block variant="danger" @click="confirm_delete()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar examen
            </b-button>
        </b-modal>
        <b-modal ref="modal-download_exam" hide-footer size="sm" centered 
            class="text-center" title="Descargar examen">
            <b-form-group v-slot="{ ariaDescribedby }"
                label="Elige las skills que deseas que aparezcan en tu examen">
                <b-form-checkbox-group
                    v-model="form.skills"
                    :options="categories"
                    :aria-describedby="ariaDescribedby"
                    stacked
                ></b-form-checkbox-group>
            </b-form-group>
            <b-button variant="dark" pill block :disabled="load || form.skills.length == 0"
                :href="`/exams/download/${form.exam_id}/${form.skills}`"
                @click="hideMDownload()">
                <b-icon-download></b-icon-download> Descargar
            </b-button>
        </b-modal>
    </div>
</template>

<script>
import swal from 'sweetalert'
export default {
    props: ['registers', 'role'],
    data() {
        return {
            exams: this.registers,
            openEditE: false,
            exam: {
                id: null, teacher_id: null, name: '', group_id: null, start_date: '', 
                start_time: '', end_time: '', duration: null, indications: '',
                questions: []
            },
            openDetails: false,
            examCom: {},
            load: false,
            topics: [],
            exam_id: null,
            stepProccess: false,
            form: {
                exam_id: null,
                skills: [],
            },
            categories: []
        }
    },
    methods: {
        // EDITAR EXAMEN
        edit_exam(exam){
            axios.get('/exams/show', {params: {exam_id: exam.id}}).then(response => {
                this.setDatos_exam(response.data.exam);
                this.openEditE = true;
                // questions: response.data.questions, topics: [], topics_s: response.data.topics, categories: []
            }); 
        },
        setDatos_exam(exam) {
            this.exam = {
                id: exam.id,
                teacher_id: exam.teacher_id,
                name: exam.name,
                group_id: exam.group_id,
                start_date: exam.start_date,
                start_time: exam.start_time,
                end_time: exam.end_time,
                error_range: exam.error_range,
                duration: exam.duration,
                indications: exam.indications,
                topics_count: exam.topics_count,
                questions_count: exam.questions_count,
                categories: [],
                topics: []
            };
        },
        // EXAMEN ACTUALIZADO
        updated_exam(){
            this.openEditE = false;
            swal("OK", "El examen se actualizo exitosamente.", "success")
            .then((value) => {
                location.href = `/teacher/exams`;
            });
        },
        // MOSTRAR DETALLES DEL EXAMEN
        // details_exam(exam){
        //     this.load = true;
        //     axios.get('/exams/show', {params: {exam_id: exam.id}}).then(response => {
        //         this.examCom = response.data;
        //         if(this.examCom.instructions.length > 0) {
        //             this.openDetails = true;
        //         } else {
        //             swal("", "El examen no se ha concluido, faltan temas/preguntas por agregar. Verificar en editar examen.", "info");
        //         }
        //         this.load = false;
        //     });
        // },
        // EXAMEN CREADO
        exams_created(){
            
        },
        // ENVIAR NOTIFICACIÓN DE EXAMEN
        notification_exam(exam){
            this.load = true;
            axios.put('/exams/notification', exam).then(response => {
                if(response.data) {
                    swal("OK", "La notificación a cada alumno se ha enviado correctamente.", "success");
                } else {
                    swal("", "El grupo no cuenta con alumnos. Ve a la sección de alumnos para agregarlos.", "warning");
                }
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
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
                swal("OK", "El examen ha sido eliminado correctamente.", "success")
                .then((value) => {
                    location.href = `/teacher/exams`;
                });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        step_proccess(){
            this.stepProccess = true;
        },
        download_exam(exam){
            this.form.exam_id = exam.id;
            this.form.skills = [];
            this.categories = [];
            exam.categories.forEach(categorie => {
                let d = { value: categorie.id, text: categorie.categorie };
                this.categories.push(d);
            });
            this.$refs['modal-download_exam'].show();
        },
        hideMDownload(){
            this.$refs['modal-download_exam'].hide();
        }
    }
}
</script>