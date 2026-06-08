import React, { useState, useEffect } from "react";
import DateRangePicker from "../../../js/bizana/components/DateRangePicker";

interface SearchFormProps {
  fromDate: string;
  toDate: string;
  search:string;
  paymentStatus: string;
  onChange: (key: string, value: string) => void;
  onDateChange: (startDate: string, endDate: string) => void;
  onSearch: (searchTerm: string) => void;
  onPaymentStatusChange: (value: string) => void;
}

const SearchForm: React.FC<SearchFormProps> = ({
  fromDate,
  toDate,
  search,
  paymentStatus,
  onChange,
  onDateChange,
  onSearch,
  onPaymentStatusChange
}) => {
  // Local state for immediate input update (debounced in parent component)
  const [localSearch, setLocalSearch] = useState(search);

  // Update local search when external search changes (e.g., on reset)
  useEffect(() => {
    setLocalSearch(search);
  }, [search]);

  const handleSearchChange = (value: string) => {
    // Update input immediately for instant feedback
    setLocalSearch(value);

    // Update parent state (which handles debouncing via useEffect)
    onChange("search", value);
  };

  return (
    <div className="flex items-end space-x-3 w-full">
      {/* Date Range Picker - increased width */}
      <div className="w-[28rem] flex-shrink-0">
        <DateRangePicker
          startDate={fromDate}
          endDate={toDate}
          onDateChange={onDateChange}
        />
      </div>

      {/* Payment Status Filter */}
      <div className="flex items-end space-x-2 w-48 flex-shrink-0">
        <label className="text-sm font-medium text-gray-700 whitespace-nowrap">
          Payment Status
        </label>
        <select
          className="theme-input w-full"
          value={paymentStatus}
          onChange={(e) => onPaymentStatusChange(e.target.value)}
        >
          <option value="">Select Status</option>
          <option value="paid">paid</option>
          <option value="unpaid">unpaid</option>
        </select>
      </div>

      {/* Search Input - reduced fixed width */}
      <div className="search-form relative w-72 flex-shrink-0">
        <input
          type="text"
          id="search"
          name="search"
          value={localSearch}
          className="theme-input w-full"
          placeholder="Search Order with ID/Phone/Name"
          onChange={(e) => handleSearchChange(e.target.value)}
          autoComplete="off"
        />
        <span className="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
          <i className="fa-solid fa-magnifying-glass"></i>
        </span>
      </div>

      {/* Add Order Button - fixed width */}
      <div className="w-auto flex-shrink-0">
        <a
          href="/admin/orders/create"
          className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 inline-flex items-center whitespace-nowrap"
        >
          <i className="fa-solid fa-plus mr-2"></i>
          Add Order
        </a>
      </div>
    </div>
  );
};

export default SearchForm;
