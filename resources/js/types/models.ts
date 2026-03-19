interface Link {
    url: string|null;
    label: string;
    page: number|null;
    active: boolean;
}

export interface Pagination<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Link[];
    next_page_url: string|null;
    path: string;
    per_page: number;
    prev_page_url: string|null;
    to: number;
    total: number;
}

export type Company = {
    id: string;
    name: string;
    email?: string | null;
    logo?: string | null;
    website?: string | null;
    employees?: Employee[];
    created_at: string;
    updated_at: string;
};
export type Employee = {
    id: string;
    first_name: string;
    last_name: string;
    company_id: string;
    company?: Company;
    email?: string | null;
    phone?: string | null;
    created_at: string;
    updated_at: string;
};
export type Permission = {
    uuid: string;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
};
export type Role = {
    uuid: string;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
};
export type User = {
    id: string;
    name: string;
    email: string;
    email_verified_at?: string | null;
    password: string;
    remember_token?: string | null;
    created_at: string;
    updated_at: string;
};

