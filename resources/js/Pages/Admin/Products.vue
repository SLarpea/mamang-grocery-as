<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Admin/Modal.vue";
import { onMounted, reactive, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const APP_URL = import.meta.env.VITE_APP_URL;

const props = defineProps({
  products: Array,
  categories: Array,
  message: String,
});

onMounted(() => {
  props.products.forEach((item) => {
    sales.value[item.id] = item.on_sale;
  });
});

const showModal = ref(false);
const modalMode = ref("default");
const previewImage = ref("");
const search = ref("");
const itemsPerPage = ref(6);
const loading = ref(false);
const sales = ref([]);
const headers = ref([
  {
    title: "Id",
    align: "start",
    key: "id",
  },
  {
    title: "Name",
    align: "start",
    key: "name",
  },
  {
    title: "Image",
    align: "start",
    key: "thumbnail",
    sortable: false,
  },
  {
    title: "Price",
    align: "start",
    key: "price",
  },
  {
    title: "Categories",
    align: "start",
    key: "categories",
  },
  {
    title: "Sale",
    align: "start",
    key: "sale",
  },
  {
    title: "Action",
    align: "start",
    key: "actions",
    sortable: false,
  },
]);
const actions = reactive({
  add: {
    icon: "fa-regular fa-square-plus",
    method: "post",
    path: "product.store",
    action: "saved",
  },
  edit: {
    icon: "fa-solid fa-pen",
    method: "post",
    path: "product.update",
    action: "updated",
  },
  delete: {
    icon: "fa-solid fa-xmark",
    method: "post",
    path: "product.delete",
    action: "removed",
  },
});
const form = useForm({
  id: null,
  name: null,
  file: null,
  price: null,
  selectedCategories: [],
  img_link: null,
});

const openModal = (mode, id = null) => {
  if (mode !== "add") setFormValue(id);
  modalMode.value = mode;
  showModal.value = true;
};

const onImagePreview = (e) => {
  form.file = e.target.files[0];
  previewImage.value = URL.createObjectURL(form.file);
};

const submitForm = (mode) => {
  form.submit(actions[mode].method, route(actions[mode].path), {
    onSuccess: (response) => {
      if (response.props.message === "success") {
        modalMode.value = "default";
        showModal.value = false;
        showToast(`Product ${form.name.toUpperCase()} has been ${actions[mode].action}`);
        form.reset();
        previewImage.value = "";
      }
    },
  });
};

const setFormValue = (id) => {
  const product = props.products.find((item) => item.id === id);
  form.id = id;
  form.name = product.name;
  form.price = product.price;
  form.selectedCategories = JSON.parse(product.categories);
  form.img_link = product.img_link;
  previewImage.value = product.img_link;
};

const showToast = (title) => {
  Swal.fire({
    toast: true,
    position: 'top-end',
    title: title,
    icon: "success",
    showConfirmButton: false,
    timer: 3000,
  });
};

const closeModal = () => {
  showModal.value = false;
  previewImage.value = "";
  form.reset();
};

const imgPath = (imageSrc) => {
  return imageSrc.includes("blob") ? imageSrc : APP_URL + imageSrc;
};

const chipColor = () => {
  var colors = ['blue', 'red', 'teal', 'orange', 'brown'];
  var randNum = Math.floor(Math.random() * 5);

  return colors[randNum];
};

const onChangeSwitch = (item) => {
  Swal.fire({
    title: "Activate Sale",
    input: 'number',
    inputLabel: 'Enter percent on sale',
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: "Item on Sale!",
    showLoaderOnConfirm: true,
    preConfirm: async (percent) => {
      try {
        Inertia.visit(route('product.activate.sale'), {
          method: 'post',
          data: { id: item.id, sale_percent: parseInt(percent) },
          onError: error => {
            alert(error);
          }
        });
      } catch (error) {
        Swal.showValidationMessage(`Request failed: ${error} `);
      }
    },
    cancelButtonText: "Cancel",
    allowOutsideClick: false,
    customClass: "default",
  }).then((result) => {
    if (result.isConfirmed) {
      showToast(
        `Product of ${item.name.toUpperCase()} is now on sale!`
      );
    } else if (result.isDismissed) {
      sales.value[item.id] = 0;
    }
  });
};

const deactivateProductSale = (item) => {
  Swal.fire({
    title: "Deactivate Sale",
    text: `Are you sure deactivate ${item.percent_sale}% sale on this ${item.name.toUpperCase()} product?`,
    showConfirmButton: true,
    confirmButtonText: "Yes, Deactivate It!",
    showCancelButton: true,
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      Inertia.visit(route('product.deactivate.sale'), {
        method: 'post',
        data: { id: item.id },
        onSuccess: page => {
          showToast(`Product sale ${item.name.toUpperCase()} have been deactivated`);
        }
      })
    }
  })
};

</script>

