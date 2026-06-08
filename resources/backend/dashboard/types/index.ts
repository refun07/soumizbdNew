import { ICommonQueryParams } from './response';

export interface IFilterData<T = any> {
    data: T;
    filter: ICommonQueryParams;
}
