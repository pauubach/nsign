<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar flat class="bg-secondary row justify-end">
        <q-btn
          v-show="!userStore.isLogged"
          class="q-mx-sm"
          color="primary"
          icon="login"
          label="Login"
          @click="$router.push('/login')"
        ></q-btn>
        <q-btn
          v-show="!userStore.isLogged"
          class="q-mx-sm"
          color="primary"
          icon="person_add"
          label="Registro"
          @click="$router.push('/register')"
        ></q-btn>
        <q-btn
          v-show="userStore.isLogged"
          class="q-mx-sm"
          color="primary"
          icon="logout"
          label="Logout"
          @click="doLogout"
        ></q-btn>
      </q-toolbar>
    </q-header>
    <q-page-container>
      <ProductList v-if="userStore.isLogged">
        <h4>Listado de productos</h4>
      </ProductList>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<script setup>
import { useUserStore } from "stores/user";
import { useRouter } from "vue-router";
import ProductList from "components/Products/ProductList.vue";

const userStore = useUserStore();
const router = useRouter();

const doLogout = async () => {
  userStore.logout();
  router.push("/login");
};

const checkLogged = async () => {
  await userStore.checkUser();
  if (!userStore.isLogged) {
    router.push("/login");
  } else {
    router.push("/");
  }
};

checkLogged();
</script>
