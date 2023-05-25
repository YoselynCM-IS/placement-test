<template>
    <div>
        <label>50 alumnos máximo por grupo.</label><hr>
        <b-form @submit="save_file" enctype="multipart/form-data">
            <input 
                :disabled="load" type="file" required id="archivoType"
                class="custom-file" v-on:change="fileChange">
            <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo</label>
            <p>
                <strong>ARCHIVO: </strong>
                {{ file ? file.name : 'No se ha seleccionado' }}
            </p>
            <b-row>
                <b-col>
                    <b-alert v-if="load" show variant="info">
                        No cerrar este recuadro hasta que el archivo termine de cargar.
                    </b-alert>
                </b-col>
                <b-col sm="4">
                    <b-button pill :disabled="load || file == null" class="mt-3" 
                        variant="success" type="submit" block>
                        <b-icon-check v-if="!load"></b-icon-check>
                        <b-spinner v-else type="grow" small></b-spinner>
                        {{ !load ? 'Guardar':'Cargando' }}
                    </b-button>
                </b-col>
            </b-row>
        </b-form>
    </div>
</template>

<script>
export default {
    props: ['group'],
    data(){
        return {
            load: false,
            file: null,
            errorFormat: false
        }
    },
    methods: {
        fileChange(e){
            this.file = e.target.files[0];
        },
        save_file(e){
            e.preventDefault();

            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.xlsx)$/i;

            this.load = true;
            if(allowedExtensions.exec(fileInput.value)){
                this.errorFormat = false;

                let formData = new FormData();
                formData.append('group_id', this.group.id);
                formData.append('file', this.file);

                axios.post('/groups/upload_students', formData, { headers: { 'content-type': 'multipart/form-data' } })
                .then(response => {
                    this.$emit('upload_students', response.data);
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                    let ms = "Revisar que la lista no contenga un alumno(a) ya registradoa(a) y/o que el correo electrónico de cada alumno(a) este escrito correctamente.";
                    swal("Algo a ocurrido...", ms, "warning");
                });
            } else {
                this.errorFormat = true;
                this.load = false;
            }
        }
    }
}
</script>

<style>

</style>