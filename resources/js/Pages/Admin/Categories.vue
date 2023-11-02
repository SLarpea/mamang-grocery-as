<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Admin/Modal.vue";
import { reactive, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    categories: Array,
    message: String
})

const APP_URL = import.meta.env.VITE_APP_URL;
const showModal = ref(false);
const modalMode = ref("default");
const previewImage = ref("");
const search = ref("");
const itemsPerPage = ref(6);
const loading = ref(false);
const form = useForm({
  id: null,
  name: null,
  description: null,
  file: null,
  is_active: true,
});
const actions = reactive({
  add: {
    icon: "fa-regular fa-square-plus",
    method: "post",
    path: "category.store",
    action: "saved",
  },
  edit: {
    icon: "fa-solid fa-pen",
    method: "post",
    path: "category.update",
    action: "updated",
  },
  delete: {
    icon: "fa-solid fa-xmark",
    method: "post",
    path: "category.delete",
    action: "removed",
  },
});
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
    title: "Description",
    align: "start",
    key: "description",
    scrollable: false
  },
  {
    title: "Action",
    align: "start",
    key: "actions",
    sortable: false,
  },
]);

const openModal = (mode, id = null) => {
  if (mode !== "add") setFormValue(id);
  modalMode.value = mode;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  previewImage.value = "";
  form.reset();
};

const onImagePreview = (e) => {
  form.file = e.target.files[0];
  previewImage.value = URL.createObjectURL(form.file);
};

const imgPath = (imageSrc) => {
  return imageSrc.includes("blob") ? imageSrc : APP_URL + imageSrc;
};

const submitForm = (mode) => {
  form.submit(actions[mode].method, route(actions[mode].path), {
    onSuccess: (response) => {
      if (response.props.message === "success") {
        modalMode.value = "default";
        showModal.value = false;
        showToast(form.name.toUpperCase(), actions[mode].action);
        form.reset();
        previewImage.value = "";
      }
    },
  });
};

const setFormValue = (id) => {
  const category = props.categories.find((item) => item.id === id);
  form.id = id;
  form.name = category.name;
  form.description = category.description;
  form.logo_path = category.logo_path;
  previewImage.value = category.logo_path;
};

const showToast = (productName, action) => {
  Swal.fire({
    toast: true,
    position: 'top-end',
    title: `Product ${productName} has been ${action}`,
    icon: "success",
    showConfirmButton: false,
    timer: 3000,
  });
};

</script>
<template>
  <AdminLayout title="Categories">
    <div class="wrapper">
      <v-sheet
        class="categories p-5"
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
            <span class="card-title">Categories</span>
            <span class="card-subtitle">Helps to organize your products.</span>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              size="small"
              color="add-bg-button-color"
              prepend-icon="fa-solid fa-circle-plus"
              @click="openModal('add')"
            >
              add category
            </v-btn>
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col cols="12">
            <v-data-table
              v-model:items-per-page="itemsPerPage"
              :search="search"
              :headers="headers"
              :items-length="categories.length"
              :items="categories"
              :loading="loading"
              class="elevation-1 table"
              item-value="name"
              loading-text="Loading... Please wait"
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
              <!-- <template v-slot:item.price="{ item }">
                <td style="font-weight: 600; color: #388e3c">
                  PHP {{ item.price }}
                </td>
              </template> -->
              <template v-slot:item.thumbnail="{ item }">
                <td>
                  <div class="imageThumb">
                    <img :src="APP_URL + item.logo_path" alt="previewImg" />
                  </div>
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
          <span class="card-title">{{ modalMode }} category</span>
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
              <strong>{{ form.name?.toUpperCase() }}</strong> category?</span
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
          <v-textarea
            v-if="modalMode !== 'delete'"
            v-model="form.description"
            label="Description"
            variant="outlined"
            rows="3"
            row-height="25"
            color="primary-bg-color"
            auto-grow
            shaped
            clearable
          ></v-textarea>
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
          <!-- <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.price"
            label="Price"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.price = null"
            clearable
          ></v-text-field>
          <v-select
            label="Category"
            class="category"
            color="primary-bg-color"
            no-data-text="No data available"
            variant="outlined"
            :items="[]"
            multiple
            chips
            clearable
          >
            <template v-slot:chip="{ item }">
              <v-chip color="primary-bg-color">
                {{ item.value }}
              </v-chip>
            </template>
          </v-select> -->
        </template>
      </Modal>
    </v-dialog>
  </AdminLayout>
</template>