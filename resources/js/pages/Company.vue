<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Table as ATable,
    Button as AButton,
    Image as AImage,
    Popconfirm as APopConfirm,
    notification,
} from 'ant-design-vue';
import type { TableProps } from 'ant-design-vue';
import type { ColumnsType } from 'ant-design-vue/es/table';
import type { SorterResult } from 'ant-design-vue/es/table/interface';
import axios from 'axios';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';
import { usePagination } from 'vue-request';
import CompanyModalCreate from '@/components/modals/CompanyModalCreate.vue';
import CompanyModalEdit from '@/components/modals/CompanyModalEdit.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { deleteMethod } from '@/routes/company';
import type { BreadcrumbItem } from '@/types';
import type { Company, Pagination } from '@/types/models';
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
];

const columns: ColumnsType = [
    {
        title: 'Company Name',
        dataIndex: 'name',
        key: 'name',
        sorter: true,
    },
    {
        title: 'Email',
        dataIndex: 'email',
        key: 'email',
        sorter: true,
    },
    {
        title: 'Website',
        dataIndex: 'website',
        key: 'website',
        sorter: true,
    },
    {
        title: 'Logo',
        dataIndex: 'logo',
        key: 'logo',
    },
    {
        title: 'Updated at',
        dataIndex: 'updated_at',
        key: 'updated_at',
        sorter: true,
    },
    {
        title: 'Action',
        dataIndex: 'action',
    },
];

type APIParams = {
    per_page: number;
    page?: number;
    sort_field?: string;
    sort_order?: string;
    [key: string]: any;
};
const queryData = async (params: APIParams) => {
    const { data } = await axios.get<Pagination<Company>>('/company/paginate', {
        params,
    });

    return data;
};

const {
    data: dataSource,
    run,
    loading,
} = usePagination(queryData, {
    pagination: {
        currentKey: 'page',
        pageSizeKey: 'per_page',
    },
});

const pagination = computed(() => ({
    total: dataSource.value?.total,
    current: dataSource.value?.current_page,
    pageSize: dataSource.value?.per_page,
}));

const handleTableChange: TableProps<Pagination<Company>>['onChange'] = (
    pagination,
    filters,
    sorter,
    extra,
) => {
    const sorting = sorter as SorterResult<Pagination<Company>>;
    const field = sorting.field as string;
    const order = sorting.order == 'ascend' ? 'asc' : 'desc';
    run({
        per_page: pagination?.pageSize || 0,
        page: pagination?.current,
        sort_field: field,
        sort_order: order,
        ...filters,
    });
};

function createPage() {
    isModalCreateVisible.value = !isModalCreateVisible.value;
}

async function confirmDelete(companyId: string) {
    router.delete(deleteMethod(companyId), {
        onSuccess() {
            run({
                per_page: 10,
                page: 1,
            });
        },
    });
}

const isModalCreateVisible = ref(false);
const isModalEditVisible = ref(false);
const editCompanyId = ref('');
function onEditCompany(companyId: string) {
    editCompanyId.value = companyId;
    isModalEditVisible.value = true
}

function onSuccessForm() {
    editCompanyId.value = '';
    run({
        per_page: 10,
        page: 1,
    });
    openNotification('success', 'Data saved');
}
</script>

<template>
    <Head title="Company" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div>
                <AButton type="primary" @click="createPage">Create</AButton>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <ATable
                    :rowKey="(record) => record.id"
                    :dataSource="dataSource?.data"
                    :columns="columns"
                    :pagination="pagination"
                    :loading="loading"
                    @change="handleTableChange"
                >
                    <template #bodyCell="{ column, text, record }">
                        <template v-if="column.dataIndex == 'website'">
                            <AButton type="link" :href="text" target="_blank">{{
                                text
                            }}</AButton>
                        </template>
                        <template v-if="column.dataIndex == 'updated_at'">
                            {{ dayjs(text).format('YYYY-MM-DD HH:mm') }}
                        </template>
                        <template
                            v-if="column.dataIndex == 'logo' && record.logo"
                        >
                            <AImage :width="100" :src="record.logo" />
                        </template>
                        <template v-if="column.dataIndex == 'action'">
                            <div class="flex gap-2">
                                <!-- <AButton type="primary" :href="'/company/' + record.id + '/edit'" target="_blank" >Edit</AButton > -->
                                <AButton
                                    type="primary"
                                    @click="onEditCompany(record.id)"
                                >
                                    Edit
                                </AButton>
                                <APopConfirm
                                    title="Are you sure?"
                                    @confirm="() => confirmDelete(record.id)"
                                >
                                    <AButton type="text" danger>Delete</AButton>
                                </APopConfirm>
                            </div>
                        </template>
                    </template>
                </ATable>
            </div>
        </div>
    </AppLayout>
    <CompanyModalCreate v-model="isModalCreateVisible" @success="onSuccessForm" />
    <CompanyModalEdit v-model="isModalEditVisible" @success="onSuccessForm" :companyId="editCompanyId"/>
    <contextHolder />
</template>
