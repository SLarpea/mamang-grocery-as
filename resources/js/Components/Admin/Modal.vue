<script setup>
import { ref } from "vue";

const APP_URL = import.meta.env.VITE_APP_URL;

const props = defineProps({
  mode: String,
  imgSrc: String,
});

const classCon = ref(`modal-container ${props.mode}`);

const imgPath = () => {
  return props.imgSrc.includes("blob") ? props.imgSrc : APP_URL + props.imgSrc;
};

const submitBtnColor = (mode) => {
  switch (mode) {
    case 'add':
      var color = 'add-bg-button-color';
      break;

    case 'edit':
      var color = 'edit-bg-button-color';
      break;

    case 'delete':
      var color = 'delete-bg-button-color';
      break;
  }

  return color;
};

const submitBtnIcon = (mode) => {
  switch (mode) {
    case 'add':
      var icon = 'fa-solid fa-circle-plus';
      break;

    case 'edit':
      var icon = 'fa-solid fa-pen';
      break;

    case 'delete':
      var icon = 'fa-solid fa-trash';
      break;
  }

  return icon;
};

const submitBtnText = (mode) => {
  switch (mode) {
    case 'add':
      var text = 'add';
      break;

    case 'edit':
      var text = 'edit';
      break;

    case 'delete':
      var text = 'yes, delete it!';
      break;
  }

  return text;
};

</script>

<template>
  <v-card elevation="2" style="border-top: .3rem solid #6A1B9A;padding-top: .8rem;">
    <v-card-title>
      <slot name="header"> Default Header </slot>
    </v-card-title>
    <v-card-text>
      <slot name="body"> Default Text </slot>
    </v-card-text>
    <v-divider style="color: #6A1B9A !important;"></v-divider>
    <v-card-actions>
      <slot name="footer">
        <v-spacer></v-spacer>
        <v-btn :prepend-icon="submitBtnIcon(mode)" :color="submitBtnColor(mode)" @click.prevent="$emit('submit')">
          <slot name="confirm-btn-text"> {{ submitBtnText(mode) }} </slot>
        </v-btn>
        <v-btn color="cancel-bg-button-color" @click.prevent="$emit('cancel')">
          <slot name="cancel-btn-text"> Cancel </slot>
        </v-btn>
      </slot>
    </v-card-actions>
  </v-card>
</template>