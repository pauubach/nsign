<template>
  <q-page class="flex q-px-xl">
    <product-list> </product-list>
  </q-page>
</template>

<script>
import { defineComponent } from "vue";
import ProductList from "components/Products/ProductList.vue";

export default defineComponent({
  name: "IndexPage",
});
</script>
