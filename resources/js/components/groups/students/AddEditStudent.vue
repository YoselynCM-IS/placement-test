<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group label="Nombre completo">
                <b-form-input v-model="form.name" :disabled="load"
                    required style="text-transform:uppercase;"
                ></b-form-input>
                <div v-if="errors && errors.name" class="text-danger">
                    El nombre tiene que ser igual o mayor a 5 caracteres.
                </div>
            </b-form-group>
            <b-form-group label="Correo electrónico">
                <b-form-input type="email" v-model="form.email"
                    required :disabled="load">
                </b-form-input>
                <div v-if="errors && errors.email" class="text-danger">
                    Verifica que el correo electrónico este bien escrito y/o que otro usuario no este registrado con dicho correo.
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
        // GUARDAR ALUMNO
        onSubmit(){
            this.load = true;
            if(this.edit){
                axios.put('/groups/update_student', this.form).then(response => {
                    this.then_response(response);
                }).catch(error => { 
                    this.catch_error(error);
                });
            } else {
                axios.post('/groups/add_student', this.form).then(response => {
                    this.then_response(response);
                }).catch(error => { 
                    this.catch_error(error);
                });
            }
        },
        then_response(response){
            this.errors = {};
            this.$emit('student_created', response.data);
            this.load = false;
        },
        catch_error(error){
            this.load = false;
            if(error.response.status === 422) {
                this.errors = error.response.data.errors || {};
            } else {
                swal("Error", "Ocurrió un error al guardar al alumno. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
            }
        }
    }

}
</script>

<style>

</style>