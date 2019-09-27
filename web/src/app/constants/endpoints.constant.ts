import { environment } from "../../environments/environment";

const PREFIX = environment.apiPrefix;

export const ENDPOINTS = {

  LOGIN: () => `${PREFIX}login`,

};
