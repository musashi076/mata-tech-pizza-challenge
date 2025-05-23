<template>
  <q-layout view="lHh Lpr lFf" class="login-page-bg full-height-viewport">
    <q-page-container>
      <q-page class="flex flex-center">
        <q-card class="login-card">
          <div class="row full-height">
            <q-card-section
              class="col-md-5 col-sm-12 col-xs-12 bg-primary text-white flex flex-center column q-pa-md"
            >
              <div class="text-h4 text-weight-bold q-mb-md text-center">WELCOME!</div>
              <div class="text-subtitle1 text-center q-px-md">
                Please sign in to your account to check your Pizza Place!
              </div>
            </q-card-section>

            <q-card-section class="col-md-7 col-sm-12 col-xs-12 flex flex-center column q-pa-lg">
              <div class="text-h6 text-grey-7 q-mb-sm self-start system-name">Pizza Place</div>
              <div class="text-h5 text-primary text-weight-bold q-mb-lg">LOGIN</div>

              <q-input
                filled
                v-model="email"
                label="Email"
                class="full-width q-mb-md"
                @keyup.enter="doLogin"
              />
              <q-input
                filled
                v-model="password"
                label="Password"
                type="password"
                class="full-width q-mb-lg"
                @keyup.enter="doLogin"
              />

              <q-btn
                label="LOGIN"
                color="primary"
                class="full-width q-py-sm q-mb-md"
                rounded
                @click="doLogin"
              />
            </q-card-section>
          </div>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth-store'
import { useQuasar } from 'quasar'

export default defineComponent({
  name: 'LoginPage',
  setup() {
    const email = ref('')
    const password = ref('')
    const router = useRouter()
    const authStore = useAuthStore()
    const $q = useQuasar()

    const doLogin = async () => {
      const success = await authStore.login(email.value, password.value)

      if (success) {
        $q.notify({
          type: 'positive',
          message: 'Login successful!',
          position: 'top-right',
        })
        router.push({ name: 'Dashboard' })
      } else {
        $q.notify({
          type: 'negative',
          message: 'Invalid credentials. Please try again.',
          position: 'top-right',
        })
      }
    }

    return {
      email,
      password,
      doLogin,
    }
  },
})
</script>

<style lang="scss" scoped>
.login-page-bg {
  background-color: #2196f3;
  position: relative;
  overflow: hidden;
  min-height: 100vh;
  width: 100%;
}
</style>
