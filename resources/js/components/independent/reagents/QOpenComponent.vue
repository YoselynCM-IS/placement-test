<template>
    <div>
        <div v-if="!question.question.includes('@@@@')">
            <label v-html="question.question"></label>
            <b-form-input v-model="answer" :placeholder="answer"></b-form-input>
        </div>
        <div v-else class="padre">
            <label class="hijo" v-html="part_1()"></label>
            <b-form-input class="hijo form-inp" v-model="answer" :placeholder="answer"></b-form-input>
            <label class="hijo" v-html="part_2()"></label>
        </div>
    </div>
</template>

<script>
export default {
    props: ['question'],
    data: function(){
        return {
            answer: this.set_answer()
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
        set_answer(){
            let answers = this.question.answers;
            let a = '';

            answers.forEach(answer => {
                if(answer.answer.length > 0) a = answer.answer;
            });

            return a;
        }
    },
}
</script>

<style>
    .padre{
        text-align: left;
    }
    .hijo {
        display: inline-block;
    }
    .form-inp {
        width: 150px;
    }
</style>