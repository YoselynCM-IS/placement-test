<template>
    <div>
        <p v-html="question.question"></p>
        <b-form-radio-group v-model="answer_id" :options="set_options()"></b-form-radio-group>
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
        set_options(){
            let answers = this.question.answers;
            let options = [];

            answers.forEach(answer => {
                if(answer.value == 'correct') this.answer_id = answer.id;
                options.push({ value: answer.id, text: answer.answer });
            });

            return options;
        }
    }
}
</script>

<style>

</style>