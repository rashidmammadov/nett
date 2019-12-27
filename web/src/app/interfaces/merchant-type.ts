export interface MerchantType {
    merchantId: number;
    merchantType: string;
    merchantKey?: string;
    identityNumber: string | number;
    taxOffice?: string;
    companyTitle?: string;
    iban?: string;
}
