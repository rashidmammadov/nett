import { environment } from "../../environments/environment";

const PREFIX = environment.apiPrefix;

export const ENDPOINTS = {

  LOGIN: () => `${PREFIX}login`,

  REGISTER: () => `${PREFIX}register`,

  REGIONS: () => 'http://api.ozelden.com/api/v1/data?regions=true',

};
