export default {
    methods: {
        check_time(d){
            return (d < 10) ? `0${d}`:d;
        },
        zeroPadding(num, digit) {
            var zero = '';
            for (var i = 0; i < digit; i++) {
                zero += '0';
            }
            return (zero + num).slice(-digit);
        },
    }
}