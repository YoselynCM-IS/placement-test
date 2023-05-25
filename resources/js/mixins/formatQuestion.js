export default {
    data(){
        return {
            
        }
    },
    methods: {
        // ESTRUCTURA DE LA RESPUESTA
        set_answer(question) {
            if(question.question.includes('@@@@')){
                let a = '_____';
                if(question.answers){
                    question.answers.forEach(answer => {
                        if(answer.value == 'correct' && answer.answer.length > 0)
                            a = answer.answer;
                    });
                }
                return question.question.replace('@@@@', `<strong><u>${a}</u></strong>`);
            }
            return question.question;
        },
    }
}