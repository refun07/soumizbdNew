import React, { useState } from 'react';
import CourierOrderModal from './CourierOrderModal';
import type { DeliveryPartner, CourierOrderResponse } from '../../../types/index';
import SelectInput from '../../inputs/SelectInput';
import CourierDetailsModal from './CourierDetailsModal';

interface Props {
    order: any;
}

const SelectPartner = ({order}:Props) => {
  const [selectedPartner, setSelectedPartner] = useState<DeliveryPartner | ''>(order.courier_partner);
  const [modalOpen, setModalOpen] = useState(false);
  const [openDetailsModal,setOpenDetailsModal]=useState(false);
  const [orderStatus, setOrderStatus] = useState<string | null>(order.courier_devlivery_status);
  const [orderInfo, setOrderInfo] = useState<any>(null);
  const [orderSuccess,setOrderSuccess] =useState(false);
  const handlePlaceClick = () => {
    if (!selectedPartner) return;
    setModalOpen(true);
  };

  const handleSubmit = async (formData: Record<string, any>) => {
    const payload = {
      partner: selectedPartner,
      order_group_id: order.orderGroupId || order.order_group_id,
      ...formData,
    };

    try {
      const res = await fetch('/api/v1/courier/order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json',  'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload),
      });

      const data: CourierOrderResponse = await res.json();
      if (data.status === 'success') {
        if(selectedPartner == 'pathao'){
            setOrderStatus(data.data.order_status);

        }else if(selectedPartner == 'steadfast'){
            setOrderStatus(data.data.consignment.status);

        }
        setOrderSuccess(true)
        setOrderInfo(data.data);
      } else {
        setOrderStatus(`Error: ${data.message}`);
      }
    } catch (err) {
      setOrderStatus('Error: API failed');
    }
  };


function formatStatusString(str:string) {
  // Split by underscores or hyphens, capitalize each word, and join with spaces
  return str
    .split(/[_-]/) // Split on both underscores and hyphens
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ');
}
  return (
    <div className="flex items-center space-x-2 p-4 ">
      {(order.courier_partner && order.courier_partner!='') || orderSuccess ? (
        <div className="flex flex-col">
          {/* <span className="text-sm font-bold text-gray-300">{formatStatusString(orderStatus ?? '')}</span> */}
          <div className="flex items-center space-x-2">
            <div  className="w-[18px] h-[18px] flex items-center justify-center rounded">
                <img src={`/images/courier/${selectedPartner}icon.svg`} className="w-full h-full object-contain rounded" />
            </div>
            <div><span className="text-xs text-gray-500">{selectedPartner}</span></div>
          </div>
          <div className="text-sm"><a href='#' onClick={()=>{
            setOpenDetailsModal(true);
          }}>Track Order</a></div>
        </div>
      ) : (
        <>
            <SelectInput
                name="partner"
                placeholder="Select Partner"
                value={selectedPartner}
                options={[

                    {
                        id: 'pathao',
                        name: 'Pathao',
                    },
                    {
                        id: 'steadfast',
                        name: 'Steadfast',
                    },

                    {
                        id: 'redx',
                        name: 'Redx',
                    },
                ]}
                getOptionLabel={(item: { name: any; }) => item.name}
                getOptionValue={(item: { id: any; }) => item.id}
                onChange={(item) => {
                    setSelectedPartner(item.id as DeliveryPartner)
                }}
                groupClassName="grow"
            />
          <button
            onClick={handlePlaceClick}
            className="bg-blue-600 text-white px-4 py-2 rounded text-sm"
          >
            Place
          </button>
        </>
      )}

      <CourierOrderModal
        partner={selectedPartner}
        isOpen={modalOpen}
        onClose={() => setModalOpen(false)}
        onSubmit={handleSubmit}
      />
      {order.courier_partner && openDetailsModal && <CourierDetailsModal
        partner={selectedPartner}
        trackingId={order.tracking_number}
        isOpen={openDetailsModal}
        onClose={() => setOpenDetailsModal(false)}
        onSubmit={()=>{}}
        />}

    </div>
  );
};

export default SelectPartner;
