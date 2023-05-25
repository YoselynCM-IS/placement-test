<template>
    <div>
        <label v-html="part_1()"></label>
        <b-form-select class="form-sel" required v-model="answer_id" 
            :options="set_select()">
        </b-form-select>
        <label v-html="part_2()"></label>
    </div>
</template>

<script>
export default {
    props: ['question'],
    data: function(){
        return {
            answer_id: null
        }
    },
    methods: {
        part_1(){
            this.index = this.question.question.indexOf("@@@@");
            return this.question.question.substr(0, this.index);
        },
        part_2(){
            let long = this.question.question.length;
            return this.question.question.substr(this.index + 4, long);
        },
        set_select(){
            let answers = this.question.answers;
            let options = [];

            options.push({
                value: null, text: 'Select an answer', disabled: true
            });
            answers.forEach(answer => {
                if(answer.value == 'correct') this.answer_id = answer.id;
                options.push({ value: answer.id, text: answer.answer });
            });

            return options;
        }
    },
}
</script>

<style>
    .form-sel {
        width: 150px;
    }
</style>