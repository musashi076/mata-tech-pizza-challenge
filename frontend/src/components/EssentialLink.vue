<template>
  <q-item clickable tag="a" target="_self" :to="link" active-class="bg-primary-darker">
    <q-item-section v-if="icon" avatar> <q-icon :name="icon" class="text-white" /> </q-item-section>

    <q-item-section>
      <q-item-label class="text-white">{{ title }}</q-item-label>
      <q-item-label caption class="text-white">{{ caption }}</q-item-label>
    </q-item-section>
  </q-item>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'EssentialLink',
  props: {
    title: {
      type: String,
      required: true,
    },

    caption: {
      type: String,
      default: '',
    },

    link: {
      type: String,
      default: '#',
    },

    icon: {
      type: String,
      default: '',
    },
  },
})
</script>

<style lang="scss" scoped>
.bg-primary-darker {
  background-color: darken($primary, 10%) !important;
}
</style>
