export default {
    methods: {
        // OBTENER LAS CATEGORIES
        set_categories(instructions){
            let cs = [];
            instructions.forEach(instruction => {
                cs.push(instruction.categorie.categorie);
            });
            return this.eliminaDuplicados(cs);
        },
        eliminaDuplicados(arr){
            const unicos = [];
            for(var i = 0; i < arr.length; i++) {
                const elemento = arr[i];
                if (!unicos.includes(arr[i])) {
                    unicos.push(elemento);
                }
            }
            return unicos;
        }
    },
}