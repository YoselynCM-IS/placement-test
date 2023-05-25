export default {
    data(){
        return {
            levels_name: []
        }
    },
    mounted: function(){
        axios.get('/levels/get_all').then(response => {
            this.levels_name = response.data;
        }).catch(error => { });
    },
    methods: {
        filter_level(level_id){
            if(this.levels_name.length > 0){
                let l = this.levels_name.find(level => level.id == level_id);
                return l.level;
            }
            return '';
            // console.log(level_id);
            // if(level_id == 1) return 'A1';
            // if(level_id == 2) return 'A1+';
            // if(level_id == 3) return 'A2';
            // if(level_id == 4) return 'A2+';
            // if(level_id == 5) return 'B1';
            // if(level_id == 6) return 'B1+';
        }
    },
}