<template>
    <div>
        <b-card-group deck>
            <b-card
                header="Profesores"
                header-text-variant="white"
                align="center" id="card-home">
                <b-button id="btn-home" pill href="/administrator/teachers">
                    <b-icon-person-fill font-scale="4"></b-icon-person-fill>
                </b-button>
            </b-card>
            <b-card
                header="Examenes"
                header-text-variant="white"
                align="center" id="card-home">
                <b-button id="btn-home" pill href="/administrator/exams">
                    <b-icon-pencil-square font-scale="4"></b-icon-pencil-square>
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
</template>

<script>
export default {
    
}
</script>