<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Admin/Modal.vue";
import { computed, onMounted, reactive, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

onMounted(() => {
  //   console.log(JSON.parse(props.carousel[0].data) ?? []);
  //   const headers = [
  //     ...JSON.parse(props.carousel[0].slider_headers),
  //     {
  //       title: "Action",
  //       align: "start",
  //       key: "actions",
  //       sortable: false,
  //     },
  //   ];
});

const props = defineProps({
  carousel: Object,
  message: String,
});

const carousel = props.carousel[0];
const slides = JSON.parse(carousel.data);
const carouselSlides = JSON.parse(props.carousel[0].data) ?? [];
const showModal = ref(false);
const modalMode = ref("default");
const search = ref("");
const itemsPerPage = ref(6);
const loading = ref(false);
const previewImage = ref(null);
const selectedSlideIdx = ref(null);
const form = useForm(
  carousel.type === "promo"
    ? {
        text: null,
        pill_text: null,
      }
    : {
        file: null,
        image_path: null,
      }
);
const headers = computed(() => {
  var defaultHeaders = [
    {
      title: "Sort Order",
      align: "start",
      key: "sort_order",
    },
    {
      title: "Action",
      align: "start",
      key: "actions",
      sortable: false,
    },
  ];
  if (carousel.type === "promo") {
    var arrayHeaders = [
      {
        title: "Text",
        align: "start",
        key: "text",
        sortable: false,
      },
      {
        title: "Pill Text",
        align: "start",
        key: "pill_text",
        sortable: false,
      },
    ];
  } else {
    var arrayHeaders = [
      {
        title: "Image Path",
        align: "start",
        key: "image_path",
        sortable: false,
      },
    ];
  }
  return [...arrayHeaders, ...defaultHeaders];
});

const actions = reactive({
  add: {
    icon: "fa-regular fa-square-plus",
    method: "post",
    path: "carousel.store",
    action: "saved",
  },
  edit: {
    icon: "fa-solid fa-pen",
    method: "post",
    path: "carousel.update",
    action: "updated",
  },
  delete: {
    icon: "fa-solid fa-xmark",
    method: "post",
    path: "carousel.delete",
    action: "removed",
  },
});

const openModal = (mode, idx = null) => {
  if (mode !== "add") setFormValue(idx);
  modalMode.value = mode;
  showModal.value = true;
  selectedSlideIdx.value = idx;
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
};

const showToast = (title) => {
  Swal.fire({
    toast: true,
    position: "top-end",
    title: title,
    icon: "success",
    showConfirmButton: false,
    timer: 3000,
  });
};

const setFormValue = (idx) => {
  const slide = JSON.parse(slides[idx]);
  if (carousel.type === "promo") {
    (form.id = id), (form.text = slide.text);
    form.pill_text = slide.pill_text;
  } else {
    form.image_path = slide.image_path;
  }
};

const submitForm = (mode, idx) => {
  form.submit(actions[mode].method, route(actions[mode].path), {
    onSuccess: (response) => {
      if (response.props.message === "success") {
        modalMode.value = "default";
        showModal.value = false;
        showToast(
          `${carousel.name} slide ${idx + 1} has been ${actions[mode].action}`
        );
        form.reset();
      }
    },
  });
};

const onImagePreview = (e) => {
  form.file = e.target.files[0];
  previewImage.value = URL.createObjectURL(form.file);
};

const breadCrumbItems = (curr) => {
  return [
    {
      title: "Carousels",
      disabled: false,
      href: route("carousels.index"),
    },
    {
      title: curr,
      disabled: true,
    },
  ];
};

const imgPath = (imageSrc) => {
  return imageSrc.includes("blob") ? imageSrc : APP_URL + imageSrc;
};

</script>
<template>
  <AdminLayout title="Carousel">
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
        <v-row no-gutters>
          <v-breadcrumbs :items="breadCrumbItems(carousel.name)" divider="•">
            <template v-slot:prepend>
              <v-icon size="small" icon="fa-solid fa-horse"></v-icon>
            </template>
          </v-breadcrumbs>
        </v-row>
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
            <span class="card-title"
              >Carousel {{ carousel.name }} ({{
                carouselSlides?.length ?? 0
              }}/{{ carousel.max_slide }})</span
            >
            <span class="card-subtitle">Manage slides in these carousel.</span>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              size="small"
              color="add-bg-button-color"
              prepend-icon="fa-solid fa-circle-plus"
              @click="openModal('add')"
            >
              add slide
            </v-btn>
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col cols="12">
            <v-data-table
              :headers="headers"
              :items="carouselSlides"
              item-value="name"
              v-model:items-per-page="itemsPerPage"
              :items-length="carouselSlides.length"
              :loading="loading"
              class="elevation-1 table"
              loading-text="Loading... Please wait"
              no-data-text="No data available"
            >
              <template v-slot:item.name="{ item }">
                <Link
                  :href="
                    route('carousels.index', {
                      current: item.type,
                    })
                  "
                  >{{ item.name }}</Link
                >
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
            </v-data-table>
          </v-col>
        </v-row>
      </v-sheet>
    </div>
    <v-dialog class="dialog" v-model="showModal" width="50rem" persistent>
      <Modal
        :mode="modalMode"
        @submit="submitForm(modalMode, selectedSlideIdx)"
        @cancel="closeModal()"
      >
        <template #header>
          <span class="card-title">{{ modalMode }} slide</span>
        </template>

        <template #body>
          <div v-if="modalMode === 'delete'">
            <span class="deletePromptTitle"
              >Are you sure to delete this
              <strong>{{ selectedSlideIdx + 1 }}</strong> slide?</span
            >
            <span class="deletePromptSubTitle"
              >You won't be able to revert this!</span
            >
          </div>
          <div v-if="modalMode !== 'delete'">
            <div v-if="carousel.type === 'promo'">
              <v-text-field
                v-model="form.text"
                label="Text"
                variant="outlined"
                color="primary-bg-color"
                @click:clear="form.text = null"
                clearable
              ></v-text-field>
              <v-text-field
                v-model="form.pill_text"
                label="Pill Text"
                variant="outlined"
                color="primary-bg-color"
                @click:clear="form.pill_text = null"
                clearable
              ></v-text-field>
            </div>
            <div v-if="carousel.type !== 'promo'">
              <v-img
                v-if="previewImage"
                class="bg-white mb-5 mx-auto"
                width="300"
                :aspect-ratio="1"
                :src="imgPath(previewImage)"
                cover
              ></v-img>
              <v-file-input
                label="Select Image"
                variant="outlined"
                color="primary-bg-color"
                prepend-icon=""
                density="compact"
                @change="onImagePreview"
                @click:clear="previewImage = ''"
                clearable
              ></v-file-input>
            </div>
          </div>
          <!-- <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.name"
            label="Name"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.name = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.type"
            label="Type"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.type = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.min_slide"
            type="number"
            label="Minimum Slide"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.min = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-if="modalMode !== 'delete'"
            v-model="form.max_slide"
            type="number"
            label="Maximum Slide"
            variant="outlined"
            color="primary-bg-color"
            @click:clear="form.max = null"
            clearable
          ></v-text-field> -->
        </template>
      </Modal>
    </v-dialog>
  </AdminLayout>
</template>