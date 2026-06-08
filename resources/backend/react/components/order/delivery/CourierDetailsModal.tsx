import React, { useState } from 'react';
import { useTrackCourier } from '../../../../api/courierApi';
import { Dispatch, SetStateAction } from 'react';

type DeliveryPartner = 'pathao' | 'steadfast' | 'redx' | '';

interface CourierDetailsModalProps {
  isOpen: boolean;
  onClose: () => void;
  partner: DeliveryPartner;
  trackingId: number;
  onSubmit?: (data: Record<string, any>) => void;
}

const partnerLabels: Record<DeliveryPartner, string> = {
  pathao: 'Pathao',
  steadfast: 'Steadfast',
  redx: 'RedX',
  '': 'Unknown',
};

const pathaoStatusMap: Record<string, { label: string; color: string }> = {
  Pending: { label: 'Pending', color: 'bg-yellow-100 text-yellow-800' },
  'Pickup Assigned': { label: 'Pickup Assigned', color: 'bg-blue-100 text-blue-800' },
  'Pickup Done': { label: 'Pickup Done', color: 'bg-blue-100 text-blue-800' },
  'In Transit': { label: 'In Transit', color: 'bg-indigo-100 text-indigo-800' },
  'At Hub': { label: 'At Hub', color: 'bg-purple-100 text-purple-800' },
  'Delivery Assigned': { label: 'Delivery Assigned', color: 'bg-orange-100 text-orange-800' },
  'On Delivery': { label: 'On Delivery', color: 'bg-orange-100 text-orange-800' },
  Delivered: { label: 'Delivered', color: 'bg-green-100 text-green-800' },
  Cancelled: { label: 'Cancelled', color: 'bg-red-100 text-red-800' },
  'Partial Delivered': { label: 'Partial Delivered', color: 'bg-amber-100 text-amber-800' },
  Returned: { label: 'Returned', color: 'bg-red-100 text-red-800' },
  'Exchange Delivered': { label: 'Exchange Delivered', color: 'bg-green-100 text-green-800' },
};

const steadfastStatusMap: Record<string, { label: string; color: string }> = {
  pending: { label: 'Pending', color: 'bg-yellow-100 text-yellow-800' },
  'in_review': { label: 'In Review', color: 'bg-blue-100 text-blue-800' },
  approved: { label: 'Approved', color: 'bg-blue-100 text-blue-800' },
  'picked_up': { label: 'Picked Up', color: 'bg-indigo-100 text-indigo-800' },
  'in_transit': { label: 'In Transit', color: 'bg-indigo-100 text-indigo-800' },
  delivered: { label: 'Delivered', color: 'bg-green-100 text-green-800' },
  cancelled: { label: 'Cancelled', color: 'bg-red-100 text-red-800' },
  returned: { label: 'Returned', color: 'bg-red-100 text-red-800' },
  'partial_delivered': { label: 'Partial Delivered', color: 'bg-amber-100 text-amber-800' },
};

const redxStatusMap: Record<string, { label: string; color: string }> = {
  'pickup-pending': { label: 'Pickup Pending', color: 'bg-yellow-100 text-yellow-800' },
  'pickup-assigned': { label: 'Pickup Assigned', color: 'bg-blue-100 text-blue-800' },
  'pickup-done': { label: 'Pickup Done', color: 'bg-blue-100 text-blue-800' },
  'in-transit': { label: 'In Transit', color: 'bg-indigo-100 text-indigo-800' },
  'at-hub': { label: 'At Hub', color: 'bg-purple-100 text-purple-800' },
  'delivery-assigned': { label: 'Delivery Assigned', color: 'bg-orange-100 text-orange-800' },
  delivered: { label: 'Delivered', color: 'bg-green-100 text-green-800' },
  cancelled: { label: 'Cancelled', color: 'bg-red-100 text-red-800' },
  returned: { label: 'Returned', color: 'bg-red-100 text-red-800' },
};

function getStatusBadge(partner: DeliveryPartner, status: string | undefined) {
  if (!status) return { label: 'Unknown', color: 'bg-gray-100 text-gray-800' };
  const normalized = status.toLowerCase().replace(/[\s_-]+/g, '_');

  let map: Record<string, { label: string; color: string }> = {};
  if (partner === 'pathao') map = pathaoStatusMap;
  else if (partner === 'steadfast') map = steadfastStatusMap;
  else if (partner === 'redx') map = redxStatusMap;

  for (const [key, val] of Object.entries(map)) {
    if (key.toLowerCase().replace(/[\s_-]+/g, '_') === normalized) return val;
  }

  return { label: status, color: 'bg-gray-100 text-gray-800' };
}

