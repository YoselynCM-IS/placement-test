export default {
    data(){
        return {
            
        }
    },
    filters: {
        get_cal: function(entre){
            // return (exam.pivot.correct / exam.pivot.total) * 100;
            let cal = entre * 100;
            if(cal > 0) return cal.toFixed(0);
            return 0;
        }
    },
}