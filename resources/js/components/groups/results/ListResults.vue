<template>
    <div>
        <b-tabs pills card vertical>
            <b-tab title="Home">
                Presionar sobre el grupo para visualizar la lista de alumnos y sus resultados del examen.
            </b-tab>
            <b-tab v-for="(group, i) in groups" v-bind:key="i" 
                :title="group.group" @click="get_student(group)">
                <b-row class="mb-2">
                    <b-col>
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
                            pill variant="dark" :href="`/groups/download_list/${group.id}`">
                            <b-icon-download></b-icon-download> Lista de alumnos
                        </b-button>
                    </b-col>
                </b-row>
                <div v-if="!load">
                    <b-table v-if="studentsLength > 0"
                        :items="studentsData.data" :fields="fieldsStudents">
                        <template v-slot:cell(index)="data">
                            {{ data.index + 1 }}
                        </template>
                        <template v-slot:cell(exams)="data">
                            <b-list-group v-if="data.item.exams.length > 0">
                                <b-list-group-item v-for="(exam, i) in data.item.exams" v-bind:key="i">
                                    <b-row>
                                        <b-col sm="8">
                                            {{ exam.name }}
                                            <!-- <ul>
                                                <li><strong>Nivel obtenido:</strong> {{ filter_level(exam.pivot.level_id) }}</li>
                                                <li><strong>Calificación:</strong> {{ exam | get_cal }} de 100</li>
                                                <li><strong>Puntuación:</strong> {{ exam.pivot.correct }} / {{ exam.pivot.total }}</li>
                                            </ul> -->
                                        </b-col>
                                        <b-col sm="4">
                                            <b-button pill size="sm" id="btn-actions"
                                                @click="get_details(data.item, exam)">
                                                <b-icon-info-circle></b-icon-info-circle> Detalles
                                            </b-button>
                                        </b-col>
                                    </b-row>
                                </b-list-group-item>
                            </b-list-group>
                            <h6 v-else><strong>No ha aplicado</strong></h6>
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
            </b-tab>
        </b-tabs>
        <b-modal v-model="modalDetails" hide-backdrop hide-footer 
            :title="student_dato.name" size="xl">
            <results-exam :advances="advances" :exam="exam" :student_id="student_dato.id"
                        :role_id="user.role_id">
            </results-exam>
        </b-modal>
    </div>
</template>

<script>
import filterLevel from '../../../mixins/filterLevel';
import formatCal from '../../../mixins/formatCal';
export default {
    props: ['user', 'registers'],
    mixins: [filterLevel, formatCal],
    data(){
        return {
            groups: this.registers,
            load: false,
            studentsData: {},
            studentsLength: 0,
            fieldsStudents: [
                { key: 'index', label: 'N.' },
                { key: 'user.name', label: 'Nombre' },
                { key: 'exams', label: 'Examen' },
                
            ],
            group_id: null,
            modalDetails: false,
            advances: [],
            exam: {},
            student_dato: { id: null, name: null },
            skills: []
        }
    },
    created: function(){
        if(this.group_id !== null){
            this.get_results();
        }
    },
    methods: {
        // OBTENER TODOS LOS ESTUDIANTES DEL GRUPO
        get_results(page = 1){
            this.http_students(page);
        },
        // HTTP DE STUDENT
        http_students(page = 1){
            this.load = true;
            this.studentsLength = 0;
            axios.get(`/groups/list_avances?page=${page}`, {params: {group_id: this.group_id}}).then(response => {
                this.studentsData = response.data;
                this.studentsLength = this.studentsData.data.length;
                this.load = false;
            });
        },
        // OBTENER STUDENTS
        get_student(group){
            this.group_id = group.id;
            this.http_students();
        },
        // OBTENER DETALLES
        get_details(student, exam){
            this.advances = [];
            this.exam = {};
            this.student_name = null;
            axios.get('/exams/get_advances', 
                    {params: {student_id: student.id, exam_id: exam.id}})
                .then(response => {
                this.exam = response.data.exam;
                this.advances = response.data.advances;
                this.skills = response.data.skills;
                // this.student_name = student.user.name;
                this.student_dato = { id: student.id, name: student.user.name };
                this.modalDetails = true;
            });
        }
    }
}
</script>

<style>

</style>