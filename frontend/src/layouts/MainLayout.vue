<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title>Pizza Place</q-toolbar-title>

        <div>
          <q-btn flat label="Logout" @click="logout" />
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="bg-primary">
      <q-list>
        <q-item-label header></q-item-label>

        <EssentialLink v-for="link in essentialLinks" :key="link.title" v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import EssentialLink from 'components/EssentialLink.vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth-store'

const linksList = [
  {
    title: 'Dashboard',
    caption: 'Your Dashboard',
    icon: 'dashboard',
    link: '/dashboard',
  },
  {
    title: 'Products',
    caption: 'View Products',
    icon: 'local_pizza',
    link: '/products',
  },
  {
    title: 'Orders',
    caption: 'View Orders',
    icon: 'receipt_long',
    link: '/orders',
  },
]

export default defineComponent({
  name: 'MainLayout',

  components: {
    EssentialLink,
  },

  setup() {
    const leftDrawerOpen = ref(false)
    const router = useRouter()
    const authStore = useAuthStore() // Access the Pinia store

    const logout = async () => {
      await authStore.logout()
      router.push({ name: 'Login' })
    }

    return {
      essentialLinks: linksList,
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value
      },
      logout,
    }
  },
})
</script>
