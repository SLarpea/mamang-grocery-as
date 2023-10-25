<script setup>
import { ref } from 'vue'

const props = defineProps({
    mode: String,
    imgSrc: String
});

const classCon = ref(`modal-container ${props.mode}`);

</script>
<template>
    <transition name="modal">
      <div class="modal-mask">
        <div class="modal-wrapper">
          <div :class="classCon">
            <div class="modal-header">
              <slot name="header"> default header </slot>
            </div>

            <div class="modal-preview-image">
                <img v-if="imgSrc" :src="imgSrc" alt="previewImg">
            </div>

            <div class="modal-body">
              <slot name="body"> default body </slot>
            </div>

            <div class="modal-footer">
              <slot name="footer">
                <button class="modal-confirm-button" @click="$emit('submit')">
                  <slot name="button-confirm-text"> Ok </slot>
                </button>

                <button class="modal-cancel-button" @click="$emit('cancel')">
                  <slot name="button-cancel-text"> Cancel </slot>
                </button>
              </slot>
            </div>
          </div>
        </div>
      </div>
    </transition>
</template>