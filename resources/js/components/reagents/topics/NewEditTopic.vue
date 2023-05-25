<template>
    <div>
        <b-form @submit.prevent="save_topic()">
            <b-form-group label="Topic">
                <b-input v-model="form.topic" :disabled="load" required></b-input>
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
    data(){
        return {
            load: false
        }
    },
    methods: {
        save_topic(){
            this.load = true;
            if(!this.edit){
                axios.post('/topics/create', this.form).then(response => {
                    this.$emit('topic_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                });
            } else {
                axios.put('/topics/update', this.form).then(response => {
                    this.$emit('topic_saved', response.data);
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

<style>

</style>