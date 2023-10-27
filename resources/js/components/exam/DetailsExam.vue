<template>
    <div>
        <b-row class="mb-2">
            <b-col>
                <h5 class="mb-3"><b>Examen</b> <i>(Vista del profesor)</i></h5>
            </b-col>
            <b-col sm="2">
                <b-button pill id="btn-actions" href="/teacher/exams">
                    <b-icon-arrow-left></b-icon-arrow-left> Mis examenes
                </b-button>
            </b-col>
        </b-row>
        <b-row>
            <!-- DETALLES DEL EXAMEN -->
            <b-col sm="4">
                <information-exam :exam="exam.exam" :show="true"></information-exam>
            </b-col>
            <!-- PREGUNTAS DEL EXAMEN -->
            <b-col>
                <b-card>
                    <label><strong>Preguntas</strong></label>
                    <b-card v-for="(instruction, i) in exam.instructions" v-bind:key="i"
                        border-variant="light" class="mb-2">
                        <template #header>
                            <b-row>
                                <b-col sm="3">
                                    <i>{{ instruction.level.level }}</i>
                                    <!-- <i>{{ filter_level(instruction.pivot.level_id) }}</i> -->
                                </b-col>
                                <b-col>
                                    <b>{{ instruction.topic.topic }}</b>
                                </b-col>
                            </b-row>
                        </template>
                        <p v-html="instruction.instruction"></p>
                        <track-component v-if="instruction.categorie_id == 2"
                            :link="instruction.link" :text="'Track'"></track-component>
                        <div v-for="(question, j) in instruction.questions" v-bind:key="j"
                                class="mb-1">
                            <!-- <div v-if="question.instruction_id == instruction.id"> -->
                                <div v-if="instruction.categorie_id !== 3">
                                    <q-open-component v-if="question.type_id == 1"
                                        :question="question">
                                    </q-open-component>
                                    <q-select-component v-if="question.type_id == 2"
                                        :question="question">
                                    </q-select-component>
                                    <q-multiple-component v-if="question.type_id == 3"
                                        :question="question">
                                    </q-multiple-component>
                                    <!-- <q-match-component v-if="question.type_id == 4"
                                        :question="question" :answers="answers"></q-match-component> -->
                                </div>
                                <div v-else>
                                    <b-row>
                                        <b-col>
                                            <label v-html="question.question"></label>
                                        </b-col>
                                        <b-col sm="3">
                                            <b-badge variant="dark">Recording</b-badge>
                                        </b-col>
                                    </b-row>
                                </div>
                            <!-- </div> -->
                        </div>
                    </b-card>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
// import filterLevel from '../../mixins/filterLevel';
import InformationExam from './InformationExam.vue';
export default {
    components: { InformationExam },
    props: ['exam'],
    // mixins: [filterLevel],
    data(){
        return {
            // answers: []
            
        }
    },
    methods: {
        // set_answer(question){
        //     if(question.question.includes('@@@@')){
        //         let a = '';
        //         question.answers.forEach(answer => {
        //             if(answer.value == 'correct' || answer.answer.length > 0)
        //                 a = answer.answer;
        //         });

        //         if(question.type_id == 1){
        //             let input_1 = `<input placeholder="${a}"></input>`;
        //             return question.question.replace('@@@@', input_1);
        //         }
        //         if(question.type_id == 2){
        //             question.question.replace('@@@@', `<q-select-component></q-select-component>`);
        //             return question.question.replace('@@@@', s);;
        //         }
        //     }
            
        //     return question.question;
            
        // }
    }
}
</script>