<template>
  <div class="q-mx-xl">
    <slot></slot>
    <q-table
      :rows="rows"
      :columns="columns"
      :visible-columns="visibleColumns"
      row-key="id"
      rows-per-page-label="Productos por página"
      selection="multiple"
      v-model:selected="selectedRows"
    >
      <template v-slot:header="props">
        <q-tr :props="props">
          <q-th v-for="col in props.cols" :key="col.name" :props="props">
            {{ col.label }}
          </q-th>
        </q-tr>
      </template>

      <template v-slot:body="props">
        <q-tr
          :props="props"
          @click="props.selected = !props.selected"
          :loading="loadingData"
        >
          <q-td v-for="col in props.cols" :key="col.name" :props="props">
            <span v-if="col.name !== 'image'">{{ col.value }}</span>
            <span v-if="col.name === 'price'"> €</span>
            <img
              class="product-image"
              v-if="col.name === 'image'"
              :src="props.row.imageFile"
            />
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>
<script setup>
import { ref } from "vue";
import { useProductsStore } from "stores/products";

const productsStore = useProductsStore();

const columns = [
  {
    name: "id",
    field: "id",
    label: "",
  },
  {
    name: "image",
    field: "imageFile",
    label: "",
    autowidth: true,
  },
  {
    name: "title",
    field: "title",
    label: "Nombre",
    sortable: true,
    align: "left",
  },
  {
    name: "category",
    field: "category",
    label: "Categoría",
    sortable: true,
    align: "left",
  },
  {
    name: "status",
    field: "status",
    label: "Estado",
    sortable: true,
    align: "left",
  },
  {
    name: "price",
    field: "price",
    label: "Precio",
    sortable: true,
    align: "left",
  },
];

const visibleColumns = ["image", "title", "category", "status", "price"];

const rows = ref([]);
const selectedRows = ref();
const loadingData = ref(false);

const loadProducts = async () => {
  loadingData.value = true;
  await productsStore.getProducts();
  rows.value = productsStore.products;
  loadingData.value = false;
};

loadProducts();
</script>

<style lang="scss" scoped>
.q-table {
  tbody td:after {
    background: rgba(0, 200, 180, 0.1);
  }
  .q-tr .q-td:first-child {
    width: min(200px, 30vw);
  }
}

.product-image {
  width: min(200px, 30vw);
  max-height: 100px;
  object-fit: cover;
}
</style>
