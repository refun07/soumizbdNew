import React, { useState } from 'react';
import { useAppSelector } from '../../../../order/hooks';
import SelectInput from '../../inputs/SelectInput';
import { PathaoCity } from '../../../types';
import { useAreas, useZones } from '../../../../api/courierApi';

interface CourierOrderModalProps {
  isOpen: boolean;
  onClose: () => void;
  partner: 'pathao' | 'steadfast' | 'redx' | '';
  onSubmit: (data: Record<string, any>) => void;
}

const CourierOrderModal: React.FC<CourierOrderModalProps> = ({
  isOpen,
  onClose,
  partner,
  onSubmit,
}) => {
  // Shared fields

  const [weight, setWeight] = useState('');
  const [itemDesc, setItemDesc] = useState('');
  const [instructions, setInstructions] = useState('');
  const [cityId, setCityId] = useState<number | null>(null);
  const [zoneId, setZoneId] = useState<number | null>(null);
  const [areaId, setAreaId] = useState<number | null>(null);
  const [area, setArea] = useState<string | null>(null);

  // Redx specific
  const [packageDetail, setPackageDetail] = useState('');
//   const [specialInstruction, setSpecialInstruction] = useState('');
  const cities = useAppSelector((state) => state.courier.cities);
  const { data: zones = [] } = useZones(cityId ?? undefined);
  const { data: areas = [] } = useAreas(zoneId ?? undefined);
  const redxAreas = useAppSelector((state) => state.courier.redxAreas);

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    let payload: Record<string, any> = {
     instructions
    };

    if (partner === 'pathao') {
      payload = {
        ...payload,
        recipient_city: cityId,
        recipient_zone: zoneId,
        recipient_area: areaId,
        item_weight: parseFloat(weight),
        item_description: itemDesc,
      };
    } else if (partner === 'redx') {
      payload = {
        ...payload,
        weight: parseFloat(weight),
        package_detail: packageDetail,
        delivery_area:area,
        delivery_area_id:areaId
        // special_instruction: specialInstruction,
      };
    }

    onSubmit(payload);
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
      <div className="bg-[#1c1c1e] p-6 rounded-xl shadow-xl w-full max-w-2xl">
        <h2 className="text-lg font-semibold text-white mb-4 capitalize">
          Place Order - {partner}
        </h2>

        <form onSubmit={handleSubmit}>
          {/* Conditionally Rendered Fields */}
          {partner === 'pathao' && (
            <>
              <div className="mb-2">
                <label className="block text-sm text-gray-300">City / Zone / Area</label>
                <div className="flex gap-2">
                    <SelectInput
                        name="recipient_city"
                        placeholder="Select City"
                        value={cityId}
                        options={cities}
                        getOptionLabel={(item: { city_name: any; }) => item.city_name}
                        getOptionValue={(item: { city_id: any; }) => item.city_id}
                        onChange={(item) => {
                             setCityId(item.city_id);
                        }}
                        groupClassName="grow"
                        required
                    />

                    <SelectInput
                        name="recipient_zone"
                        placeholder="Select Zone"
                        value={zoneId}
                        options={zones}
                        getOptionLabel={(item: { zone_name: any; }) => item.zone_name}
                        getOptionValue={(item: { zone_id: any; }) => item.zone_id}
                        onChange={(item) => {
                             setZoneId(item.zone_id);
                        }}
                        groupClassName="grow"
                        required
                    />
                    <SelectInput
                        name="recipient_area"
                        placeholder="Select Area"
                        value={areaId}
                        options={areas}
                        getOptionLabel={(item: { area_name: any; }) => item.area_name}
                        getOptionValue={(item: { area_id: any; }) => item.area_id}
                        onChange={(item) => {
                             setAreaId(item.area_id);
                        }}
                        groupClassName="grow"
                    />
                </div>
              </div>
              <div className="mb-2">
                <label className="block text-sm text-gray-300">Weight (kg)</label>
                <input
                  type="number"
                  value={weight}
                  onChange={(e) => setWeight(e.target.value)}
                  className="w-full px-4 py-2 bg-gray-800 text-white rounded-lg border border-gray-700"
                  step="0.1"
                  required
                />
              </div>
              <div className="mb-4">
                <label className="block text-sm text-gray-300">Item Description</label>
                <textarea
                  value={itemDesc}
                  onChange={(e) => setItemDesc(e.target.value)}
                  className="w-full px-4 py-2 bg-gray-800 text-white rounded-lg border border-gray-700"

                />
              </div>
            </>
          )}

          {partner === 'redx' && (
            <>
              <div className="mb-4">
                <label className="block text-sm text-gray-300">Area</label>
                 <SelectInput
                        name="recipient_area"
                        placeholder="Select Area"
                        value={areaId}
                        options={redxAreas}
                        getOptionLabel={(item: { name: any; }) => item.name}
                        getOptionValue={(item: { id: any; }) => item.id}
                        onChange={(item) => {
                             setAreaId(item.id);
                             setArea(item.name)
                        }}
                        groupClassName="grow"
                        required
                    />
              </div>
              <div className="mb-4">
                <label className="block text-sm text-gray-300">Weight (kg)</label>
                <input
                  type="number"
                  value={weight}
                  onChange={(e) => setWeight(e.target.value)}
                  className="w-full px-4 py-2 bg-gray-800 text-white rounded-lg border border-gray-700"
                  step="0.1"
                  required
                />
              </div>
            </>
          )}
            <div className="mb-4">
                <label className="block text-sm text-gray-300">Instructions/Note</label>
                <textarea
                    value={instructions}
                    onChange={(e) => setInstructions(e.target.value)}
                    className="w-full px-4 py-2 bg-gray-800 text-white rounded-lg border border-gray-700"

                />
            </div>
          {/* Buttons */}
          <div className="flex justify-end gap-2 mt-6">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-500 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition"
            >
              Submit
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default CourierOrderModal;
