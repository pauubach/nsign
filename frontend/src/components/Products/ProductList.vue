<template>
  <div class="q-ma-xl">
    <q-table
      class="table"
      :rows="productsStore.products"
      :columns="columns"
      :visible-columns="visibleColumns"
      row-key="id"
      selection="multiple"
      v-model:selected="selectedRows"
      rows-per-page-label="Productos por página"
      no-data-label="No hay productos"
      no-results-label="No hay productos que coincidan con la búsqueda"
      :selected-rows-label="getSelectedString"
      :pagination-label="getPaginationLabel"
      hide-no-data
      :filter="filter"
      :filter-method="filterMethod"
    >
      <template v-slot:top-left>
        <slot></slot>
      </template>
      <template v-slot:top-right>
        <q-btn
          dense
          round
          outline
          color="primary"
          @click.stop="editRow(null)"
          icon="add"
          v-if="userStore.isAdmin"
        />
        <div class="separator-sm" />
        <q-btn
          dense
          round
          outline
          float="right"
          color="accent"
          @click.stop="deleteSelectedRows()"
          icon="delete"
          v-if="selectedRows?.length && userStore.isAdmin"
        />
        <div class="separator" />
        <q-input
          dense
          debounce="300"
          v-model="filter"
          placeholder="Buscar"
          outlined
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
        &nbsp;
      </template>
      <template v-slot:bottom-right
        ><q-btn
          dense
          round
          flat
          color="accent"
          @click.stop="deleteSelectedRows()"
          icon="delete"
        ></q-btn
      ></template>
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
            <span v-if="col.name !== 'image' && col.name !== 'status'">{{
              col.value
            }}</span>
            <span v-if="col.name === 'price'"> €</span>
            <span v-if="col.name === 'status'">
              <q-icon
                v-if="col.value === 'ACTIVO'"
                name="check_circle"
                color="positive"
                size="sm"
              />
              <q-icon v-else name="cancel" color="negative" size="sm" />
            </span>
            <span v-if="col.name === 'actions' && userStore.isAdmin">
              <q-btn
                dense
                round
                flat
                color="primary"
                @click.stop="editRow(props)"
                icon="edit"
              />
              <q-btn
                dense
                round
                flat
                color="accent"
                @click.stop="deleteRow(props)"
                icon="delete"
              />
            </span>
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
  <ProductCreation
    :showDialog="showDialog"
    :product="editableProd"
    :id="editableId"
    @close="showDialog = false"
  ></ProductCreation>
</template>
<script setup>
import { ref } from "vue";
import { useProductsStore } from "stores/products";
import { useUserStore } from "stores/user";
import { Dialog } from "quasar";
import ProductCreation from "./ProductCreation.vue";

const productsStore = useProductsStore();
const userStore = useUserStore();

const visibleColumns = userStore.isAdmin
  ? ["id", "image", "title", "category", "status", "actions"]
  : ["id", "image", "title", "category", "status"];
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
    filter: false,
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
  {
    name: "actions",
    align: "center",
    field: "",
    label: "Actions",
  },
];

const selectedRows = ref();
const loadingData = ref(false);
const filter = ref("");
const showDialog = ref(false);

const loadProducts = async () => {
  loadingData.value = true;
  await productsStore.getProducts();
  // rows.value = productsStore.products;
  loadingData.value = false;
};

loadProducts();

const getPaginationLabel = (firstRowIndex, endRowIndex, totalRowsNumber) =>
  `${firstRowIndex}-${endRowIndex} de ${totalRowsNumber}`;

const getSelectedString = (numberOfRows) =>
  `${numberOfRows} filas seleccionadas`;

/**
 * Necesario para evitar que el filtrado incluya el nombre de la imagen
 * Aquí podemos eliminar el filtrado por categoría, estado o precio, pero he preferido mantenerlo
 * Bastaría con cambiar el filtro para que en vez de excluir image, incluya solo id y title
 */
const filterMethod = (rows, terms, cols, getCellValue) => {
  return rows.filter((row) =>
    cols
      .filter((col) => col.name !== "image")
      .some((col) => row[col.name].toString().includes(terms))
  );
};

const editableProd = ref();
const editableId = ref(null);

const editRow = (props) => {
  showDialog.value = true;
  editableProd.value = props?.row ? JSON.parse(JSON.stringify(props.row)) : {};
  editableId.value = props?.row.id || null;
};

const deleteRow = (props) => {
  Dialog.create({
    title: "Confirmación",
    message: "Confirma la eliminación del producto",
    cancel: true,
    persistent: true,
  }).onOk(() => {
    productsStore.deleteProducts([props.row.id]);
  });
};

const deleteSelectedRows = () => {
  const ids = JSON.parse(JSON.stringify(selectedRows.value)).map((a) => a.id);
  Dialog.create({
    title: "Confirmación",
    message: "Confirma la eliminación de los productos seleccionados",
    cancel: true,
    persistent: true,
  }).onOk(() => {
    productsStore.deleteProducts(ids);
  });
};
</script>

<style lang="scss" scoped>
.q-table {
  tbody td:after {
    background: rgba(0, 200, 180, 0.1);
  }
  .q-tr .q-td {
    &:first-child {
      width: 20px;
    }
    &:nth-child(2) {
      width: min(200px, 30vw);
    }
  }
}

.product-image {
  width: min(200px, 30vw);
  max-height: 100px;
  object-fit: cover;
}

.separator {
  width: 40px;
}
.separator-sm {
  width: 16px;
}
</style>
