<template>
    <div>
        <div v-if="!busy">
            <b-card v-if="!chooseTopics" class="mb-4">
                <b-row>
                    <b-col>
                        <b-form-group label="Elige las habilidades que tendrá el examen" 
                                v-slot="{ ariaDescribedby }">
                            <b-form-checkbox-group
                                v-model="exam.categories"
                                :options="options"
                                :aria-describedby="ariaDescribedby"
                                name="selcategories"
                            ></b-form-checkbox-group>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-button pill :disabled="load || exam.categories.length == 0" 
                                block id="btn-actions"  @click="choose_topics()">
                            <b-icon-arrow-right-circle-fill></b-icon-arrow-right-circle-fill> Continuar
                        </b-button>
                    </b-col>
                </b-row>
            </b-card>
            <b-row v-else>
                <b-col>
                    <select-topics :levels="levels" @selected_topics="selected_topics"></select-topics>
                </b-col>
                <!-- <b-col v-if="edit && topics.length > 0" sm="5">
                    <h6><strong>Temas guardados</strong></h6>
                    <b-list-group>
                        <b-list-group-item v-for="(topic, k) in topics" v-bind:key="k">
                            <b-row>
                                <b-col sm="3">{{ topic.level.level }}</b-col>
                                <b-col>{{ topic.topic }}</b-col>
                            </b-row>
                        </b-list-group-item>
                    </b-list-group>
                </b-col> -->
            </b-row>
            <b-row>
                <b-col>
                    <b-alert v-if="errorLevels" variant="warning" :show="dismissCountDown" 
                        dismissible @dismissed="dismissCountDown=0" @dismiss-count-down="countDownChanged">
                        Para continuar es necesario:
                        <ul>
                            <li>Elegir mínimo un tema por cada nivel.</li>
                            <li>Elegir <b v-for="(categorie, c) in exam.categories" v-bind:key="c">{{ categories.find(c => c.id == categorie).categorie }}, </b> en cada nivel.</li>
                        </ul>
                    </b-alert>
                    <b-alert v-if="load" show variant="info">
                        <b-spinner variant="primary"></b-spinner> 
                            Cargando..
                    </b-alert>
                </b-col>
                <b-col sm="3" class="text-right">
                    <b-button pill block :disabled="load" @click="save_topics()"
                        class="mt-2" variant="success">
                        <b-icon-check-circle></b-icon-check-circle> Guardar
                    </b-button>
                </b-col>
            </b-row>
        </div>
        <b-alert v-else show variant="info" class="text-center">
            <b-spinner variant="primary"></b-spinner> 
                Cargando..
        </b-alert>
    </div>
</template>

<script>
import setCategories from '../../mixins/setCategories';
export default {
    props: ['exam', 'edit'],
    mixins: [setCategories],
    data(){
        return {
            chooseTopics: false,
            levels: [],
            load: false,
            dismissSecs: 10,
            dismissCountDown: 0,
            errorLevels: false,
            busy: false,
            categories: [],
            options: [],
        }
    },
    created: function(){
        axios.get('/instructions/get_categories')
            .then(response => {
                this.categories = response.data;
                this.categories.forEach(categorie => {
                    this.options.push({
                        text: categorie.categorie,
                        value: categorie.id
                    });
                });
            })
            .catch(error => { });
    },
    methods: {
        // ELEGIR TEMAS
        choose_topics() {
            this.busy = true;
            let form = { categories: this.exam.categories };
            axios.put('/instructions/by_categories', form).then(response => {
                this.levels = response.data;
                this.chooseTopics = true;
                this.busy = false;
            }).catch(error => {
                this.busy = false;
            });
        },
        // RECIBIR TOPICS SELECCIONADOS
        selected_topics(topics){
            this.exam.topics = topics;
        },
        // GUARDAR TOPICS
        save_topics(){
            // if(this.chooseTopics && this.check_level()){
                this.load = true;
                // if(!this.edit){
                    axios.post('/exams/save_topics', this.exam).then(response => {
                        this.load = false;
                        if (response.data.message)
                            swal("Importante", response.data.message, "warning");
                        else
                            this.$emit('topics_saved', response.data);
                    }).catch(error => {
                        this.load = false;
                        swal("Error", "Ocurrió un error al crear el exámen. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
                    });
                // } else {
                    // let form_exam = { id: this.exam.id, topics: this.exam.topics };
                    // axios.put('/exams/update_topics', form_exam).then(response => {
                    //     this.load = false;
                    //     // console.log(response.data);
                    //     // this.$emit('topics_saved', response.data);
                    // }).catch(error => {
                    //     this.load = false;
                    //     swal("Error", "Ocurrió un error al crear el exámen. Revisa tu conexión a internet y vuelve a intentarlo.", "warning");
                    // });
                // }
            // } else {
            //     this.errorLevels = true;
            //     this.dismissCountDown = this.dismissSecs;
            // }
        },
        // VERIFICAR EL NIVEL DE CADA TEMA SELECCIONADO
        check_level(){
            // REVISAR QUE TODOS
            let lastLevel = this.levels[this.levels.length - 1];
            var level_id = lastLevel.id;
            var levels = [];

            this.exam.topics.forEach(topic => {
                levels.push(topic.level_id);
            });
            
            for (let index = 0; index < level_id; index++) {
                if(!levels.includes(index + 1)) return false
            }

            var all_ls = [];
            this.levels.forEach(level => {
                let dato = { level_id: level.id, skills: [] }
                this.exam.categories.forEach(categorie_id => {
                    let c = this.categories.find(c => c.id == categorie_id);
                    dato.skills.push({ 
                        ide: `${level.id}${c.categorie}`, 
                        categorie: c.categorie, count: 0 
                    });
                });
                all_ls.push(dato);
            });
            
            this.exam.topics.forEach(topic => {  
                topic.categories.forEach(categorie => {
                    let l_skills = all_ls.find(al => al.level_id == topic.level_id);
                    let skill = l_skills.skills.find(ls => ls.ide == `${topic.level_id}${categorie}`);
                    if(skill !== undefined) skill.count++;
                });    
            });

            let count_zero = 0;
            all_ls.forEach(als => {
                als.skills.forEach(ss => {
                    if(ss.count == 0) count_zero++;
                });
            });
            if(count_zero > 0) return false;

            return true;
        },
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
    }
}
</script>

<style>

</style>