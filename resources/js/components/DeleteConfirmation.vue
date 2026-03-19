<script setup lang="ts">
import { useVModel } from '@vueuse/core';
import {
    Modal as AModal
} from 'ant-design-vue';

const props = defineProps<{
  defaultValue?: boolean
  modelValue?: boolean
}>()

const emits = defineEmits<{
  (e: "update:modelValue", payload: boolean): void;
}>()

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

</script>
<template>
    <AModal
        v-model:open="modelValue"
        title="Confirmation"
        okText="Confirm"
        cancelText="Cancel"
    >
        <div>
            Are you sure?
        </div>
    </AModal>
</template>
