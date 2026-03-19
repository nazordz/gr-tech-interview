<script setup lang="ts">
// TODO Failed to submit with logo
import { UploadOutlined } from '@ant-design/icons-vue';
import { useForm } from '@inertiajs/vue3';
import { useVModel } from '@vueuse/core';
import type { UploadProps } from 'ant-design-vue';
import {
    Modal as AModal,
    Form as AForm,
    FormItem as AFormItem,
} from 'ant-design-vue';
import type { FormInstance, Rule } from 'ant-design-vue/es/form';
import type { UnwrapRef } from 'vue';
import { reactive, ref } from 'vue';
import { store } from '@/routes/company';

const isLoading = ref(false);

const props = defineProps<{
    defaultValue?: boolean;
    modelValue?: boolean;
}>();

const emits = defineEmits<{
    'update:modelValue': [payload: boolean];
    'success': []
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

interface FormState {
    name: string;
    email: string;
    website: string;
    logo: UploadProps['fileList'];
}
const formRef = ref<FormInstance>();
const formState: UnwrapRef<FormState> = reactive<FormState>({
    name: '',
    email: '',
    logo: [],
    website: '',
});
const rules: Record<string, Rule[]> = {
    name: [{ required: true, trigger: 'change', message: 'Company name is required' }],
    email: [{type: 'email', trigger: 'change', message: 'must format email' }],
};
async function onSubmit() {
    try {
        await formRef.value?.validateFields();

        const uploadItem = formState.logo?.[0] as unknown as File;
        const form = useForm({
            name: formState.name,
            email: formState.email,
            website: formState.website,
            logo: uploadItem ?? null,
        });
        form.submit(store(), {
            onBefore() {
                isLoading.value = true;
            },
            onSuccess() {
                formState.name = '';
                formState.email = '';
                formState.logo = [];
                formState.website = '';
                emits('success')
                modelValue.value = false;
            },
            onFinish() {
                isLoading.value = false;
            }
        });

    } catch (error) {
        console.log('validation failed');
    }
}

const handleRemove: UploadProps['onRemove'] = (file) => {
    const index = formState.logo?.indexOf(file);
    const newFileList = formState.logo?.slice();
    newFileList?.splice(index!, 1);
    formState.logo = newFileList;
};

const beforeUpload: UploadProps['beforeUpload'] = (file) => {
    formState.logo = [file];

    return false;
};

async function onOk() {
    onSubmit()
}
</script>

<template>
    <AModal title="Form Company" close v-model:open="modelValue" @ok="onOk" ok-text="Submit" :confirm-loading="isLoading">
        <AForm
            :rules="rules"
            ref="formRef"
            :model="formState"
            layout="vertical"
            name="form_in_modal"
        >
            <AFormItem ref="name" label="Company Name" name="name">
                <AInput v-model:value="formState.name"></AInput>
            </AFormItem>
            <AFormItem ref="email" label="Company Email" name="email">
                <AInput v-model:value="formState.email"></AInput>
            </AFormItem>
            <AFormItem ref="website" label="Company Website" name="website">
                <AInput v-model:value="formState.website"></AInput>
            </AFormItem>
            <AFormItem
                ref="logo"
                label="Company Logo"
                name="logo"
                extra="file types: images/*"
            >
                <AUpload
                    accept="image/*"
                    name="logo"
                    :file-list="formState.logo"
                    list-type="picture"
                    :before-upload="beforeUpload"
                    @remove="handleRemove"
                    :max-count="1"
                >
                    <a-button>
                        <template #icon><UploadOutlined /></template>
                        Click to upload
                    </a-button>
                </AUpload>
            </AFormItem>
        </AForm>
    </AModal>
</template>
