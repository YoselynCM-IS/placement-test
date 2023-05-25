<template>
    <div>
        <b-row v-if="role.role == 'admin'" class="mb-2">
            <b-col sm="3">
                <b-button pill id="btn-actions" @click="edit_mode = !edit_mode">
                    <b-icon-pencil v-if="!edit_mode"></b-icon-pencil> 
                    <b-icon-x v-else></b-icon-x>
                    {{ !edit_mode ? ' Modo edición':' Cerrar edición' }}
                </b-button>
            </b-col>
            <b-col sm="6"></b-col>
            <b-col sm="3" class="text-right">
                <b-button v-if="edit_mode" pill id="btn-actions" @click="add_level()">
                    <b-icon-plus></b-icon-plus> Agregar nivel
                </b-button>
            </b-col>
        </b-row>
        <b-card no-body>
            <b-tabs pills card vertical>
                <b-tab title="Home"></b-tab>
                <b-tab v-for="(level, i) in levels" v-bind:key="i" :title="level.level">
                    <template #title>
                        <b-row>
                            <b-col @click="get_topics(level.id)">
                                <a >{{ level.level }}</a>
                            </b-col>
                            <b-col sm="1">
                                <b-button id="btn-actions" size="sm" pill 
                                    :disabled="load" @click="get_topics(level.id)">
                                    <b-icon-arrow-right-short></b-icon-arrow-right-short>
                                </b-button>
                            </b-col>
                        </b-row>
                    </template>
                    <div v-if="!viewContent">
                        <b-row v-if="edit_mode" class="mb-2">
                            <b-col>
                                <b-button id="btn-ner" size="sm" pill :disabled="load"
                                    @click="edit_level(level, i)">
                                    <b-icon-pencil></b-icon-pencil>
                                </b-button>
                                <b-button v-if="topics.length == 0" id="btn-ner" size="sm" pill
                                    @click="delete_level(level.id, i)" :disabled="load">
                                    <b-icon-x></b-icon-x>
                                </b-button>
                            </b-col>
                            <b-col sm="10" class="text-right">
                                <b-button pill id="btn-ner" @click="add_topic(level.id)">
                                    <b-icon-plus></b-icon-plus> Agregar tema
                                </b-button>
                            </b-col>
                        </b-row>
                        <div v-if="!load">
                            <b-row v-if="topics.length > 0">
                                <b-col sm="3" v-for="(topic, i) in topics" v-bind:key="i"
                                    class="mb-3">
                                    <b-button id="btn-topic" @click="get_content(topic.id)">
                                        {{ topic.topic }}
                                    </b-button>
                                    <div v-if="edit_mode" class="text-right">
                                        <b-button pill id="btn-ner" @click="edit_topic(topic, i)" size="sm">
                                            <b-icon-pencil></b-icon-pencil>
                                        </b-button>
                                        <b-button pill id="btn-ner" @click="delete_topic(topic.id, i)" size="sm">
                                            <b-icon-x></b-icon-x>
                                        </b-button>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-alert v-else show variant="secondary">
                                No se han agregado temas
                            </b-alert>
                        </div>
                        <div v-else class="text-center text-info my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Cargando...</strong>
                        </div>
                    </div>
                    <div v-else>
                        <b-row class="mb-3">
                            <b-col sm="1">
                                <b-button id="btn-actions" size="sm" pill v-on:click="viewContent = false">
                                    <b-icon-arrow-left-circle></b-icon-arrow-left-circle>
                                </b-button>
                            </b-col>
                            <b-col sm="8">
                                <h5><b>{{ topic.topic }}</b></h5>
                            </b-col>
                            <b-col sm="3" class="text-right">
                                <b-button v-if="edit_mode" id="btn-actions" pill
                                    @click="add_instruction(topic.id)">
                                    <b-icon-plus></b-icon-plus> Agregar instrucción
                                </b-button>
                            </b-col>
                        </b-row>
                        <div v-if="!load">
                            <div v-if="topic.instructions.length > 0">
                                <b-card v-for="(instruction, i) in topic.instructions" v-bind:key="i" 
                                    class="mb-2" header-text-variant="black">
                                    <b-card-header>
                                        <b-row>
                                            <b-col>
                                                <b-row>
                                                    <b-col sm="1"><strong>{{ i + 1 | toRoman }}. </strong></b-col>
                                                    <b-col>
                                                        <label v-html="instruction.instruction"></label>
                                                    </b-col>
                                                </b-row>
                                            </b-col>
                                            <b-col sm="3" class="text-right">
                                                <div v-if="edit_mode">
                                                    <b-button pill size="sm" id="btn-actions"
                                                        @click="move_instruction(instruction, i)">
                                                        <b-icon-arrows-move></b-icon-arrows-move>
                                                    </b-button>
                                                    <b-button pill size="sm" id="btn-actions"
                                                        @click="edit_instruction(instruction, i)">
                                                        <b-icon-pencil></b-icon-pencil>
                                                    </b-button>
                                                    <b-button pill size="sm" id="btn-actions"
                                                        @click="delete_instruction(instruction, i)">
                                                        <b-icon-x></b-icon-x>
                                                    </b-button>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-card-header>
                                    <div v-if="edit_mode" class="text-right mt-2 mb-2">
                                        <b-button pill id="btn-actions" @click="add_questions(instruction.id, i)">
                                            <b-icon-plus></b-icon-plus> Agregar pregunta
                                        </b-button>
                                    </div>
                                    <track-component v-if="instruction.categorie_id == 2"
                                            :link="instruction.link" :text="'Track'"></track-component>
                                    <b-table v-if="instruction.questions.length > 0" :items="instruction.questions" 
                                        :fields="fieldsQuestions">
                                        <template v-slot:cell(index)="data">
                                            {{ data.index + 1 }}
                                        </template>
                                        <template v-slot:cell(question)="data">
                                            <p v-html="set_answer(data.item)"></p>
                                            <ul v-if="data.item.type_id == 1 && !data.item.question.includes('@@@@')">
                                                <li v-for="(answer, j) in data.item.answers" v-bind:key="j" 
                                                    v-html="answer.answer">
                                                </li>
                                            </ul>
                                            <div v-if="data.item.type_id == 2 || data.item.type_id == 3">
                                                <ul>
                                                    <li v-for="(answer, j) in data.item.answers" v-bind:key="j">
                                                        <b-row>
                                                            <b-col>
                                                                <b-badge v-if="answer.value === 'correct'" pill variant="success">
                                                                    <b-icon-check2></b-icon-check2>
                                                                </b-badge>
                                                                <b-badge v-else variant="danger" pill>
                                                                    <b-icon-x></b-icon-x>
                                                                </b-badge>
                                                                {{ answer.answer }}
                                                            </b-col>
                                                            <!-- <b-col sm="3" class="text-right">
                                                                <div v-if="edit_mode">
                                                                    <b-button id="btn-actions" pill size="sm"
                                                                        @click="edit_answer(answer, i, data.index, j)">
                                                                        <b-icon-pencil></b-icon-pencil>
                                                                    </b-button>
                                                                    <b-button id="btn-actions" pill size="sm">
                                                                        <b-icon-x></b-icon-x>
                                                                    </b-button>
                                                                </div>
                                                            </b-col> -->
                                                        </b-row>
                                                    </li>
                                                </ul>
                                            </div>
                                        </template>
                                        <template v-slot:cell(answer)="data" >
                                            <div v-if="data.item.type_id == 4">
                                                <p v-for="(answer, j) in data.item.answers" v-bind:key="j" 
                                                    v-html="answer.answer">
                                                </p>
                                            </div>
                                            <div v-if="instruction.categorie_id == 3 && data.item.type_id == 1">
                                                <b-badge variant="dark">Recording</b-badge>
                                            </div>
                                        </template>
                                        <template v-slot:cell(actions)="data">
                                            <div v-if="edit_mode">
                                                <b-button pill id="btn-actions" size="sm"
                                                    @click="edit_question(data.item, data.index, i)">
                                                    <b-icon-pencil></b-icon-pencil>
                                                </b-button>
                                                <b-button pill id="btn-actions" size="sm"
                                                    @click="delete_question(data.item, data.index, i)">
                                                    <b-icon-x></b-icon-x>
                                                </b-button>
                                                <b-button pill id="btn-actions" size="sm"
                                                    @click="add_answers(data.item, instruction.topic_id)">
                                                    <b-icon-plus></b-icon-plus>
                                                </b-button>
                                                <b-button pill id="btn-actions" size="sm"
                                                    @click="edit_answers(data.item, instruction.topic_id)">
                                                    <b-icon-list></b-icon-list>
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-table>
                                    <b-alert v-else show variant="secondary">
                                        No se han agregado preguntas
                                    </b-alert>
                                </b-card>
                            </div>
                            <b-alert v-else show variant="secondary">
                                No se han agregado instrucciones
                            </b-alert>
                        </div>
                        <div v-else class="text-center text-info my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Cargando...</strong>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </b-card>

        <!-- MODALS -->
        <b-modal :title="`${!editLevel ? 'Add':'Edit'} level`" hide-footer hide-backdrop v-model="openAddL">
            <new-edit-level-component :form="form_level" :edit="editLevel"
                @level_saved="level_saved">
            </new-edit-level-component>
        </b-modal>
        <b-modal :title="`${!editTopic ? 'Add':'Edit'} topic`" hide-footer hide-backdrop v-model="openAddT">
            <new-edit-topic-component :edit="editTopic" :form="form_topic"
                @topic_saved="topic_saved">
            </new-edit-topic-component>
        </b-modal>
        <b-modal ref="modal-delete_topic" hide-footer size="sm" centered 
            class="text-center" title="Eliminar tema">
            <p>¿Estás seguro de eliminar el tema?</p>
            <b-button pill block variant="danger" @click="confirm_delete_topic()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar tema
            </b-button>
        </b-modal>
        <b-modal :title="`${!editInstruction ? 'Add':'Edit'} instruction`" hide-footer hide-backdrop v-model="openAddI">
            <new-edit-instruction-component :edit="editInstruction" :form="form_instruction"
                @instruction_saved="instruction_saved">
            </new-edit-instruction-component>
        </b-modal>
        <b-modal ref="modal-delete_instruction" hide-footer size="sm" centered 
            class="text-center" title="Eliminar instrucción">
            <p>¿Estás seguro de eliminar la instrucción?</p>
            <b-button pill block variant="danger" @click="confirm_delete_instruction()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar instrucción
            </b-button>
        </b-modal>
        <b-modal :title="`${!editQuestion ? 'Add':'Edit'} question`" hide-footer hide-backdrop v-model="openAddQ">
            <new-edit-question-component 
                :form="form_question" :edit="editQuestion"
                @question_saved="question_saved">
            </new-edit-question-component>
        </b-modal>
        <b-modal :title="`${!editAnswers ? 'Add':'Edit'} responses`" hide-footer hide-backdrop v-model="openAddA">
            <new-edit-answers-component :edit="editAnswers"
                :question="question" @updateAnswers="updateAnswers">
            </new-edit-answers-component>
        </b-modal>
        <b-modal ref="modal-delete_question" hide-footer size="sm" centered 
            class="text-center" title="Eliminar pregunta">
            <p>¿Estás seguro de eliminar la pregunta?</p>
            <b-button pill block variant="danger" @click="confirm_delete_question()">
                <b-icon-x-circle></b-icon-x-circle> Eliminar pregunta
            </b-button>
        </b-modal>
        <b-modal title="Mover instrucción" hide-footer hide-backdrop v-model="openMoveI">
            <move-instruction-component @instruction_moved="instruction_moved"
                :form="up_instruction" :level_id="topic.level_id">
            </move-instruction-component>
        </b-modal>
    </div>
