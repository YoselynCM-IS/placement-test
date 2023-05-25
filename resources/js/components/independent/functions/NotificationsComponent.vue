<template>
    <div>
        <li class="list-inline-item dropdown">
            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <b-icon-bell></b-icon-bell>
                <b-badge v-if="notifications.length" 
                    pill variant="danger">
                    {{ notifications.length }}
                </b-badge>
            </a>

            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-lg">
                <!-- All-->
                <button
                    @click="detailsExam(notification)"
                    v-for="(notification, index) in notifications"
                    :key="index"
                    class="dropdown-item notify-item notify-all"
                >
                    {{ notification.title }}
                </button>

            </div>
        </li>
    </div>
</template>

<script>
export default {
    data(){
        return {
            notifications: []
        }
    },
    mounted() {
        Echo.private('exam-update')
            .listen('ExamUpdate', (data) => {
            this.notifications.push({
                exam_id: data.exam.id,
                title: `El examen ${data.exam.name} ha sido actualizado`
            });
        });
    },
    methods: {
        detailsExam(){
            
        }
    }
}
</script>

<style>

</style>