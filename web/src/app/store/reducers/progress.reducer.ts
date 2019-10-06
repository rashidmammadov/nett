import { createReducer, on } from '@ngrx/store';
import { loading, loaded } from '../actions/progress.action';

export const initialState: boolean = false;

export const progressReducer = createReducer(initialState,
  on(loading, () => true),
  on(loaded, () => false)
);
