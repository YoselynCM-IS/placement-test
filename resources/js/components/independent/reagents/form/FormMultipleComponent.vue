<template>
    <div>
        <p v-html="question.question"></p>
        <b-form-radio-group v-model="question.answer" :options="set_options()"
            required></b-form-radio-group>
    </div>
</template>

<script>
export default {
    props: ['question'],
    data: function(){
        return {
            
        }
    },
    methods: {
        set_options(){
            let answers = this.question.answers;
            let options = [];

            answers.forEach(answer => {
                options.push({ value: answer.answer, text: answer.answer });
            });

            return options;
        }
    }
}
</script>

<style>

</style>