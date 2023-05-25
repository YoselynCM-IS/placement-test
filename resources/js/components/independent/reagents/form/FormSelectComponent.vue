<template>
    <div>
        <label v-html="part_1()"></label>
        <b-form-select class="form-sel" required v-model="question.answer" 
            :options="set_select()" :disabled="load">
        </b-form-select>
        <label v-html="part_2()"></label>
    </div>
</template>

<script>
export default {
    props: ['question', 'load'],
    data: function(){
        return {
            
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
                options.push({ value: answer.answer, text: answer.answer });
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