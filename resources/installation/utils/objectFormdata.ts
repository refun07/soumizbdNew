export const objectToFormData = (object: { [key: string]: any }): FormData => {
    const formData = new FormData();
    Object.keys(object).forEach((key) => {
        if (object[key] === undefined || object[key] === null) return;

        if (Array.isArray(object[key])) {
            object[key].forEach((item: any) => {
                formData.append(`${key}[]`, item);
            });
            return;
        }

        formData.append(key, object[key]);
    });
    return formData;
};

export const formDataToObject = (
    formData: FormData,
): { [key: string]: any } => {
    const object: { [key: string]: any } = {};
    formData.forEach((value, key) => {
        object[key] = value;
    });
    return object;
};