function PathaoStatusView({ data }: { data: any }) {
  const orderData = data?.data || data;
  const status = orderData?.order_status;
  const badge = getStatusBadge('pathao', status);

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-3">
        <span className={`px-3 py-1 rounded-full text-sm font-semibold ${badge.color}`}>
          {badge.label}
        </span>
      </div>

      <div className="grid grid-cols-2 gap-3">
        {orderData?.consignment_id && (
          <InfoField label="Consignment ID" value={String(orderData.consignment_id)} />
        )}
        {orderData?.merchant_order_id && (
          <InfoField label="Merchant Order ID" value={String(orderData.merchant_order_id)} />
        )}
        {orderData?.recipient_name && (
          <InfoField label="Recipient" value={orderData.recipient_name} />
        )}
        {orderData?.recipient_phone && (
          <InfoField label="Phone" value={orderData.recipient_phone} />
        )}
        {orderData?.recipient_city && (
          <InfoField label="City" value={String(orderData.recipient_city)} />
        )}
        {orderData?.recipient_zone && (
          <InfoField label="Zone" value={String(orderData.recipient_zone)} />
        )}
        {orderData?.delivery_type && (
          <InfoField
            label="Delivery Type"
            value={orderData.delivery_type == 48 ? 'Normal' : 'On Demand'}
          />
        )}
        {orderData?.item_type && (
          <InfoField label="Item Type" value={orderData.item_type == 1 ? 'Document' : 'Parcel'} />
        )}
        {orderData?.item_weight && (
          <InfoField label="Weight" value={`${orderData.item_weight} kg`} />
        )}
        {orderData?.amount_to_collect !== undefined && (
          <InfoField label="COD Amount" value={`৳ ${orderData.amount_to_collect}`} />
        )}
        {orderData?.rider_name && <InfoField label="Rider" value={orderData.rider_name} />}
        {orderData?.rider_phone && <InfoField label="Rider Phone" value={orderData.rider_phone} />}
        {orderData?.created_at && (
          <InfoField label="Created" value={orderData.created_at} />
        )}
        {orderData?.updated_at && (
          <InfoField label="Last Updated" value={orderData.updated_at} />
        )}
      </div>
    </div>
  );
}

function SteadfastStatusView({ data }: { data: any }) {
  const consignment = data?.consignment || data;
  const status = consignment?.status;
  const badge = getStatusBadge('steadfast', status);

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-3">
        <span className={`px-3 py-1 rounded-full text-sm font-semibold ${badge.color}`}>
          {badge.label}
        </span>
      </div>

      <div className="grid grid-cols-2 gap-3">
        {consignment?.tracking_code && (
          <InfoField label="Tracking Code" value={consignment.tracking_code} />
        )}
        {consignment?.invoice && <InfoField label="Invoice" value={consignment.invoice} />}
        {consignment?.recipient_name && (
          <InfoField label="Recipient" value={consignment.recipient_name} />
        )}
        {consignment?.recipient_phone && (
          <InfoField label="Phone" value={consignment.recipient_phone} />
        )}
        {consignment?.cod_amount !== undefined && (
          <InfoField label="COD Amount" value={`৳ ${consignment.cod_amount}`} />
        )}
        {consignment?.created_at && (
          <InfoField label="Created" value={consignment.created_at} />
        )}
        {consignment?.updated_at && (
          <InfoField label="Last Updated" value={consignment.updated_at} />
        )}
        {consignment?.note && <InfoField label="Note" value={consignment.note} />}

        {consignment?.deliveryman_name && (
          <InfoField label="Deliveryman" value={consignment.deliveryman_name} />
        )}
        {consignment?.deliveryman_phone && (
          <InfoField label="Deliveryman Phone" value={consignment.deliveryman_phone} />
        )}
      </div>
    </div>
  );
}

