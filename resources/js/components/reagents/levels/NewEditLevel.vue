<template>
    <div>
        <b-form @submit.prevent="save_level()">
            <b-form-group label="Level">
                <b-input v-model="form.level" :disabled="load" required></b-input>
                 <div v-if="errors && errors.level" class="text-danger">
                    El nivel tiene que ser único y tener máximo 20 caracteres.
                </div>
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
            load: false,
            errors: {}
        }
    },
    methods: {
        // GUARDAR NIVEL
        save_level(){
            this.load = true;
            if(!this.edit){
                axios.post('/levels/create', this.form).then(response => {
                    this.$emit('level_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    this.catch_eror(error);
                });
            } else {
                axios.put('/levels/update', this.form).then(response => {
                    this.$emit('level_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    this.catch_eror(error);
                });
            }
        },
        // NIVELES
        catch_eror(error){
            this.load = false;
            if(error.response.status === 422) {
                this.errors = error.response.data.errors || {};
            } else {
                swal("Error", "Ocurrió un error al guardar el nivel. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
            }
        }
    }
}
</script>

<style>

</style>