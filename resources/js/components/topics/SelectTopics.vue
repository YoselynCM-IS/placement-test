<template>
    <div>
        <b-tabs pills card vertical>
            <b-tab v-for="(level, i) in all_levels" v-bind:key="i" 
                :title="level.level" @click="position = i">
                <b-table :items="level.topics" :fields="fieldsTopics"
                    :select-mode="selectMode" responsive="sm" ref="selectableTable"
                    selectable @row-selected="onRowSelected">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(skills)="data">
                        <ul v-for="(categorie, j) in data.item.categories"  v-bind:key="j">
                            <li>{{ categorie }}</li>
                        </ul>
                    </template>
                    <template #cell(selected)="{ rowSelected }">
                        <template v-if="rowSelected">
                            <span aria-hidden="true">&check;</span>
                            <span class="sr-only">Selected</span>
                        </template>
                        <template v-else>
                            <span aria-hidden="true">&nbsp;</span>
                            <span class="sr-only">Not selected</span>
                        </template>
                    </template>
                </b-table>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
import setCategories from '../../mixins/setCategories';
export default {
    props: ['levels'],
    mixins: [setCategories],
    data(){
        return {
            fieldsTopics: [
                { label: 'Tema', key: 'topic' },
                { label: 'Skills', key: 'skills' },
                { label: 'Seleccionar', key: 'selected' }
            ],
            selectMode: 'multi',
            selected: [],
            levelTopics: [],
            position: 0,
            topics: [],
            all_levels: []
        }
    },
    created: function(){
        this.levels.forEach(level => {
            var dato1 = {
                id: level.id, 
                level: level.level, 
                topics: []
            };
            level.topics.forEach(topic => {
                var categories = this.set_categories(topic.instructions);
                var dato2 = {
                    id: topic.id, 
                    level_id: topic.level_id, 
                    topic: topic.topic, 
                    categories: categories,
                    instructions: topic.instructions
                };
                dato1.topics.push(dato2);
            });
            this.all_levels.push(dato1);
            this.levelTopics.push({level_id: level.id, topics: []});
        });
    },
    methods: {
        // SELECCION DE REGISTROS EN LA TABLA
        onRowSelected(items) {
            this.selected = items;
            this.levelTopics[this.position].topics = this.selected;
            this.topics = [];
            this.levelTopics.forEach(lt => {
                lt.topics.forEach(topic => {
                    this.topics.push(topic);
                });
            });
            this.$emit('selected_topics', this.topics);
        },
    }
}
</script>

<style>

</style>