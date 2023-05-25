<template>
    <div>
        <b-form @submit.prevent="save_question()">
            <b-form-group label="Type">
                <b-form-select v-model="form.type_id" 
                    required :options="types" :disabled="load">
                </b-form-select>
            </b-form-group>
            <b-form-group label="Question">
                <vue-editor v-model="form.question" :disabled="load"></vue-editor>
            </b-form-group>
            <div class="text-center">
                <b-button type="submit" pill id="btn-actions" block :disabled="load">
                    <b-icon-check-circle></b-icon-check-circle> Save
                </b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
export default {
    props: ['form', 'edit'],
    data() {
        return {
            types: [],
            load: false
        }
    },
    mounted: function() {
        this.get_types();
    },
    methods: {
        // OBTENER TIPOS DE PREGUNTA
        get_types(){
            this.load = true;
            this.types = [];
            axios.get('/questions/get_types').then(response => {
                let ts = response.data;
                this.types.push({
                    value: null, text: 'Select an option', disabled: true
                });
                ts.forEach(t => {
                    this.types.push({ value: t.id, text: t.type });
                });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        // GUARDAR CAMBIOS DE LA PREGUNTA
        save_question(){
            this.load = true;
            if(!this.edit){
                axios.post('/questions/create', this.form).then(response => {
                    this.$emit('question_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                });
            } else {
                axios.put('/questions/update', this.form).then(response => {
                    this.$emit('question_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                });
            }
        }
    }
}
</script>