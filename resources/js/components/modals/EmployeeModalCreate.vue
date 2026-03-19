<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useVModel } from '@vueuse/core';
import {
    Modal as AModal,
    Form as AForm,
    FormItem as AFormItem,
    Select as ASelect,
    Spin as ASpin,
} from 'ant-design-vue';
import type { FormInstance, Rule } from 'ant-design-vue/es/form';
import type { DefaultOptionType } from 'ant-design-vue/es/select';
import axios from 'axios';
import { debounce } from 'lodash-es';
import type { UnwrapRef } from 'vue';
import { onMounted, reactive, ref, watch } from 'vue';
import { store } from '@/routes/employee';
import type { Company, Pagination } from '@/types/models';

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
    first_name: string;
    last_name: string;
    company_id: string;
    email: string;
    phone: string;
}
const formRef = ref<FormInstance>();
const formState: UnwrapRef<FormState> = reactive<FormState>({
    first_name: '',
    last_name: '',
    email: '',
    company_id: '',
    phone: '',
});
const rules: Record<string, Rule[]> = {
    first_name: [{ required: true, trigger: 'change', message: 'First name is required' }],
    last_name: [{ required: true, trigger: 'change', message: 'Last name is required' }],
    company_id: [{ required: true, trigger: 'change', message: 'Company is required' }],
    email: [{type: 'email', trigger: 'change', message: 'must format email' }],
};
async function onSubmit() {
    try {
        await formRef.value?.validateFields();

        const form = useForm({
            first_name: formState.first_name,
            last_name: formState.last_name,
            email: formState.email,
            phone: formState.phone,
            company_id: formState.company_id,
        });
        form.submit(store(), {
            onBefore() {
                isLoading.value = true;
            },
            onSuccess() {
                formState.first_name = '';
                formState.last_name = '';
                formState.email = '';
                formState.phone = '';
                formState.company_id = '';
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

async function onOk() {
    onSubmit()
}

interface IState {
    data: DefaultOptionType[];
    fetching: boolean;
}

const state = reactive<IState>({
  data: [],
  fetching: false,
});

const fetchUser = debounce<(value?: string) => Promise<void>>(async (value) => {
  state.data = [];
  state.fetching = true;
    const req = await axios.get<Pagination<Company>>('/company/paginate', {
        params: {
            page: 1,
            per_page: 10,
            search: value
        }
    })
    state.data = req.data.data.map((x) => ({
        label: x.name,
        value: x.id,
    }));
    state.fetching = false;
}, 300);

onMounted(() => {
    fetchUser();
})

</script>

<template>
    <AModal title="Form Employee" close v-model:open="modelValue" @ok="onOk" ok-text="Submit" :confirm-loading="isLoading">
        <AForm
            :rules="rules"
            ref="formRef"
            :model="formState"
            layout="vertical"
            name="form_in_modal"
        >
            <AFormItem ref="company_id" label="Company" name="company_id">
                <ASelect
                    placeholder="Type to search cimpany"
                    v-model:value="formState.company_id"
                    style="width: 100%"
                    :filter-option="false"
                    :not-found-content="state.fetching ? undefined : null"
                    :options="state.data"
                    :show-search="true"
                    @search="fetchUser"
                >
                    <template v-if="state.fetching" #notFoundContent>
                        <ASpin size="small" />
                    </template>
                </ASelect>
            </AFormItem>
            <AFormItem ref="first_name" label="First Name" name="first_name">
                <AInput v-model:value="formState.first_name"></AInput>
            </AFormItem>
            <AFormItem ref="last_name" label="Last Name" name="last_name">
                <AInput v-model:value="formState.last_name"></AInput>
            </AFormItem>
            <AFormItem ref="email" label="Email" name="email">
                <AInput v-model:value="formState.email"></AInput>
            </AFormItem>
            <AFormItem ref="phone" label="Phone" name="phone">
                <AInput v-model:value="formState.phone"></AInput>
            </AFormItem>
        </AForm>
    </AModal>
</template>
