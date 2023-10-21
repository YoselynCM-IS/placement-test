<template>
    <div>
        <b-card v-if="!chooseQuestions" class="mb-4">
            <b-row>
                <b-col>
                    <p><strong>Elegir preguntas</strong></p>
                </b-col>
                <b-col sm="3">
                    <b-button pill block :disabled="load"
                            id="btn-actions"  @click="choose_questions()">
                        <b-icon-arrow-right-circle-fill></b-icon-arrow-right-circle-fill> Continuar
                    </b-button>
                </b-col>
            </b-row>
        </b-card>
        <b-tabs v-else pills card vertical>
            <b-tab v-for="(topic, i) in examTopics" v-bind:key="i"
                 @click="position = i">
                <template #title>
                    {{ topic.topic }} <br>
                    <strong><i>{{ topic.level.level }}</i></strong>
                </template>
                <b-card v-for="(instruction, j) in topic.instructions" v-bind:key="j"
                    bg-variant="light" :header-html="instruction.instruction">
                    <b-row>
                        <b-col>
                            <track-component v-if="instruction.categorie_id == 2"
                                :link="instruction.link" :text="'Track'"></track-component>
                        </b-col>
                        <b-col sm="2">
                            <b-button pill size="sm" variant="secondary" :disabled="load"
                                @click="selectQuestions(instruction)">
                                <b-icon-check></b-icon-check>
                            </b-button>
                        </b-col>
                    </b-row>
                    <b-table :items="instruction.questions" :fields="fieldsQuestions"
                        responsive="sm">
                        <!-- <template v-slot:cell(index)="data">
                            {{ data.index + 1 }}
                        </template> -->
                        <template v-slot:cell(question)="data">
                            <p v-html="set_answer(data.item)"></p>
                        </template>
                        <template v-slot:cell(skill)="data">
                            {{ instruction.categorie.categorie }}
                        </template>
                        <template v-slot:cell(selected)="data">
                            <b-button pill size="sm" :variant="data.item.variant"
                                :pressed.sync="data.item.state"
                                @click="selectQuestion(data.item)"
                                :disabled="load || instruction.categorie_id ==  2">
                                <b-icon-check></b-icon-check>
                            </b-button>
                        </template>
                    </b-table>
                </b-card>
            </b-tab>
        </b-tabs>
        <b-row>
            <b-col class="text-center">
                <b-alert v-if="errorQuestions" class="text-center"
                    variant="warning" :show="dismissCountDown" dismissible 
                    @dismissed="dismissCountDown=0" @dismiss-count-down="countDownChanged">
                    {{ dismissMsg }}
                </b-alert>
                <b-alert v-if="load" show variant="info">
                    <b-spinner variant="primary"></b-spinner> 
                        Cargando..
                </b-alert>
            </b-col>
            <b-col sm="3" class="text-right">
                <b-button :disabled="load" variant="success" pill 
                    @click="check_topics()">
                    <b-icon-plus-circle></b-icon-plus-circle> Guardar
                </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import formatQuestion from '../../mixins/formatQuestion';
export default {
    props: ['exam'],
    mixins: [formatQuestion],
    data(){
        return {
            fieldsQuestions: [
                { label: 'Pregunta', key: 'question' },
                { label: 'Skill', key: 'skill' },
                { label: 'Seleccionar', key: 'selected' }
            ],
            selectMode: 'multi',
            selected: [],
            questions: [],
            position: 0,
            list_topics: [],
            chooseQuestions: false,
            load: false,
            form: {
                exam_id: this.exam.id,
                questions: []
            },
            dismissSecs: 5,
            dismissCountDown: 0,
            errorQuestions: false,
            ts: [],
            dismissMsg: '',
            examTopics: []
        }
    },
    methods: {
        choose_questions(){
            this.load = true;
            axios.get('/exams/get_topics', {params: {exam_id: this.exam.id}}).then(response => {
                // this.ts = response.data.topics;
                // this.ts.forEach(topic => {
                //     var dato = { 
                //         level: topic.level.level,
                //         topic_id: topic.id, topic: topic.topic,
                //         instructions: []
                //     };
                    
                //     topic.instructions.forEach(instruction => {
                //         var dato2 = {
                //             id: instruction.id,
                //             instruction: instruction.instruction,
                //             categorie_id: instruction.categorie_id,
                //             link: instruction.link,
                //             questions: []
                //         };
                
                //         instruction.questions.forEach(question => {
                //             let q = {
                //                 id: question.id,
                //                 topic_id: topic.id,
                //                 instruction_id: question.instruction_id,
                //                 level_id: topic.level_id,
                //                 categorie_id: instruction.categorie_id,
                //                 question: question.question,
                //                 type_id: question.type_id,
                //                 skill: instruction.categorie.categorie,
                //                 state: false, variant: 'secondary'
                //             };
                //             dato2.questions.push(q);
                //         });
                //         dato.instructions.push(dato2);
                //     });
                //     this.list_topics.push(dato);
                // });
                this.chooseQuestions = true;
                this.examTopics = response.data;
                this.load = false;
            });
        },
        save_questions(){
            this.load = true;
            axios.post('/exams/save_questions', this.form).then(response => {
                this.$emit('questions_created', response.data);
                this.load = false;
            }).catch(error => {
                this.load = false;
                swal("Error", "Ocurrió un error al crear los exámenes. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
            });
        },
        check_topics(){
            // let length_topics = this.ts.length;
            // var count = 0;
            // this.ts.forEach(t => {
            //     let q = this.questions.find(q => q.topic_id == t.id);
            //     if(q !== undefined) count++;
            // });
            
            // if(count == length_topics){
                this.load = true;
                this.form.questions = this.questions;
                axios.post('/exams/check_skills', this.form).then(response => {
                    if (response.data.message)
                        swal("Importante", response.data.message, "warning");
                    else
                        this.save_questions();
                    
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                    swal("Error", "Ocurrió un error al crear los exámenes. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
                });
            // } else {
            //     this.function_dismiss('Es necesario elegir mínimo una pregunta por tema.');
            // }
        },
        function_dismiss(msg){
            this.dismissMsg = msg;
            this.errorQuestions = true;
            this.dismissCountDown = this.dismissSecs;
        },
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        selectQuestion(question){
            this.set_questions(question);
        },
        set_questions(question){
            if(question.state) {
                question.variant = 'success';
                this.questions.push(question);
            } else {
                this.questions.splice(this.get_idQ(question.id), 1);
                question.variant = 'secondary';
            }
        },
        get_idQ(q_id){
            return this.questions.findIndex(q => q.id == q_id);
        },
        selectQuestions(instruction) {
            instruction.questions.forEach(question => {
                question.state = !question.state;
                this.set_questions(question);
            });
        }
    }
}
</script>

<style>

</style>