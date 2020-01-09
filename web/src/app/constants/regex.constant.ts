export const REGEX = {
    CARD_NUMBER: /^\d{16}$/,
    IDENTITY_NUMBER: /^\d{11}$/,
    USERNAME: /^[a-zA-Z0-9_.]*$/,
    PASSWORD: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,20}$/,
    TAX_NUMBER: /^\d{10}$/
};
