<template>
  <div class="q-pa-md relative" v-if="!userStore.isLogged">
    <q-form
      @submit="doLogin"
      class="q-gutter-md"
      style="max-width: 400px; margin: auto"
    >
      <q-input
        filled
        v-model="user.email"
        label="Email *"
        autocomplete="email"
        :rules="[(val, rules) => rules.email(val) || 'Escribe un email válido']"
      />
      <q-input
        v-model="user.password"
        filled
        autocomplete="current-password"
        :type="user.isPwd ? 'password' : 'text'"
        :rules="[(val) => val.length || 'Escribe un password']"
        label="Contraseña *"
      >
        <template v-slot:append>
          <q-icon
            :name="user.isPwd ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            @click="user.isPwd = !user.isPwd"
          />
        </template>
      </q-input>
      <div>
        <q-btn
          label="Submit"
          type="submit"
          color="primary"
          :disabled="loading"
        />
      </div>
    </q-form>
  </div>
</template>

<script setup>
import { useUserStore } from "stores/user";
import { useNotificationStore } from "stores/notification";
import { useRouter } from "vue-router";
import { ref } from "vue";

const userStore = useUserStore();
const notificationStore = useNotificationStore();

const user = ref({
  email: "",
  password: "",
  isPwd: true,
});

const loading = ref(false);

const router = useRouter();

const doLogin = async () => {
  try {
    loading.value = true;
    await userStore.login({
      email: user.value.email,
      password: user.value.password,
    });

    router.push({ name: "home" });
  } catch (error) {
    notificationStore.error({ message: error });
  } finally {
    loading.value = false;
  }
};
</script>
