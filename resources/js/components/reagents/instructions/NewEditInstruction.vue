<template>
    <div>
        <b-form @submit.prevent="save_instruction()">
            <b-form-group label="Type">
                <b-form-select v-model="form.categorie_id" 
                    required :options="categories" @change="typeSelected" 
                    :disabled="load">
                </b-form-select>
            </b-form-group>
            <b-form-group label="Instruction">
                <vue-editor v-model="form.instruction" :disabled="load"></vue-editor>
            </b-form-group>
            <b-form-group v-if="form.categorie_id == 2" :label="`URL ${type_select}`">
                <b-input v-model="form.link" :disabled="load" required></b-input>
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
import { VueEditor } from "vue2-editor";
export default {
    props: ['form', 'edit'],
    data(){
        return {
            load: false,
            categories: [],
            type_select: null
        }
    },
    mounted: function() {
        this.get_categories();
        this.typeSelected();
    },
    methods: {
        // OBTENER TIPOS DE PREGUNTA
        get_categories(){
            this.load = true;
            this.categories = [];
            axios.get('/instructions/get_categories').then(response => {
                let cs = response.data;
                this.categories.push({
                    value: null, text: 'Select an option', disabled: true
                });
                cs.forEach(c => {
                    this.categories.push({ value: c.id, text: c.categorie });
                });
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        save_instruction(){
            this.load = true;
            if(!this.edit){
               axios.post('/instructions/create', this.form).then(response => {
                    this.$emit('instruction_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                }); 
            } else {
                axios.put('/instructions/update', this.form).then(response => {
                    this.$emit('instruction_saved', response.data);
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                });
            }
        },
        typeSelected(){
            if(this.form.categorie_id == 2) this.type_select = 'Audio';
        }
    }
}
</script>

<style>

</style>