</template>

<script>
import formatQuestion from '../../mixins/formatQuestion';
export default {
    props: ['registers', 'role'],
    mixins: [formatQuestion],
    data(){
        return {
            levels: this.registers,
            topics: [],
            viewContent: false,
            topic: {},
            fieldsQuestions: [
                {key: 'index', label: ''},
                {key: 'question', label: 'Question'},
                {key: 'answer', label: ''},
                {key: 'actions', label: ''}
            ],
            question: { 
                id: null, type_id: null, 
                question: null, topic_id: null,
                answer: null, answers: []
            },
            form_level: { id: null, level: null },
            form_topic: { id: null, level_id: null, topic: null },
            form_instruction: { id: null, topic_id: null, instruction: null, categorie_id: null, link: null },
            form_question: { id: null, instruction_id: null, type_id: null, question: null, answer: null },
            edit_mode: false,
            editLevel: false,
            editTopic: false,
            editInstruction: false,
            editQuestion: false,
            openAddL: false,
            openAddT: false,
            openAddI: false,
            openAddQ: false,
            openAddA: false,
            position: null,
            pos_question: null,
            topic_id: null,
            instruction_id: null,
            question_id: null,
            load: false,
            up_instruction: { instruction_id: null, topic_id: null },
            openMoveI: false,
            editAnswers: false
        }
    },
    filters: {
        toRoman(number){
            let letra;
            switch (number){
                case 1:
                    letra="I";
                    break;
                case 2:
                    letra="II";
                    break;
                case 3:
                    letra="III";
                    break;
                case 4:
                    letra="IV";
                    break;
                case 5:
                    letra="V";
                    break;
                case 6:
                    letra="VI";
                    break;
                case 7:
                    letra="VII";
                    break;
                case 8:
                    letra="VIII";
                    break;
                case 9:
                    letra="IX";
                    break;
                case 10:
                    letra="X";
                    break;
            }
            return letra;
        }
    },
    methods: {
        // OBTENER TOPICS
        get_topics(level_id){
            this.topics = [];
            this.viewContent = false;
            this.load = true;
            axios.get('/topics/by_level', {params: {level_id: level_id}}).then(response => {
                this.topics = response.data;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // OBTENER CONTENIDO DE TOPIC
        get_content(id){
            this.load = true;
            axios.get('/topics/show', {params: {topic_id: id}}).then(response => {
                this.topic = response.data;
                this.viewContent = true;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        /** LEVELS */
        // AGREGAR NIVEL
        add_level(){
            this.form_level = { id: null, level: null };
            this.editLevel = false;
            this.openAddL = true;
        },
        // EDITAR NIVEL
        edit_level(level, pos){
            this.form_level = { id: level.id, level: level.level };
            this.position = pos;
            this.editLevel = true;
            this.openAddL = true;
        },
        // NIVEL GUARDADO
        level_saved(level){
            this.openAddL = false;
            if(!this.editLevel){
                this.levels.push(level);
                swal("OK", "El nivel se guardo correctamente.", "success");
            } else {
                this.levels[this.position].level = level.level;
                this.position = null;
                swal("OK", "El nivel se actualizo correctamente.", "success");
            }
        },
        // ELIMINAR NIVEL
        delete_level(level_id, pos){
            this.load = true;
            axios.delete('/levels/delete', {params: {level_id: level_id}}).then(response => {
                this.levels.splice(pos, 1);
                swal("OK", "El nivel se elimino correctamente.", "success");
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        /** LEVELS */
        /** TOPICS */
        // AGREGAR TOPIC
        add_topic(level_id){
            this.editTopic = false;
            this.openAddT = true;
            this.form_topic = { id: null, level_id: level_id, topic: null };
        },
        // EDITAR TOPIC
        edit_topic(topic, pos){
            this.editTopic = true;
            this.openAddT = true;
            this.form_topic.id = topic.id;
            this.form_topic.topic = topic.topic;
            this.position = pos;
        },
        // TOPIC GUARDADO
        topic_saved(topic){
            this.openAddT = false;
            if(!this.editTopic){
                this.topics.unshift(topic);
                swal("OK", "El tema se guardo correctamente.", "success");
            } else {
                this.topics[this.position].topic = topic.topic;
                this.position = null;
                swal("OK", "El tema se actualizo correctamente.", "success");
            }
        },
        // ELIMINAR TOPIC, VERIFICAR QUE NO TENGA INSTRUCCIONES
        delete_topic(topic_id, pos){
            axios.get('/topics/instruction_assignments', {params: {topic_id: topic_id}}).then(response => {
                if(response.data.instructions_count == 0){
                    this.topic_id = topic_id;
                    this.position = pos;
                    this.$refs['modal-delete_topic'].show();
                } else {
                    swal("", "El tema no puede ser eliminado ya que tiene contenido creado.", "warning");
                }
            }).catch(error => { });
        },
        // CONFIRMAR ELIMINACIÓN DE TEMA
        confirm_delete_topic(){
            this.load = true;
            axios.delete('/topics/delete', {params: {topic_id: this.topic_id}}).then(response => {
                this.$refs['modal-delete_topic'].hide();
                swal("OK", "El tema ha sido eliminado correctamente.", "success");
                this.topics.splice(this.position, 1);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        /** TOPICS */
        /** INSTRUCTIONS */
        // AGREGAR INSTRUCTION
        add_instruction(topic_id){
            this.form_instruction = { 
                id: null, topic_id: topic_id, instruction: null, 
                categorie_id: null, link: null
            };
            this.editInstruction = false;
            this.openAddI = true;
        },
        // EDITAR INSTRUCCIÓN
        edit_instruction(instruction, pos){
            this.position = pos;
            this.form_instruction = { 
                id: instruction.id, 
                topic_id: instruction.topic_id, 
                instruction: instruction.instruction,
                categorie_id: instruction.categorie_id,
                link: instruction.link
            };
            this.editInstruction = true;
            this.openAddI = true;
        },
        // INSTRUCCION GUARDADA
        instruction_saved(instruction){
            this.openAddI = false;
            if(!this.editInstruction){
                let i = { 
                    id: instruction.id, 
                    instruction: instruction.instruction, 
                    topic_id: instruction.topic_id, 
                    categorie_id: instruction.categorie_id,
                    link: instruction.link,
                    questions: [] 
                };
                this.topic.instructions.unshift(i);
                swal("OK", "La instrucción se guardo correctamente.", "success");
            } else {
                this.topic.instructions[this.position].instruction = instruction.instruction;
                this.position = null;
                swal("OK", "La instrucción se actualizo correctamente.", "success");
            }
        },
        // ELIMINAR INSTRUCCIÓN
        delete_instruction(instruction, pos){
            if(instruction.questions && instruction.questions.length) {
                swal("", "La instrucción no puede ser eliminada ya que tiene preguntas creadas.", "warning");
            } else{
                this.instruction_id = instruction.id;
                this.position = pos;
                this.$refs['modal-delete_instruction'].show();
            }
        },
        // CONFIRMAR ELIMINACIÓN DE INSTRUCCIÓN
        confirm_delete_instruction(){
            this.load = true;
            axios.delete('/instructions/delete', {params: {instruction_id: this.instruction_id}}).then(response => {
                this.$refs['modal-delete_instruction'].hide();
                swal("OK", "La instrucción ha sido eliminada correctamente.", "success");
                this.topic.instructions.splice(this.position, 1);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // MOVER INSTRUCCIÓN
        move_instruction(instruction, pos){
            this.position = pos;
            this.up_instruction.instruction_id = instruction.id;
            this.openMoveI = true;
        },
        // INSTRUCCIÓN MOVIDA
        instruction_moved(instruction){
            this.topic.instructions.splice(this.position,1);
            this.position = null;
            this.up_instruction = {};
            this.openMoveI = false;
            swal("OK", "La instrucción se movio correctamente.", "success");
        },
        /** INSTRUCTIONS */
        /** QUESTIONS */
        // AGREGAR PREGUNTA
        add_questions(instruction_id, pos){
            this.form_question = { 
                id: null, 
                instruction_id: instruction_id, 
                type_id: null,
                question: null, 
                answer: null
            };
            this.position = pos;
            this.editQuestion = false;
            this.openAddQ = true;
        },
        // EDITAR QUESTION
        edit_question(question, pos_q, pos_i){
            this.form_question = { 
                id: question.id,
                instruction_id: question.instruction_id, 
                type_id: question.type_id,
                question: question.question, 
                answer: null
            };
            this.editQuestion = true;
            this.position = pos_i;
            this.pos_question = pos_q;
            this.openAddQ = true;
        },
        // QUESTION ACTUALIZADA
        question_saved(question){
            let a_questions = this.topic.instructions[this.position]['questions'];
            if(!this.editQuestion){
                a_questions.push(question);
                swal("OK", "La pregunta se guardo correctamente.", "success");
            } else{
                a_questions[this.pos_question]['type_id'] = question.type_id;
                a_questions[this.pos_question]['question'] = question.question;
                swal("OK", "La pregunta se actualizo correctamente.", "success");
            }
            this.position = null;
            this.pos_question = null;
            this.openAddQ = false;
        },
        // ELIMINAR PREGUNTA
        delete_question(question, pos_q, pos_i){
            if(question.answers && question.answers.length) {
                swal("", "La pregunta no puede ser eliminada ya que tiene respuestas creadas.", "warning");
            } else{
                this.question_id = question.id;
                this.position = pos_i;
                this.pos_question = pos_q; 
                this.$refs['modal-delete_question'].show();
            } 
        },
        // CONFIRMAR ELIMINACIÓN DE QUESTION
        confirm_delete_question(){
            this.load = true;
            axios.delete('/questions/delete', {params: {question_id: this.question_id}}).then(response => {
                this.$refs['modal-delete_question'].hide();
                swal("OK", "La pregunta ha sido eliminada correctamente.", "success");
                this.topic.instructions[this.position]['questions'].splice(this.pos_question, 1);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        /** QUESTIONS */
        /** ANSWERS */
        // AGREGAR ANSWERS
        add_answers(question, topic_id){
            let as = [
                { answer: '', value: 1 },
                { answer: '', value: 2 },
                { answer: '', value: 3 },
                { answer: '', value: 4 }
            ];
            this.values_question(topic_id, question, null, as);
            this.editAnswers = false;
            this.openAddA = true;
        },
        // VALORES DE LA PREGUNTA, PARA PODER EDITAR
        values_question(topic_id, question, answer, answers){
            this.question = { 
                id: question.id, 
                type_id: question.type_id, 
                question: question.question,
                topic_id: topic_id,
                answer: answer, answers: answers
            };
        },
        // EDITAR RESPUESTAS
        edit_answers(question, topic_id){
            let as = [];
            let count = 1;
            question.answers.forEach(a => {
                as.push({
                    id: a.id,
                    question_id: a.question_id,
                    answer: a.answer,
                    value: count++,
                });
            });

            var answer = null;
            if(question.type_id == 1 || question.type_id == 4) 
                answer = question.answers[0].answer;

            this.values_question(topic_id, question, answer, as);
            this.editAnswers = true;
            this.openAddA = true;
        },
        // RESPUESTAS GUARDADAS
        updateAnswers(topic){
            this.topic = topic;
            this.openAddA = false;
        },
        /** ANSWERS */
    }
}
</script>