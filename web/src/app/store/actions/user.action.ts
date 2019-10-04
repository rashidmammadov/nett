import { createAction, props } from '@ngrx/store';
import { UserType } from '../../interfaces/user-type';

export const set = createAction('[User] Set', props<{user: UserType}>());
