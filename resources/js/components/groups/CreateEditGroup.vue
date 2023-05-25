<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group label="Nombre del grupo">
                <b-form-input v-model="form.group" :disabled="load"
                    required style="text-transform:uppercase;"
                ></b-form-input>
                <div v-if="errors && errors.group" class="text-danger">
                    El nombre del grupo tiene que ser igual o mayor a 2 caracteres.
                </div>
            </b-form-group>
            <b-button pill block type="submit" :disabled="load"
                class="mt-2" variant="success">
                <b-icon-check-circle></b-icon-check-circle> Guardar
            </b-button>
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
        // GUARDAR GRUPO
        onSubmit(){
            this.load = true;
            if(!this.edit){
                axios.post('/groups/create', this.form).then(response => {
                    this.then_results(response);
                }).catch(error => { 
                    this.catch_error(error);
                });
            } else {
                axios.put('/groups/update', this.form).then(response => {
                    this.then_results(response);
                }).catch(error => { 
                    this.catch_error(error);
                });
            }
        },
        // THEN RESULTS
        then_results(response){
            this.load = false;
            this.errors = {};
            this.$emit('group_created', response.data);
        },
        // CATH ERRORS
        catch_error(error){
            this.load = false;
            if(error.response.status === 422) {
                this.errors = error.response.data.errors || {};
            } else {
                swal("Error", "Ocurrió un error al guardar el grupo. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
            }
        }
    }
}
</script>

<style>

</style>