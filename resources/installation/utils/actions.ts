import {
    IAdminConfig,
    IChecklist,
    IDatabaseInfo,
    IMigrationResponse,
} from '../types';
import { axiosInstance } from './axios';
import { objectToFormData } from './objectFormdata';

export const getChecklist = async () => {
    const res = await axiosInstance.get('/checklist');
    return res.data.result as IChecklist;
};

export const postDatabaseInfo = async (values: IDatabaseInfo) => {
    const formData = new FormData();

    for (const key in values) {
        formData.append(key, values[key]);
    }
    formData.append('types[]', 'DB_HOST');
    formData.append('types[]', 'DB_PORT');
    formData.append('types[]', 'DB_DATABASE');
    formData.append('types[]', 'DB_USERNAME');
    formData.append('types[]', 'DB_PASSWORD');

    const res = await axiosInstance.post('/database-setup', formData);
    return res.data.result;
};

export const runMigration = async (demo: boolean) => {
    const formData = new FormData();
    if (demo) formData.append('demo', 'true');

    const res = await axiosInstance.post('/run-db-migration', formData, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    });
    return res.data.result as IMigrationResponse;
};

export const postAdminConfig = async (data: IAdminConfig) => {
    const res = await axiosInstance.post(
        '/admin-configuration',
        objectToFormData(data),
        {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        },
    );
    return res.data.result;
};
