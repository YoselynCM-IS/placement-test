<template>
    <div>
        <div class="container col-md-9">
            <b-card-group deck>
                <b-card
                    header="Mis examenes"
                    header-text-variant="white"
                    align="center" id="card-home">
                    <b-button id="btn-home" pill href="/teacher/exams">
                        <b-icon-pencil-square font-scale="4"></b-icon-pencil-square>
                    </b-button>
                </b-card>
                <b-card
                    header="Mis grupos"
                    header-text-variant="white"
                    align="center" id="card-home">
                    <b-button id="btn-home" pill href="/teacher/groups">
                        <b-icon-people-fill font-scale="4"></b-icon-people-fill>
                    </b-button>
                </b-card>
            </b-card-group>
            <b-card-group deck class="mt-5">
                <b-card
                    header="Resultados"
                    header-text-variant="white"
                    align="center" id="card-home">
                    <b-button id="btn-home" pill href="/teacher/results">
                        <b-icon-card-checklist font-scale="4"></b-icon-card-checklist>
                    </b-button>
                </b-card>
                <b-card
                    header="Reactivos"
                    header-text-variant="white"
                    align="center" id="card-home">
                    <b-button id="btn-home" pill href="/levels/home">
                        <b-icon-question-square font-scale="4"></b-icon-question-square>
                    </b-button>
                </b-card>
            </b-card-group>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            
        }
    },
    methods: {
        
    }
}
</script>