<template>
  <AdminLayout title="Products">
    <div class="wrapper">
      <v-sheet
        class="products p-5"
        height="auto"
        width="100%"
        style="
          box-shadow: rgba(0, 0, 0, 0.15) 0 0.3rem 0.2rem 0;
          border-bottom: 0.3rem solid #6a1b9a;
        "
        border
        rounded
      >
        <v-row
          style="
            height: 5rem;
            display: flex;
            align-items: center;
            margin-bottom: 1.3rem;
          "
          no-gutters
        >
          <v-col cols="11">
            <span class="card-title">Products</span>
            <span class="card-subtitle">All grocery products are here.</span>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              size="small"
              color="add-bg-button-color"
              prepend-icon="fa-solid fa-circle-plus"
              @click="openModal('add')"
            >
              add product
            </v-btn>
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col cols="12">
            <v-data-table
              v-model:items-per-page="itemsPerPage"
              :search="search"
              :headers="headers"
              :items-length="products.length"
              :items="products"
              :loading="loading"
              class="elevation-1 table"
              item-value="name"
              loading-text="Loading... Please wait"
              no-data-text="No data available"
            >
              <template v-slot:top>
                <v-text-field
                  v-model="search"
                  label="Search (UPPER CASE ONLY)"
                  color="primary-bg-color"
                  class="pa-4"
                  variant="outlined"
                ></v-text-field>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-icon
                  icon="fa-solid fa-pen"
                  size="small"
                  class="me-2"
                  color="edit-bg-button-color"
                  @click="openModal('edit', item.id)"
                ></v-icon>
                <v-icon
                  icon="fa-solid fa-trash"
                  size="small"
                  color="delete-bg-button-color"
                  @click.prevent="openModal('delete', item.id)"
                ></v-icon>
              </template>
              <template v-slot:item.name="{ item }">
                <td style="font-weight: 600; text-transform: capitalize">
                  {{ item.name }}
                </td>
              </template>
              <template v-slot:item.price="{ item }">
                <td style="font-weight: 600; color: #388e3c">
                  PHP {{ item.price }}
                </td>
              </template>
              <template v-slot:item.thumbnail="{ item }">
                <td>
                  <div class="imageThumb">
                    <img :src="APP_URL + item.img_link" alt="previewImg" />
                  </div>
                </td>
              </template>
              <template v-slot:item.categories="{ item }">
                <td>
                  <v-chip v-for="(item, index) in JSON.parse(item.categories)" size="small" :color="chipColor()" :key="index">
                    <v-icon class="mr-1" size="x-small" icon="fa-solid fa-tag"></v-icon>
                    {{ item }}
                  </v-chip>
                </td>
              </template>
              <template v-slot:item.sale="{ item }">
                <td>
                  <v-btn
                    v-if="item.on_sale === 1 && item.percent_sale !== 0"
                    variant="tonal"
                    density="comfortable"
                    color="#B71C1C"
                    rounded
                    @click.prevent="deactivateProductSale(item)"
                  >
                    {{ item.percent_sale }}% off
                  </v-btn>
                  <v-switch
                    v-if="item.on_sale === 0 && item.percent_sale === 0"
                    v-model="sales[item.id]"
                    color="teal"
                    :true-value="1"
                    :false-value="0"
                    @change="onChangeSwitch(item)"
                    hide-details
                  ></v-switch>
                </td>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-sheet>
    </div>
    <v-dialog class="dialog" v-model="showModal" width="50rem" persistent>
      <Modal
        :mode="modalMode"
        :imgSrc="previewImage"
        @submit="submitForm(modalMode)"
        @cancel="closeModal()"
      >
        <template #header>
          <span class="card-title">{{ modalMode }} product</span>
        </template>

        <template #body>
          <v-img
            v-if="previewImage"
            class="bg-white mb-5 mx-auto"
            width="300"
            :aspect-ratio="1"
            :src="imgPath(previewImage)"
            cover
          ></v-img>
          <div v-if="modalMode === 'delete'">
            <span class="deletePromptTitle"
              >Are you sure to delete this
              <strong>{{ form.name?.toUpperCase() }}</strong> product?</span
            >
            <span class="deletePromptSubTitle"
              >You won't be able to revert this!</span
            >
          </div>
          <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.name"
            label="Name"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.name = null"
            clearable
          ></v-text-field>
          <v-file-input
            v-if="modalMode !== 'delete'"
            label="Select Image"
            variant="outlined"
            color="primary-bg-color"
            prepend-icon=""
            density="compact"
            @change="onImagePreview"
            @click:clear="previewImage = ''"
            clearable
          ></v-file-input>
          <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.price"
            label="Price"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.price = null"
            clearable
          ></v-text-field>
          <v-select
            v-if="modalMode !== 'delete'"
            v-model="form.selectedCategories"
            label="Category"
            class="category"
            color="primary-bg-color"
            no-data-text="No data available"
            variant="outlined"
            item-value="name"
            item-title="name"
            :items="categories"
            multiple
            chips
            clearable
          >
            <template v-slot:chip="{ item }">
              <v-chip color="primary-bg-color">
                <v-icon class="mr-1" size="x-small" icon="fa-solid fa-tag"></v-icon>
                {{ item.value }}
              </v-chip>
            </template>
          </v-select>
        </template>
      </Modal>
    </v-dialog>
  </AdminLayout>
</template>