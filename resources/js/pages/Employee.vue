<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import type { TableProps } from 'ant-design-vue';
import {
    Table as ATable,
    Button as AButton,
    Popconfirm as APopConfirm,
    notification,
} from 'ant-design-vue';
import type { SorterResult } from 'ant-design-vue/es/table/interface';
import axios from 'axios';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';
import { usePagination } from 'vue-request';
import CompanyModalEdit from '@/components/modals/CompanyModalEdit.vue';
import EmployeeModalCreate from '@/components/modals/EmployeeModalCreate.vue';
import EmployeeModalEdit from '@/components/modals/EmployeeModalEdit.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { deleteMethod } from '@/routes/employee';
import type { BreadcrumbItem } from '@/types';
import type { Employee, Pagination } from '@/types/models';
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
        title: 'Employee',
        href: '/employee',
    },
];
const columns = [
    {
        title: 'Company Name',
        dataIndex: 'company',
        key: 'company.name',
    },
    {
        title: 'Full Name',
        dataIndex: 'full_name',
    },
    {
        title: 'Email',
        dataIndex: 'email',
        key: 'email',
        sorter: true,
    },
    {
        title: 'Phone',
        dataIndex: 'phone',
        key: 'phone',
        sorter: true,
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
    const { data } = await axios.get<Pagination<Employee>>(
        '/employee/paginate',
        { params },
    );

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

const handleTableChange: TableProps['onChange'] = (
    pagination,
    filters,
    sorter,
    extra,
) => {
    const sorting = sorter as SorterResult<Pagination<Employee>>
    const field = sorting.field as string
    const order = sorting.order == 'ascend' ? 'asc' : 'desc';
    run({
        per_page: pagination?.pageSize || 0,
        page: pagination?.current,
        sort_field: field,
        sort_order: order,
        ...filters,
    });
};

async function confirmDelete(employeeId: string) {
    router.delete(deleteMethod(employeeId), {
        onSuccess() {
            run({
                per_page: 10, page: 1
            });
            openNotification('Success', 'Record deleted')
        }
    })
}
const isCompanyModalEditVisible = ref(false);
const editCompanyId = ref('');
function onEditCompany(companyId: string) {
    editCompanyId.value = companyId;
    isCompanyModalEditVisible.value = true
}

const isEmployeeModalEditVisible = ref(false);
const editEmployeeId = ref('');
function onEditEmployee(employeeId: string) {
    editEmployeeId.value = employeeId;
    isEmployeeModalEditVisible.value = true
}

function onSuccessForm() {
    editCompanyId.value = '';
    run({
        per_page: 10,
        page: 1,
    });
    openNotification('success', 'Data saved');
}

const isModalCreateVisible = ref(false);
function toggleModalCreate() {
    isModalCreateVisible.value = !isModalCreateVisible.value;
}

</script>

<template>
    <Head title="Company" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div>
                <AButton type="primary" @click="toggleModalCreate">Create</AButton>
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
                        <template v-if="column.dataIndex == 'company'">
                            <AButton
                                type="link"
                                @click="onEditCompany(record.company.id)">
                            {{ record.company.name }}
                        </AButton>
                        </template>
                        <template v-if="column.dataIndex == 'full_name'">
                            {{ record.first_name }} {{ record.last_name }}
                        </template>
                        <template v-if="column.dataIndex == 'updated_at'">
                            {{ dayjs(text).format('YYYY-MM-DD HH:mm') }}
                        </template>
                        <template v-if="column.dataIndex == 'action'">
                            <div class="flex gap-2">
                                <AButton
                                    @click="onEditEmployee(record.id)"
                                >Edit</AButton>
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
    <CompanyModalEdit v-model="isCompanyModalEditVisible" @success="onSuccessForm" :companyId="editCompanyId"/>
    <EmployeeModalCreate v-model="isModalCreateVisible" @success="onSuccessForm"/>
    <EmployeeModalEdit v-model="isEmployeeModalEditVisible" @success="onSuccessForm" :employeeId="editEmployeeId"/>
    <contextHolder />
</template>
