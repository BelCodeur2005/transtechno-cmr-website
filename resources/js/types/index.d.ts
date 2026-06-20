export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface CompanySettings {
    phone_1: string;
    phone_2: string;
    phone_3: string;
    whatsapp: string;
    email: string;
    address_line1: string;
    address_line2: string;
    address_city: string;
    facebook_url: string;
    linkedin_url: string;
    company_name: string;
    rccm: string;
    niu: string;
    host_name: string;
    host_address: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    company: CompanySettings;
};
