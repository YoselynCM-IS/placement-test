<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group label="Escuela">
                <b-form-input v-model="sSchool" @keyup="show_schools()"
                    style="text-transform:uppercase;" required
                ></b-form-input>
                <div class="list-group" v-if="schools.length" id="list-drop-down">
                    <a 
                        href="#" class="list-group-item list-group-item-action" 
                        v-for="(school, i) in schools" v-bind:key="i" 
                        @click="select_school(school)">
                        {{ school.school }}
                    </a>
                </div>
            </b-form-group>
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
import searchSchool from '../../../../mixins/searchSchool';
export default {
    props: ['form', 'edit'],
    mixins: [searchSchool],
    data(){
        return {
            load: false,
            errors: {}
        }
    },
    created: function(){
        this.sSchool = this.form.school_name;
    },
    methods: {
        // ELEGIR ESCUELA
        select_school(school){
            this.form.school_id = school.id;
            this.sSchool = school.school;
            this.schools = [];
        },
        // GUARDAR PROFESOR
        onSubmit(){
            this.load = true;
            if(!this.edit){
                axios.post('/administrator/save_teacher', this.form).then(response => {
                    this.then_results(response);
                }).catch(error => { 
                    this.catch_error(error) 
                });
            } else {
                axios.put('/administrator/update_teacher', this.form).then(response => {
                    this.then_results(response);
                }).catch(error => { 
                    this.catch_error(error) 
                });
            }
        },
        // THEN RESULTS
        then_results(response){
            this.load = false;
            this.errors = {};
            this.$emit('teacher_created', response.data);
        },
        // CATH ERRORS
        catch_error(error){
            this.load = false;
            if(error.response.status === 422) {
                this.errors = error.response.data.errors || {};
            } else {
                swal("Error", "Ocurrió un error al guardar al profesor. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
            }
        }
    }
}
</script>

<style scoped>
    #list-drop-down{
        position: absolute;
        z-index: 100;
    }
</style>