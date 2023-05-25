export default {
    data() {
        return {
            sSchool: null,
            schools: []
        }
    },
    methods: {
        // MOSTRAR LISTADO DE ESCUELA
        show_schools(){
            if(this.sSchool.length > 0 && this.sSchool !== ' '){
                axios.get('/schools/by_name', {params: {school: this.sSchool}}).then(response => {
                    this.schools = response.data;
                });
            } else {
                this.schools = [];
            }
        },
    }
}