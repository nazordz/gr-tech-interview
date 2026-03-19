<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    Form as AForm,
    FormItem as AFormItem,
    Input as AInput,
    Button as AButton,
} from 'ant-design-vue';
import { notification } from 'ant-design-vue';
import type { UnwrapRef } from 'vue';
import { reactive, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { update } from '@/routes/company';
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
const props = defineProps<{
    data: Company
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company',
        href: '/company',
    },
    {
        title: props.data.name || '-',
        href: '/company/'+props.data.id+'/edit'
    }

];

interface FormState {
    name: string;
    email: string;
    logo: string;
    website: string;
}
const formRef = ref();
const formState: UnwrapRef<FormState> = reactive({
  name: props.data.name || '',
  email: props.data.email || '',
  logo: '',
  website: props.data.website || '',
});
async function onSubmit() {
    const form = useForm({
        name: formState.name,
        email: formState.email,
        website: formState.website,
    })
    form.submit(update(props.data.id))
    openNotification("success", "Data saved");
}
</script>
<template>
    <Head title="Company Edit"/>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div
                class="relative"
            >
                <AForm
                    ref="formRef"
                    :model="formState"
                    :label-col="{span: 3}"
                    :wrapper-col="{span: 6}"
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
                    <AFormItem :wrapper-col="{  offset: 4 }">
                        <AButton type="primary" @click="onSubmit">Submit</AButton>
                    </AFormItem>
                </AForm>
            </div>
        </div>
    </AppLayout>
    <contextHolder/>
</template>
