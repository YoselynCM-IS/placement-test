<template>
    <div>
        <div v-if="!busy">
            <b-form @submit.prevent="onSubmit">
                <b-form-group label="Nombre">
                    <b-form-input v-model="exam.name" required 
                        :disabled="load" style="text-transform:uppercase;"
                    ></b-form-input>
                    <list-errors :errors="errors" :campo="errors.name"></list-errors>
                </b-form-group>
                <b-form-group label="Grupo">
                    <b-form-select v-model="exam.group_id" required 
                        :disabled="load" :options="groups"
                    ></b-form-select>
                    <list-errors :errors="errors" :campo="errors.group_id"></list-errors>
                </b-form-group>
                <b-row>
                    <b-col>
                        <b-form-group label="Fecha de aplicación">
                            <b-form-datepicker v-model="exam.start_date" required 
                                :disabled="load">
                            </b-form-datepicker>
                            <list-errors :errors="errors" :campo="errors.start_date"></list-errors>
                        </b-form-group>
                    </b-col>
                    <b-col></b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <b-form-group label="Hora de inicio">
                            <b-form-timepicker v-model="exam.start_time" required
                                :disabled="load" locale="en">
                            </b-form-timepicker>
                            <list-errors :errors="errors" :campo="errors.start_time"></list-errors>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Hora de termino">
                            <b-form-timepicker v-model="exam.end_time" required
                                :disabled="load" locale="en">
                            </b-form-timepicker>
                            <list-errors :errors="errors" :campo="errors.end_time"></list-errors>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Duración (minutos)">
                            <b-form-input v-model="exam.duration" required 
                                :disabled="load" type="number"
                            ></b-form-input>
                            <list-errors :errors="errors" :campo="errors.duration"></list-errors>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="4">
                        <b-form-group label="Margen de error (por nivel)">
                            <b-form-input v-model="exam.error_range" required 
                                :disabled="load" type="number"
                            ></b-form-input>
                            <list-errors :errors="errors" :campo="errors.error_range"></list-errors>
                        </b-form-group>
                    </b-col>
                    <b-col></b-col>
                </b-row>
                <b-form-group label="Indicaciones">
                    <list-errors :errors="errors" :campo="errors.indications"></list-errors>
                    <vue-editor v-model="exam.indications"></vue-editor>
                </b-form-group>
                <b-row class="mt-2">
                    <b-col class="text-center">
                        <b-alert v-if="load" show variant="info">
                            <b-spinner variant="primary"></b-spinner> 
                                Cargando..
                        </b-alert>
                    </b-col>
                    <b-col sm="3" class="text-right">
                        <b-button pill block type="submit" :disabled="load"
                            variant="success">
                            <b-icon-check-circle></b-icon-check-circle> Guardar
                        </b-button>
                    </b-col>
                </b-row>
            </b-form>
        </div>
        <b-alert v-else show variant="info" class="text-center">
            <b-spinner variant="primary"></b-spinner> 
                Cargando..
        </b-alert>
    </div>
</template>

<script>
import ListErrors from './ListErrors.vue';
export default {
  components: { ListErrors },
    props: [ 'exam', 'edit'],
    data(){
        return {
            load: false,
            groups: [],
            busy: false,
            errors: {}
        }
    },
    created: function (){
        this.groups = [];
        this.busy = true;
        axios.get('/groups/s_by_user').then(response => {
            let gs = response.data;
            if(gs.length > 0){
                this.groups.push({
                    value: null, text: 'Selecciona una opción', disabled: true
                });
                
                gs.forEach(g => {
                    this.groups.push({
                        value: g.id,
                        text: g.group
                    });
                });
            } else {
                this.exam.group_id = null;
            }
            this.busy = false;
        });
    },
    methods: {
        onSubmit() {
            this.load = true;
            if (!this.edit) {
                var ax = axios.post('/exams/store', this.exam)
            } else {
                var ax = axios.put('/exams/update', this.exam);
            }
            ax.then(response => {
                this.load = false;
                this.errors = {};
                // console.log(response.data);
                if (response.data.message)
                    swal("Importante", response.data.message, "warning");
                else
                    this.$emit('exam_created', response.data);
            }).catch(error => {
                if (error.response.status === 422)
                    this.errors = error.response.data.errors || {};
                else 
                    swal("Error", "Ocurrió un error al crear el exámen. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");

                this.load = false;
            });
        },
    }
}
</script>

<style>

</style>