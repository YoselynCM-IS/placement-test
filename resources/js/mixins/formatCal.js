export default {
    data(){
        return {
            
        }
    },
    filters: {
        get_cal: function(skill){
            // return (exam.pivot.correct / exam.pivot.total) * 100;
            let cal = (skill.points / skill.total) * 100;
            if(cal > 0) return cal.toFixed(0);
            return 0;
        }
    },
}