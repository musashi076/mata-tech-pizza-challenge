const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      // { path: '', component: () => import('pages/IndexPage.vue') },
      {
        path: '',
        redirect: '/dashboard',
      },

      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('pages/DashboardPage.vue'),
        meta: { requiresAuth: true },
      },

      {
        path: 'products',
        name: 'Products',
        component: () => import('pages/ProductPage.vue'),
        meta: { requiresAuth: true },
      },

      {
        path: 'orders',
        name: 'Orders',
        component: () => import('pages/OrderPage.vue'),
        meta: { requiresAuth: true },
      },

      {
        path: 'daily-sales-report',
        name: 'DailySalesReport',
        component: () => import('pages/SalesReports/DailySalesReportPage.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },

  {
    path: '/login',
    name: 'Login',
    component: () => import('pages/LoginPage.vue'),
    meta: { guestOnly: true },
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
