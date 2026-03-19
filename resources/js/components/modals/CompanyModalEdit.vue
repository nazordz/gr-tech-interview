<script setup lang="ts">
import { UploadOutlined } from '@ant-design/icons-vue';
import { router, useForm } from '@inertiajs/vue3';
import { useVModel } from '@vueuse/core';
import type { UploadProps } from 'ant-design-vue';
import {
    Modal as AModal,
    Form as AForm,
    FormItem as AFormItem,
} from 'ant-design-vue';
import type { FormInstance, Rule } from 'ant-design-vue/es/form';
import axios from 'axios';
import type { UnwrapRef } from 'vue';
import { computed, reactive, ref, watch } from 'vue';
import { edit, store, update } from '@/routes/company';
import type { Company } from '@/types/models';

const isLoading = ref(false);

const props = defineProps<{
    companyId: string;
    defaultValue?: boolean;
    modelValue?: boolean;
}>();

const selectedCompany = reactive<Company>({
    id: '',
    name: '',
    email: '',
    website: '',
    logo: '',
    created_at: '',
    updated_at: '',
});

const selectedCompanyId = computed(() => props.companyId);

watch(selectedCompanyId, async (newSelectedCompanyId) => {
    if (newSelectedCompanyId) {
        try {
            isLoading.value = true;
            const { data } = await axios.get<Company>(`/company/${newSelectedCompanyId}/edit`)
            selectedCompany.id = data.id;
            selectedCompany.name = data.name;
            selectedCompany.email = data.email;
            selectedCompany.website = data.website;
            selectedCompany.logo = data.logo;
            selectedCompany.created_at = data.created_at;
            selectedCompany.updated_at = data.updated_at;

            if (data.logo) {
                fileList.value = [
                    {
                        uid: '-1',
                        status: 'done',
                        name: data.logo?.split('/').at(-1) ||'',
                        url: data.logo || '',
                        thumbUrl: data.logo || '',
                    }
                ]
            } else {
                fileList.value = []
            }

            formState.name = data.name;
            formState.email = data.email || '';
            formState.website = data.website || '';
        } catch (error) {
            console.error(error);
        } finally {
            isLoading.value = false;
        }
    }
})

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
        form.submit(update(selectedCompany.id), {
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
const fileList = ref<UploadProps['fileList']>([]);
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
                    list-type="picture"
                    :file-list="formState.logo!.length > 0 ? formState.logo : fileList"
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
