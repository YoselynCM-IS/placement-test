export default {
    data(){
        return {
            
        }
    },
    methods: {
        check_time(d){
            return (d < 10) ? `0${d}`:d;
        }
    }
}