<template>
    <div>
        <b-tabs content-class="mt-3" align="center">
            <b-tab title="Datos" active>
                <form-datos :exam="exam" :edit="true"
                    @exam_created="exam_created"></form-datos>
            </b-tab>
            <b-tab v-if="step_2 && !step_3 && exam.topics_count == 0" title="Temas">
                <form-topics :exam="exam" :edit="false" 
                    @topics_saved="topics_saved"></form-topics>
            </b-tab>
            <b-tab v-if="(exam.topics_count > 0 && exam.questions_count == 0) || step_3" 
                title="Preguntas">
                <form-questions :exam="exam" @questions_created="questions_created"></form-questions>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
export default {
    props: ['exam', 'topics'],
    data(){
        return {
            step_2: true,
            step_3: false
        }
    },
    methods: {
        // RESULTADO DE EXAMEN EDITADO
        exam_created(exam){
            this.$emit('updated_exam', exam);
        },
        // TEMAS GUARDADOS
        topics_saved(exam){
            this.set_step(false, true);
        },
        // PREGUNTAS GUARDADAS
        questions_created(){
            this.set_step(false, false);
            swal("OK", "El examen se guardo correctamente. Presiona en OK para visualizarlo.", "success")
            .then((value) => {
                location.href = `/teacher/exams`;
            });
        },
        set_step(dos, tres){
            this.step_2 = dos;
            this.step_3 = tres;
        },
    }
}
</script>
