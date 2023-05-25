<template>
    <div>
        <b-form @submit.prevent="move_instruction()">
            <b-form-group label="Temas">
                <b-form-select v-model="form.topic_id" :options="topics"
                    required :disabled="load">
                </b-form-select>
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
    props: ['form', 'level_id'],
    data(){
        return {
            load: false,
            topics: []
        }
    },
    created: function(){
        this.load = true;
        axios.get('/topics/by_level', {params: {level_id: this.level_id}}).then(response => {
            let ts = response.data;

            this.topics.push({
                value: null, text: 'Selecciona el tema', disabled: true
            });
            ts.forEach(t => {
                this.topics.push({
                    value: t.id, text: t.topic
                });
            });
            
            this.load = false;
        }).catch(error => {
            // PENDIENTE
            this.load = false;
        });
    },
    methods: {
        move_instruction(){
            this.load = true;
            axios.put('/instructions/move', this.form).then(response => {
                this.load = false;
                this.$emit('instruction_moved', response.data);
            }).catch(error => {
                this.load = false;
                swal("Error", "Ocurrió un error al mover la instrucción. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
            });
        }
    }
}
</script>

<style>

</style>