<template>
    <div>
        <b-row>
            <b-col><p v-html="question.question"></p></b-col>
            <b-col>
                {{ question.answers[0].answer }}
            </b-col>
        </b-row>
    </div>
</template>

<script>
export default {
    props: ['question', 'answers'],
    created: function () {
        this.answers.push(this.question.answers[0].answer);
        console.log(this.shuffleArray(this.answers));

    },
    data() {
        return {
            
        }
    },
    methods: {
        shuffleArray(inputArray) {
            inputArray.sort(() => Math.random() - 0.5);
        }
    }
}
</script>

<style>

</style>