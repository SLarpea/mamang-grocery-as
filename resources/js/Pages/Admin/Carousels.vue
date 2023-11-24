<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Admin/Modal.vue";
import { onMounted, reactive, ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const APP_URL = import.meta.env.VITE_APP_URL;

const props = defineProps({
  carousels: Array,
  message: String,
});

const showModal = ref(false);
const modalMode = ref("default");
const search = ref("");
const itemsPerPage = ref(6);
const loading = ref(false);
const sliderHeaderField = ref("");
const form = useForm({
  id: null,
  name: null,
  type: null,
  dots: null,
  arrows: null,
  accessibilty: null,
  rows: null,
  slides_to_show: null,
  slides_per_row: null,
  autoplay_speed: null,
  pause_on_hover: null,
  min_slide: null,
  max_slide: null,
});
const headers = ref([
  {
    title: "Name",
    align: "start",
    key: "name",
    width: "10rem",
  },
  {
    title: "Type",
    align: "start",
    key: "type",
    width: "10rem",
  },
  {
    title: "Dots",
    align: "start",
    key: "dots",
    width: "10rem",
  },
  {
    title: "Arrows",
    align: "start",
    key: "arrows",
    width: "10rem",
  },
  {
    title: "Accessibility",
    align: "start",
    key: "accessibility",
    width: "12rem",
  },
  {
    title: "Rows",
    align: "start",
    key: "rows",
    width: "10rem",
  },
  {
    title: "Slides To Show",
    align: "start",
    key: "slides_to_show",
    width: "12rem",
  },
  {
    title: "Slides Per Row",
    align: "start",
    key: "slides_per_row",
    width: "12rem",
  },
  {
    title: "Autoplay Speed",
    align: "start",
    key: "autoplay_speed",
    width: "12rem",
  },
  {
    title: "Pause On Hover",
    align: "start",
    key: "pause_on_hover",
    width: "13rem",
  },
  {
    title: "Min Slide",
    align: "start",
    key: "min_slide",
    width: "10rem",
  },
  {
    title: "Max Slide",
    align: "start",
    key: "max_slide",
    width: "10rem",
  },
  {
    title: "Action",
    align: "center",
    key: "actions",
    sortable: false,
  },
]);
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

const openModal = (mode, id = null) => {
  if (mode !== "add") setFormValue(id);
  modalMode.value = mode;
  showModal.value = true;
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

const setFormValue = (id) => {
  const carousel = props.carousels.find((item) => item.id === id);
  form.id = id;
  form.name = carousel.name;
  form.type = carousel.type;
  form.dots = carousel.dots;
  form.arrows = carousel.arrows;
  form.accessibilty = carousel.accessibilty;
  form.rows = carousel.rows;
  form.slides_to_show = carousel.slides_to_show;
  form.slides_per_row = carousel.slides_per_row;
  form.autoplay_speed = carousel.autoplay_speed;
  form.center_padding = carousel.center_padding;
  form.pause_on_hover = carousel.pause_on_hover;
  form.min_slide = carousel.min_slide;
  form.max_slide = carousel.max_slide;
};

const submitForm = (mode) => {
  form.submit(actions[mode].method, route(actions[mode].path), {
    onSuccess: (response) => {
      if (response.props.message === "success") {
        modalMode.value = "default";
        showModal.value = false;
        showToast(
          `Carousel ${form.name.toUpperCase()} has been ${actions[mode].action}`
        );
        form.reset();
      }
    },
  });
};

const addSliderHeader = () => {
  if (
    sliderHeaderField &&
    sliderHeaderField.length !== 0 &&
    !form.slider_headers.includes(sliderHeaderField.value)
  ) {
    form.slider_headers.push(sliderHeaderField.value);
    sliderHeaderField.value = "";
  }
};

const computeToSecond = (milliseconds) => {
  return milliseconds / 1000;
};

const pluralization = (milliseconds, word) => {
  return computeToSecond(milliseconds) > 1 ? word + "s" : word;
};

const onSliderHeadersChange = (newVal) => {
  console.log(newVal);
};
</script>
<template>
  <AdminLayout title="Carousels">
    <div class="wrapper">
      <v-alert
        icon="$info"
        title="Tip:"
        text="To add a slide collection, just clicked a carousel name."
        border="start"
        border-color="#6a1b9a"
        class="mb-3"
      ></v-alert>
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
            <span class="card-title">Carousels</span>
            <span class="card-subtitle"
              >Manage your desired carousel for homepage.</span
            >
          </v-col>
          <v-col cols="1" class="text-right">
            <!-- <v-btn
              size="small"
              color="add-bg-button-color"
              prepend-icon="fa-solid fa-circle-plus"
              @click="openModal('add')"
            >
              add carousel
            </v-btn> -->
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col cols="12">
            <v-data-table
              :headers="headers"
              :items="carousels"
              item-value="name"
              v-model:items-per-page="itemsPerPage"
              :items-length="carousels.length"
              :loading="loading"
              class="elevation-1 carousel-table"
              loading-text="Loading... Please wait"
              no-data-text="No data available"
            >
              <template v-slot:item.name="{ item }">
                <Link
                  v-if="item.type !== 'products'"
                  :href="
                    route('carousels.index', {
                      current: item.type,
                    })
                  "
                  >{{ item.name }}</Link
                >
                <td v-if="item.type === 'products'">{{ item.name }}</td>
              </template>
              <template v-slot:item.autoplay_speed="{ item }">
                <td>
                  {{ computeToSecond(item.autoplay_speed) }}
                  {{ pluralization(item.autoplay_speed, "second") }}
                </td>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-icon
                  icon="fa-solid fa-pen"
                  size="small"
                  class="me-2"
                  color="edit-bg-button-color"
                  @click="openModal('edit', item.id)"
                ></v-icon>
                <!-- <v-icon
                  icon="fa-solid fa-trash"
                  size="small"
                  color="delete-bg-button-color"
                  @click.prevent="openModal('delete', item.id)"
                ></v-icon> -->
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-sheet>
    </div>
    <v-dialog class="dialog" v-model="showModal" width="50rem" persistent>
      <Modal
        :mode="modalMode"
        @submit="submitForm(modalMode)"
        @cancel="closeModal()"
      >
        <template #header>
          <span class="card-title">{{ modalMode }} carousel</span>
        </template>

        <template #body>
          <div v-if="modalMode === 'delete'">
            <span class="deletePromptTitle"
              >Are you sure to delete this
              <strong>{{ form.name?.toUpperCase() }}</strong> carousel?</span
            >
            <span class="deletePromptSubTitle"
              >You won't be able to revert this!</span
            >
          </div>
          <v-text-field
            v-model="form.name"
            label="Name"
            variant="outlined"
            color="primary-bg-color"
            class="mb-2"
            @click:clear="form.name = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-model="form.type"
            label="Type"
            variant="outlined"
            color="primary-bg-color"
            class="mb-2"
            @click:clear="form.type = null"
            clearable
          ></v-text-field>
          <v-radio-group class="mb-2" label="Pagination Dots" v-model="form.dots" inline>
            <v-radio color="primary-bg-color" label="True" value="true"></v-radio>
            <v-radio color="primary-bg-color" label="False" value="false"></v-radio>
          </v-radio-group>
          <v-radio-group class="mb-2" label="Arrows" v-model="form.arrows" inline>
            <v-radio color="primary-bg-color" label="True" value="true"></v-radio>
            <v-radio color="primary-bg-color" label="False" value="false"></v-radio>
          </v-radio-group>
          <v-radio-group class="mb-2" label="Accessibility" v-model="form.accessibility" inline>
            <v-radio color="primary-bg-color" label="True" value="true"></v-radio>
            <v-radio color="primary-bg-color" label="False" value="false"></v-radio>
          </v-radio-group>
          <v-radio-group class="mb-2" label="Pause on Hover" v-model="form.pause_on_hover" inline>
            <v-radio color="primary-bg-color" label="True" value="true"></v-radio>
            <v-radio color="primary-bg-color" label="False" value="false"></v-radio>
          </v-radio-group>
          <v-text-field
            v-model="form.rows"
            type="number"
            label="Rows"
            variant="outlined"
            color="primary-bg-color"
            class="mb-2"
            @click:clear="form.rows = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-model="form.slides_to_show"
            type="number"
            label="Slides to show"
            variant="outlined"
            color="primary-bg-color"
            hint="7 is Maximum"
            class="mb-2"
            @click:clear="form.slides_to_show = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-model="form.slides_per_row"
            type="number"
            label="Slides per row"
            variant="outlined"
            color="primary-bg-color"
            class="mb-2"
            @click:clear="form.slides_per_row = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-model="form.autoplay_speed"
            type="number"
            label="Autoplay Speed"
            variant="outlined"
            color="primary-bg-color"
            hint="1000 is equal to 1 second"
            class="mb-2"
            @click:clear="form.autoplay_speed = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-model="form.min_slide"
            type="number"
            label="Minimum Slide"
            variant="outlined"
            color="primary-bg-color"
            class="mb-2"
            @click:clear="form.min = null"
            clearable
          ></v-text-field>
          <v-text-field
            v-model="form.max_slide"
            type="number"
            label="Maximum Slide"
            variant="outlined"
            color="primary-bg-color"
            class="mb-2"
            @click:clear="form.max = null"
            clearable
          ></v-text-field>
        </template>
      </Modal>
    </v-dialog>
  </AdminLayout>
</template>