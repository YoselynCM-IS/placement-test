<template>
    <div>
        <div v-if="!busy">
            <b-form @submit.prevent="onSubmit">
                <b-form-group label="Nombre">
                    <b-form-input v-model="exam.name" required 
                        :disabled="load" style="text-transform:uppercase;"
                    ></b-form-input>
                </b-form-group>
                <b-form-group v-if="groups.length > 0" label="Grupo">
                    <b-form-select v-model="exam.group_id" required 
                        :disabled="load" :options="groups"
                    ></b-form-select>
                </b-form-group>
                <b-row v-if="groups.length > 0">
                    <b-col>
                        <b-form-group label="Fecha de aplicación">
                            <b-form-datepicker v-model="exam.start_date" required 
                                :disabled="load">
                            </b-form-datepicker>
                        </b-form-group>
                    </b-col>
                    <b-col></b-col>
                </b-row>
                <b-row v-if="groups.length > 0">
                    <b-col>
                        <b-form-group label="Hora de inicio">
                            <b-form-timepicker v-model="exam.start_time" required
                                :disabled="load" locale="en">
                            </b-form-timepicker>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Hora de termino">
                            <b-form-timepicker v-model="exam.end_time" required
                                :disabled="load" locale="en">
                            </b-form-timepicker>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Duración (minutos)">
                            <b-form-input v-model="exam.duration" required 
                                :disabled="load" type="number"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <b-form-group label="Margen de error por nivel">
                            <b-form-input v-model="exam.error_range" required 
                                :disabled="load" type="number"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col></b-col>
                </b-row>
                <b-form-group label="Indicaciones">
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
export default {
    props: [ 'exam', 'edit' ],
    data(){
        return {
            load: false,
            groups: [],
            busy: false
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
        onSubmit(){
            this.load = true;
            if(!this.edit) {
                axios.post('/exams/create', this.exam).then(response => {
                    this.load = false;
                    this.$emit('exam_created', response.data);
                }).catch(error => {
                    this.load = false;
                    swal("Error", "Ocurrió un error al crear el exámen. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
                });
            } else {
                axios.put('/exams/update', this.exam).then(response => {
                    this.load = false;
                    this.$emit('exam_created', response.data);
                }).catch(error => {
                    this.load = false;
                    swal("Error", "Ocurrió un error al crear el exámen. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
                });
            }
        },
    }
}
</script>

<style>

</style>