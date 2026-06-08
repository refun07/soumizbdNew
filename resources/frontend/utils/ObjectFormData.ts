export const objectToFormData = (object: { [key: string]: any }): FormData => {
    const formData = new FormData();

    Object.keys(object).forEach((key) => {
        const value = object[key];

        if (value === undefined || value === null) return;

        if (Array.isArray(value)) {
            value.forEach((item) => {
                // Stringify object items in arrays, leave primitives as is
                if (typeof item === 'object' && !(item instanceof File)) {
                    formData.append(`${key}[]`, JSON.stringify(item));
                } else {
                    formData.append(`${key}[]`, item);
                }
            });
        } else if (typeof value === 'object' && !(value instanceof File)) {
            // Convert plain objects (not File) to JSON string
            formData.append(key, JSON.stringify(value));
        } else {
            formData.append(key, value);
        }
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
