<template>
    <div>
        <!-- <results-exam :advances="{{$advances}}" 
                :exam="{{$exam}}" 
                :role_id="{{auth()->user()->role_id}}"
                :skills="{{$skills}}"
                :student_id="{{auth()->user()->student->id}}"></results-exam> -->
        <div v-if="!loaded">
            <h6><b>{{ exam.name }}</b></h6>
            <hr>
            <b-card title="" class="mb-3">
                <template #header>
                    <b-row>
                        <b-col><h6>Nivel</h6></b-col>
                        <b-col><h6>Correctas</h6></b-col>
                        <b-col><h6>Total</h6></b-col>
                        <b-col><h6>Calificación</h6></b-col>
                    </b-row>
                </template>
                <b-row v-for="(level, i) in levels" v-bind:key="i" >
                    <b-col :class="set_class(level)">
                        <b>{{ level.level.level }}</b>
                    </b-col>
                    <b-col :class="set_class(level)">{{ level.correct }}</b-col>
                    <b-col :class="set_class(level)">{{ level.total }}</b-col>
                    <b-col :class="set_class(level)">
                        {{ (level.correct / level.total) | get_cal }}
                    </b-col>
                </b-row>
            </b-card>
            <b-card title="" class="mb-3">
                <template #header>
                    <b-row>
                        <b-col><h6>Skill</h6></b-col>
                        <b-col><h6>Correctas</h6></b-col>
                        <b-col><h6>Total</h6></b-col>
                        <b-col><h6>Calificación</h6></b-col>
                    </b-row>
                </template>
                <b-row v-for="(skill, i) in skills" v-bind:key="i" >
                    <b-col><b>{{ skill.categorie }}</b></b-col>
                    <b-col>{{ set_calificacion(skill.categorie, skill.points) }}</b-col>
                    <b-col>{{ set_calificacion(skill.categorie, skill.total) }}</b-col>
                    <b-col>{{ (skill.points / skill.total) | get_cal }}</b-col>
                </b-row>
            </b-card>
        </div>
        <div v-else class="text-center text-info my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Cargando...</strong>
        </div>
    </div>
</template>

<script>
import formatCal from '../../../mixins/formatCal';
export default {
    props: ['exam', 'student_id'],
    mixins: [formatCal],
    data(){
        return {
            name_level: null,
            levels: [],
            loaded: false,
            skills: [],
            exam_dato: {}
        }
    },
    created: function(){
        this.get_results();
        this.get_advances();
        this.get_level();
    },
    methods: {
        get_results(){
            this.loaded = true;
            axios.get('/exams/get_results', {params: {exam_id: this.exam.id, student_id: this.student_id}}).then(response => {
                this.levels = response.data;
                this.loaded = false;
            });
        },
        get_level(){
            axios.get('/levels/show', {params: {level_id: this.exam.pivot.level_id}}).then(response => {
                this.name_level = response.data.level;
            });
        },
        get_advances(){
            axios.get('/exams/get_advances', 
                    {params: {student_id: this.student_id, exam_id: this.exam.id}})
                .then(response => {
                this.exam_dato = response.data.exam;
                // this.advances = response.data.advances;
                this.skills = response.data.skills;
            });
        },
        set_class(level){
            return `bg-${level.level.level == this.name_level ?'success':''}`;
        },
        set_calificacion(categorie, total){
            return categorie == 'Speaking' ? (total/100):total;
        }
    }
}
</script>

<style>

</style>