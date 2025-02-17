import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from './components/LoginComponent.vue';
import DashboardComponent from './components/DashboardComponent.vue';
import CounselorDashboardComponent from './components/CounselorDashboardComponent.vue';

const routes = [
  { path: '/', component: LoginComponent },
  { 
    path: '/dashboard', 
    component: DashboardComponent,
    beforeEnter: (to, from, next) => {
      if (!localStorage.getItem('access_token')) {
        next('/');
      } else {
        next();
      }
    }
  },
  { 
    path: '/counselor-dashboard', 
    component: CounselorDashboardComponent,
    beforeEnter: (to, from, next) => {
      if (!localStorage.getItem('access_token')) {
        next('/');
      } else {
        next();
      }
    }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