function RedxStatusView({ data }: { data: any }) {
  const tracking = data?.tracking || data;
  const status = tracking?.status;
  const badge = getStatusBadge('redx', status);

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-3">
        <span className={`px-3 py-1 rounded-full text-sm font-semibold ${badge.color}`}>
          {badge.label}
        </span>
      </div>

      <div className="grid grid-cols-2 gap-3">
        {tracking?.tracking_id && (
          <InfoField label="Tracking ID" value={String(tracking.tracking_id)} />
        )}
        {tracking?.merchant_invoice && (
          <InfoField label="Invoice" value={tracking.merchant_invoice} />
        )}
        {tracking?.recipient_name && (
          <InfoField label="Recipient" value={tracking.recipient_name} />
        )}
        {tracking?.recipient_phone && (
          <InfoField label="Phone" value={tracking.recipient_phone} />
        )}
        {tracking?.cod_amount !== undefined && (
          <InfoField label="COD Amount" value={`৳ ${tracking.cod_amount}`} />
        )}
        {tracking?.created_at && (
          <InfoField label="Created" value={tracking.created_at} />
        )}
        {tracking?.updated_at && (
          <InfoField label="Last Updated" value={tracking.updated_at} />
        )}
      </div>

      {Array.isArray(tracking?.logs) && tracking.logs.length > 0 && (
        <div>
          <h4 className="text-sm font-semibold text-foreground mb-2">Tracking Timeline</h4>
          <div className="space-y-2">
            {tracking.logs.map((log: any, idx: number) => (
              <div key={idx} className="flex items-start gap-3">
                <div className="flex flex-col items-center">
                  <div className="w-2.5 h-2.5 rounded-full bg-sky-500 mt-1.5" />
                  {idx < tracking.logs.length - 1 && (
                    <div className="w-0.5 h-full bg-border mt-1" />
                  )}
                </div>
                <div>
                  <div className="text-sm font-medium text-foreground">
                    {log.status || log.title || log.state}
                  </div>
                  {(log.time || log.created_at) && (
                    <div className="text-xs text-muted">{log.time || log.created_at}</div>
                  )}
                  {log.remarks && (
                    <div className="text-xs text-muted mt-0.5">{log.remarks}</div>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

function InfoField({ label, value }: { label: string; value: string }) {
  return (
    <div>
      <div className="text-xs text-muted uppercase">{label}</div>
      <div className="text-sm font-medium text-foreground break-all">{value}</div>
    </div>
  );
}

const CourierDetailsModal: React.FC<CourierDetailsModalProps> = ({
  isOpen,
  onClose,
  partner,
  trackingId,
  onSubmit,
}) => {
  const [loading, setLoading] = useState(false);
  const [showRaw, setShowRaw] = useState(false);
  const { data, error } = useTrackCourier(setLoading, trackingId, partner);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div className="flex items-center justify-between p-4 border-b border-border sticky top-0 bg-white z-10">
          <h2 className="text-lg font-semibold text-foreground">
            Delivery Status — {partnerLabels[partner]}
          </h2>
          <button
            onClick={onClose}
            className="text-muted hover:text-foreground transition-colors"
          >
            <i className="fa-solid fa-times text-xl"></i>
          </button>
        </div>

        <div className="p-4">
          {loading && (
            <div className="flex items-center justify-center py-8">
              <i className="fa-solid fa-spinner fa-spin text-2xl text-sky-500 mr-3"></i>
              <span className="text-muted">Fetching delivery status...</span>
            </div>
          )}

          {!loading && error && (
            <div className="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
              <i className="fa-solid fa-triangle-exclamation text-red-500 text-xl mb-2"></i>
              <div className="text-sm text-red-700">
                Failed to fetch delivery status. Please try again.
              </div>
            </div>
          )}

          {!loading && !error && data && (
            <>
              {partner === 'pathao' && <PathaoStatusView data={data} />}
              {partner === 'steadfast' && <SteadfastStatusView data={data} />}
              {partner === 'redx' && <RedxStatusView data={data} />}
              {!partner && (
                <div className="text-sm text-muted">No delivery partner specified.</div>
              )}

              <div className="mt-4 pt-4 border-t border-border">
                <button
                  onClick={() => setShowRaw(!showRaw)}
                  className="text-sm text-muted hover:text-foreground flex items-center gap-2 transition-colors"
                >
                  <i className={`fa-solid fa-chevron-${showRaw ? 'up' : 'down'} text-xs`} />
                  {showRaw ? 'Hide' : 'Show'} Raw Response
                </button>
                {showRaw && (
                  <pre className="mt-2 p-3 bg-gray-50 rounded-lg text-xs text-gray-700 overflow-x-auto max-h-64 overflow-y-auto">
                    {JSON.stringify(data, null, 2)}
                  </pre>
                )}
              </div>
            </>
          )}

          {!loading && !error && !data && (
            <div className="text-center py-8 text-muted">No tracking data available.</div>
          )}
        </div>

        <div className="flex items-center justify-end p-4 border-t border-border bg-gray-50">
          <button
            type="button"
            onClick={onClose}
            className="btn btn-secondary"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
};

export default CourierDetailsModal;
