<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar flat class="bg-secondary row justify-between">
        <q-btn
          color="primary"
          icon="home"
          label="Inicio"
          @click="$router.push('/')"
        ></q-btn>
        <q-btn
          color="primary"
          icon="login"
          label="Login"
          @click="$router.push('/login')"
        ></q-btn>
      </q-toolbar>
    </q-header>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
// import { defineComponent, ref } from "vue";

// export default defineComponent({
//   name: "MainLayout",

//   setup() {
//     return {};
//   },
// });
</script>
