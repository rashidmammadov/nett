import { createReducer, on } from '@ngrx/store';
import { set } from '../actions/user.action';
import { UserType } from '../../interfaces/user-type';

export const initialState: UserType = null;

export function userReducer(state, action) {
    return createReducer(initialState,
        on(set, () => action.user)
    )(state, action);
}
