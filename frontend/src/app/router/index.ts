import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '../../pages/HomePage.vue'
import CompaniesPage from '../../pages/companies/CompaniesPage.vue'
import CompanyFormPage from '../../pages/companies/CompanyFormPage.vue'
import ProductsPage from '../../pages/products/ProductsPage.vue'
import ProductFormPage from '../../pages/products/ProductFormPage.vue'
import NotFoundPage from '../../pages/NotFoundPage.vue'

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
    { path: '/products', name: 'products', component: ProductsPage },
    { path: '/products/new', name: 'product-create', component: ProductFormPage },
    { path: '/products/:id/edit', name: 'product-edit', component: ProductFormPage },
    { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFoundPage },
  ],
})
