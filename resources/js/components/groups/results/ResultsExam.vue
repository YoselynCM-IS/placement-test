<template>
    <div>
        <b-row>
            <b-col sm="3">
                <h5>{{ exam.name }}</h5>
                <b-button variant="dark" block pill class="mt-1"
                    @click="get_results_modal()">
                    Resultados
                </b-button>
                <information-exam :exam="exam" :show="true"></information-exam>
            </b-col>
            <b-col>
                <b-card v-for="(advance, i) in advances" v-bind:key="i">
                    <b-card-title>
                        <b-row>
                            <b-col sm="10">
                                {{ advance.level.level }} - {{ advance.instruction.topic.topic }}
                            </b-col>
                            <b-col sm="2">
                                {{ advance.correct }} / {{ advance.total }}
                            </b-col>
                        </b-row>
                    </b-card-title>
                    <b-card-sub-title class="mb-2">
                        <p v-html="advance.instruction.instruction"></p>
                    </b-card-sub-title>
                    <track-component v-if="advance.instruction.categorie_id == 2"
                            :link="advance.instruction.link" :text="'Track'"></track-component>
                    <b-list-group>
                        <b-list-group-item v-for="(result, j) in advance.results" v-bind:key="j">
                            <div v-if="advance.instruction.categorie_id !== 3">
                                <div v-if="(result.question.type_id == 1 && !result.question.question.includes('@@@@')) 
                                        || result.question.type_id == 3">
                                    <p v-html="result.question.question"></p>
                                    <b-alert show :variant="`${result.value == 'correct' ? 'success':'danger'}`">
                                        {{ result.answer }}
                                    </b-alert>
                                </div>
                                <b-alert show v-if="result.question.type_id == 1 && result.question.question.includes('@@@@') 
                                                    || result.question.type_id == 2"
                                    :variant="`${result.value == 'correct' ? 'success':'danger'}`">
                                    <p v-html="set_answer(result)"></p>
                                </b-alert>
                            </div>
                            <div v-else>
                                <p v-html="result.question.question"></p>
                                <b-row v-if="result.track">
                                    <b-col>
                                        <b-alert show :variant="set_status(result.value)">
                                            <track-component :link="result.track.public_url | filterTrack"
                                                        :text="'Recording'"></track-component>
                                        </b-alert>
                                    </b-col>
                                    <b-col sm="2" class="text-right">
                                        <div v-if="result.value == 'pending'">
                                            <b-button v-if="role_id == 2"
                                                variant="success" pill size="sm" @click="set_points(result.id, i, j)">
                                                <b-icon-pencil></b-icon-pencil> Calificar
                                            </b-button>
                                            <b-badge v-else variant="warning">
                                                Pendiente
                                            </b-badge>
                                        </div>
                                        <b-badge v-if="result.value == 'correct'" variant="light">
                                            Calificación: {{ result.points }}
                                        </b-badge>
                                    </b-col>
                                </b-row>
                                <b-alert v-else show variant="danger">
                                    No se subió grabación.
                                </b-alert>
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
            </b-col>
        </b-row> 
        <b-modal ref="modal-points" size="sm" hide-footer title="Asignar calificación"
                centered hide-backdrop>
            <b-form @submit.prevent="save_points()">
                <b-form-group label="Calificación (0 a 100)">
                    <b-input v-model="form.points" :disabled="load" 
                        autofocus required type="number" min="0" max="100"></b-input>
                </b-form-group>
                <div class="text-center">
                    <b-button type="submit" pill id="btn-actions" block :disabled="load">
                        <b-icon-check-circle></b-icon-check-circle> Save
                    </b-button>
                </div>
            </b-form>
        </b-modal>   
        <b-modal ref="modal-results" hide-footer title="Resultados"
                centered hide-backdrop>
                <details-results :exam="exam" :student_id="student_id"></details-results>
        </b-modal>
    </div>
</template>

<script>
import InformationExam from '../../exam/InformationExam.vue';
export default {
  components: { InformationExam },
    props: ['advances', 'exam', 'role_id', 'student_id'],
    data(){
        return{
            pos_advance: null,
            pos_result: null,
            load: false,
            form: {
                result_id: null,
                points: 0
            },
            levels: []
        }
    },
    filters: {
        filterTrack: function(link){
            let extraer = link.slice(0, -5);
            return extraer.replace('www.dropbox', 'dl.dropboxusercontent');
        }
    },
    methods: {
        set_answer(result) {
            if(result.question.question.includes('@@@@')){
                return result.question.question.replace('@@@@', `<strong><u>${result.answer}</u></strong>`);
            }
            return result.question.question;
        },
        set_status(value){
            if(value == 'correct') return 'success';
            // if(value == 'incorrect') return 'danger';
            if(value == 'pending') return 'warning';
        },
        set_points(result_id, i, j){
            this.pos_advance = i;
            this.pos_result = j;
            this.form.result_id = result_id;
            this.form.points = 0;
            this.$refs['modal-points'].show();
        },
        save_points(){
            this.load = true;
            axios.post('/exams/save_points', this.form).then(response => {
                this.advances[this.pos_advance].correct = response.data.correct;
                this.advances[this.pos_advance].results[this.pos_result].points = response.data.points;
                this.advances[this.pos_advance].results[this.pos_result].value = 'correct';
                this.$refs['modal-points'].hide();
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        get_results_modal(){
            this.$refs['modal-results'].show();
        }
    }
}
</script>

<style>

</style>