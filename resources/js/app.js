/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/** VUE-RESOURCE */
import VueResource from 'vue-resource';
Vue.use(VueResource);

//necesario para http post, put, delete channel routes
Vue.http.interceptors.push((request, next) => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    request.headers.set('X-CSRF-TOKEN', csrfToken);
    next();
});
/** VUE-RESOURCE */

/** BOOTSTRAP VUEJS */
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
/** BOOTSTRAP VUEJS */

// MOMENT

Vue.use(require('vue-moment'));

// PAGINATION
Vue.component('pagination', require('laravel-vue-pagination'));

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/** ADMINISTRADOR */
Vue.component('adm-home-component', require('./components/administrator/HomeComponent.vue').default);

// EXAM
Vue.component('list-exams', require('./components/administrator/exam/ListExams.vue').default);

// USERS
Vue.component('list-component', require('./components/administrator/users/ListComponent.vue').default);
// TEACHER
Vue.component('list-teachers', require('./components/administrator/users/teacher/ListTeachers.vue').default);
Vue.component('create-edit-teacher', require('./components/administrator/users/teacher/CreateEditTeacher.vue').default);

/** ADMINISTRADOR */

/** TEACHER */
Vue.component('teach-home-component', require('./components/teacher/HomeComponent.vue').default);

// EXAM
Vue.component('my-exams', require('./components/teacher/exam/MyExams.vue').default);

/** TEACHER */


/** STUDENT */
Vue.component('student-home-component', require('./components/student/HomeComponent.vue').default);

// EXAMS
Vue.component('assigned-exams', require('./components/student/exam/AssignedExams.vue').default);
Vue.component('check-exam', require('./components/student/exam/CheckExam.vue').default);
Vue.component('solve-exam', require('./components/student/exam/SolveExam.vue').default);

/** STUDENT */

/** LEVELS */
Vue.component('select-topics', require('./components/topics/SelectTopics.vue').default);
/** LEVELS */

/** REAGENTS */
Vue.component('reagents-list-component', require('./components/reagents/ListComponent.vue').default);
// LEVELS
Vue.component('new-edit-level-component', require('./components/reagents/levels/NewEditLevel.vue').default);
// TOPICS
Vue.component('new-edit-topic-component', require('./components/reagents/topics/NewEditTopic.vue').default);
// INSTRUCTION
Vue.component('new-edit-instruction-component', require('./components/reagents/instructions/NewEditInstruction.vue').default);
Vue.component('move-instruction-component', require('./components/reagents/instructions/MoveInstruction.vue').default);
// QUESTIONS
Vue.component('new-edit-question-component', require('./components/reagents/questions/NewEditQuestion.vue').default);
// ANSWERS
Vue.component('new-edit-answers-component', require('./components/reagents/answers/NewEditAnswers.vue').default);

/** EXAMS */
Vue.component('gral-details-exam', require('./components/exam/DetailsExam.vue').default);
Vue.component('form-exam', require('./components/exam/FormExam.vue').default);
Vue.component('create-exam', require('./components/exam/CreateExam.vue').default);

Vue.component('form-datos', require('./components/exam/FormDatos.vue').default);
Vue.component('form-topics', require('./components/exam/FormTopics.vue').default);
Vue.component('form-questions', require('./components/exam/FormQuestions.vue').default);
/** EXAMS */

/** GROUPS */
Vue.component('create-edit-group', require('./components/groups/CreateEditGroup.vue').default);
Vue.component('list-groups', require('./components/groups/ListGroups.vue').default);

// STUDENTS
Vue.component('add-edit-student', require('./components/groups/students/AddEditStudent.vue').default);
Vue.component('upload-students', require('./components/groups/students/UploadStudents.vue').default);
Vue.component('list-group-student', require('./components/groups/students/ListGroupStudents.vue').default);

// RESULTS
Vue.component('list-results', require('./components/groups/results/ListResults.vue').default);
Vue.component('results-exam', require('./components/groups/results/ResultsExam.vue').default);
Vue.component('details-results', require('./components/groups/results/DetailsResults.vue').default);

/** GROUPS */

/** INDEPENDENT */
// QUESTIONS GRAL
Vue.component('q-select-component', require('./components/independent/reagents/QSelectComponent.vue').default);
Vue.component('q-open-component', require('./components/independent/reagents/QOpenComponent.vue').default);
Vue.component('q-multiple-component', require('./components/independent/reagents/QMultipleComponent.vue').default);
// Vue.component('q-match-component', require('./components/independent/reagents/QMatchComponent.vue').default);

// QUESTIONS FORM
Vue.component('form-select-component', require('./components/independent/reagents/form/FormSelectComponent.vue').default);
Vue.component('form-open-component', require('./components/independent/reagents/form/FormOpenComponent.vue').default);
Vue.component('form-multiple-component', require('./components/independent/reagents/form/FormMultipleComponent.vue').default);


// FUNCTIONS
Vue.component('timer-component', require('./components/independent/functions/TimerComponent.vue').default);
Vue.component('notifications-component', require('./components/independent/functions/NotificationsComponent.vue').default);
Vue.component('track-component', require('./components/independent/functions/TrackComponent.vue').default);

/** INDEPENDENT */

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
