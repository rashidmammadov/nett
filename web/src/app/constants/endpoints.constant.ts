import { environment } from "../../environments/environment";

const PREFIX = environment.apiPrefix;

export const ENDPOINTS = {

  ACTIVATE: () => `${PREFIX}activate`,

  GAMES: (id:number = null) => !!id ? `${PREFIX}games/${id}` : `${PREFIX}games`,

  LOGIN: () => `${PREFIX}login`,

  REFRESH_USER: () => `${PREFIX}refreshUser`,

  REGISTER: () => `${PREFIX}register`,

  REGIONS: () => 'http://api.ozelden.com/api/v1/data?regions=true',

};
