import { environment } from "../../environments/environment";

const PREFIX = environment.apiPrefix;

export const ENDPOINTS = {

  LOGIN: () => `${PREFIX}login`,

  REFRESH_USER: () => `${PREFIX}refreshUser`,

  REGISTER: () => `${PREFIX}register`,

  REGIONS: () => 'http://api.ozelden.com/api/v1/data?regions=true',

};
