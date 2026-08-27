import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '../../pages/HomePage.vue'
import CompaniesPage from '../../pages/companies/CompaniesPage.vue'
import CompanyFormPage from '../../pages/companies/CompanyFormPage.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomePage,
    },
    { path: '/companies', name: 'companies', component: CompaniesPage },
    { path: '/companies/new', name: 'company-create', component: CompanyFormPage },
    { path: '/companies/:id/edit', name: 'company-edit', component: CompanyFormPage },
  ],
})
