// src/components/CourierInitLoader.tsx
import { useCities, useRedxAreas } from '../../../../api/courierApi';

const CourierInitLoader = () => {
  useCities(); // runs on mount, stores in redux + react query
  useRedxAreas();
  return null;
};

export default CourierInitLoader;
