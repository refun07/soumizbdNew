// src/store/courierSlice.ts
import { createSlice, PayloadAction } from '@reduxjs/toolkit';

interface CourierState {
  cities: any[];
  zones: any[];
  areas: any[];
  redxAreas:any[];
}

const initialState: CourierState = {
  cities: [],
  zones: [], 
  areas: [],
  redxAreas:[]
};

const courierSlice = createSlice({
  name: 'courier',
  initialState,
  reducers: {
    setCities(state, action: PayloadAction<any[]>) {
      state.cities = action.payload;
    },
    setZones(state, action: PayloadAction<{ cityId: number; zones: any[] }>) {
      state.zones = action.payload.zones;
    },
    setAreas(state, action: PayloadAction<{ zoneId: number; areas: any[] }>) {
      state.areas= action.payload.areas;
    },
    setRedxAreas(state,action: PayloadAction<any[]>){
      state.redxAreas = action.payload;
    }
  },
});

export const { setCities, setZones, setAreas, setRedxAreas } = courierSlice.actions;
export default courierSlice.reducer;
