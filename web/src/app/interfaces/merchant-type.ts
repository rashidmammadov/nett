export interface MerchantType {
    merchantId: number;
    merchantType: string;
    merchantKey?: string;
    identityNumber: string | number;
    taxOffice?: string;
    taxNumber?: string;
    companyTitle?: string;
    iban?: string;
}
