<template>
    <div>
        <h5 v-html="question.question"></h5>
        <div v-if="question.type_id == 1 || question.type_id == 4">
            <!-- OPEN / MATCH -->
            <b-input v-model="question.answer" :disabled="load"></b-input>
        </div>
        <div v-if="question.type_id == 2 || question.type_id == 3">
            <!-- SELECT / MULTIPLE -->
            <div class="text-right mb-2">
                <b-button :disabled="load" pill id="btn-actions" @click="add_space()">
                    <b-icon-plus-circle></b-icon-plus-circle> Add space
                </b-button>
            </div>
            <b-table :items="question.answers" :fields="fieldsAnswers">
                <template v-slot:cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template v-slot:cell(answer)="data">
                    <b-input v-model="data.item.answer" :disabled="load"></b-input>
                </template>
                <template v-slot:cell(value)="data">
                    <b-form-radio v-model="question.answer" name="answer-radios" 
                        :value="data.item.value" :disabled="load">
                    </b-form-radio>
                </template>
            </b-table>
        </div>
        <div class="text-center mt-3">
            <b-button id="btn-actions" block pill @click="save_answers()" :disabled="load">
                <b-icon-check-circle></b-icon-check-circle> Save
            </b-button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['question', 'edit'],
    data() {
        return {
            fieldsAnswers: [
                { key: 'index', label: 'N.' },
                { key: 'answer', label: 'Answer' },
                { key: 'value', label: 'Correct' }
            ],
            load: false
        }
    },
    methods: {
        // AGREGAR ESPACIO PARA RESPUESTA
        add_space(){
            let v = this.question.answers.length + 1;
            this.question.answers.push({ answer: '', value: v });
        },
        // GUARDAR RESPUESTAS
        save_answers() {
            this.load = true;
            let as = null;

            if(!this.edit)
                as = axios.post('/answers/create', this.question);
            else 
                as = axios.put('/answers/update', this.question);

            as.then(response => {
                this.$emit('updateAnswers', response.data);
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>