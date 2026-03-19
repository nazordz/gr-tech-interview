<script setup lang="ts">
import { UploadOutlined } from '@ant-design/icons-vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import type {
    UploadProps
, NotificationPlacement} from 'ant-design-vue';
import {
    Form as AForm,
    FormItem as AFormItem,
    Input as AInput,
    Button as AButton,
    Upload as AUpload,
} from 'ant-design-vue';
import { notification } from 'ant-design-vue';
import type { UnwrapRef } from 'vue';
import { reactive, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store, update } from '@/routes/company';
import type { BreadcrumbItem } from '@/types';
import type { Company } from '@/types/models';

const [api, contextHolder] = notification.useNotification();

const openNotification = (message: string, description: string) => {
  api.success({
    message,
    description,
    placement: 'bottomRight',
  });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company',
        href: '/company',
    },
    {
        title: 'Create',
        href: '/company/create',
    },
];

interface FormState {
    name: string;
    email: string;
    website: string;
    logo: UploadProps['fileList'];
}
const formRef = ref();
const formState: UnwrapRef<FormState> = reactive<FormState>({
    name: '',
    email: '',
    logo: [],
    website: '',
});
async function onSubmit() {
    const uploadItem = formState.logo?.[0] as unknown as File;
    // const file = uploadItem?.originFileObj;

    const form = useForm({
        name: formState.name,
        email: formState.email,
        website: formState.website,
        logo: uploadItem ?? null,
    });
    form.submit(store(), {
        onSuccess() {
            openNotification("success", "Data saved");
        },
    })
}

const handleRemove: UploadProps['onRemove'] = file => {
    const index = formState.logo?.indexOf(file);
    const newFileList = formState.logo?.slice();
    newFileList?.splice(index!, 1);
    formState.logo = newFileList;
};

const beforeUpload: UploadProps['beforeUpload'] = file => {
    formState.logo = [file];

    return false;
};

</script>
<template>
    <Head title="Company Edit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-4 p-4">
            <div class="relative">
                <AForm
                    ref="formRef"
                    :model="formState"
                    :label-col="{ span: 3 }"
                    :wrapper-col="{ span: 6 }"
                >
                    <AFormItem ref="name" label="Company Name" name="name">
                        <AInput v-model:value="formState.name"></AInput>
                    </AFormItem>
                    <AFormItem ref="email" label="Company Email" name="email">
                        <AInput v-model:value="formState.email"></AInput>
                    </AFormItem>
                    <AFormItem
                        ref="website"
                        label="Company Website"
                        name="website"
                    >
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
                    <AFormItem class="text-right">
                        <AButton type="primary" @click="onSubmit"
                            >Submit</AButton
                        >
                    </AFormItem>
                </AForm>
            </div>
        </div>
    </AppLayout>
    <contextHolder />
</template>
