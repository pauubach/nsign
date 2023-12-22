<template>
  <q-dialog :model-value="showDialog" @hide="$emit('close')">
    <q-card style="width: 600px; max-width: 60vw">
      <q-form class="q-gutter-md" @submit="sendForm">
        <q-card-section>
          <q-btn
            round
            flat
            dense
            icon="close"
            class="float-right"
            color="grey-8"
            v-close-popup
          ></q-btn>
          <div class="text-h6">{{ title }}</div>
        </q-card-section>
        <q-separator inset></q-separator>
        <q-card-section class="q-pt-none">
          <input type="hidden" :value="prod.id" />
          <q-list>
            <q-item>
              <q-item-section>
                <q-input outlined v-model="prod.title" label="Nombre *" />
              </q-item-section>
            </q-item>
            <q-item class="q-pb-none">
              <q-item-section>
                <q-select
                  v-model="prod.category"
                  :options="productStore.categories"
                  option-value="name"
                  option-label="name"
                  outlined
                  label="Categoría *"
                />
              </q-item-section>
            </q-item>
            <q-item class="q-pa-none q-ma-none"
              ><q-btn
                label="Nueva categoría"
                icon="add"
                flat
                size="md"
                class="float-right"
                color="secondary"
                @click="addCategory"
              ></q-btn>
            </q-item>
            <q-item
              ><q-item-section>
                <q-toggle
                  label="Estado *"
                  v-model="prod.status"
                  color="positive"
                  true-value="ACTIVO"
                  false-value="INACTIVO"
                ></q-toggle
              ></q-item-section>
            </q-item>
            <q-item
              ><q-item-section>
                <q-input
                  outlined
                  v-model="prod.price"
                  label="Precio *"
                  type="number"
                  step="0.01"
                /> </q-item-section
            ></q-item>
            <q-item>
              <q-item-section
                ><q-file
                  outlined
                  v-model="prod.image"
                  label="Imagen"
                  accept=".jpg, image/*"
                  max-file-size="2097152"
                  @rejected="fileRejected"
              /></q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
        <q-card-section>
          <q-card-actions align="right">
            <q-btn
              flat
              label="Cancelar"
              color="primary"
              dense
              v-close-popup
            ></q-btn>
            <q-btn
              type="submit"
              flat
              label="Guardar"
              color="primary"
              dense
              v-close-popup
            ></q-btn>
          </q-card-actions>
        </q-card-section>
      </q-form> </q-card
  ></q-dialog>
</template>
<script setup>
import { ref, computed, watch } from "vue";
import { useProductsStore } from "stores/products";
import { Dialog, Notify } from "quasar";

defineEmits(["close"]);

const props = defineProps({
  showDialog: {
    type: Boolean,
    required: true,
  },
  product: {
    type: Object,
  },
  id: {
    type: Number,
  },
});

const prod = ref({
  id: props.product?.id,
  title: props.product?.title,
  category: { name: props.product?.category },
  status: props.product?.status || "INACTIVO",
  price: props.product?.price,
  image: typeof props.product?.image === "string" ? null : props.product?.image,
});

const productStore = useProductsStore();

const title = computed(() =>
  props.product ? "Editar producto" : "Nuevo producto"
);

const fileRejected = () => {
  Notify.create({
    type: "warning",
    message: "Solo se aceptan imágenes menores de 2MB",
  });
};

const addCategory = () => {
  Dialog.create({
    title: "Nueva categoría",
    message: "Introduce el nombre",
    prompt: {
      model: "",
      type: "text",
    },
    cancel: true,
  }).onOk((data) => {
    productStore.addCategory(data);
    prod.value.category = { name: data };
  });
};

const idObserver = computed(() => {
  updateProd();
  return "";
});

const updateProd = () => {
  prod.value = {
    id: props.id,
    title: props.product?.title,
    category: { name: props.product?.category },
    status: props.product?.status || "INACTIVO",
    price: props.product?.price,
    image:
      typeof props.product?.image === "string" ? null : props.product?.image,
  };
};
const sendForm = () => {
  productStore.addProduct(prod.value);
};
</script>
