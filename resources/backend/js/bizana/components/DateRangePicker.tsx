import React, { useState, useEffect } from 'react';
import { DateRangePicker as ReactDateRangePicker, defaultInputRanges } from 'react-date-range';
import { IoCalendar } from 'react-icons/io5';
import 'react-date-range/dist/styles.css'; // main css file
import 'react-date-range/dist/theme/default.css'; // theme css file
import './DateRangePicker.css';

interface DateRangePickerProps {
  startDate: string;
  endDate: string;
  onDateChange: (startDate: string, endDate: string) => void;
}



const DateRangePicker: React.FC<DateRangePickerProps> = ({
  startDate,
  endDate,
  onDateChange,
}) => {
  const [showPicker, setShowPicker] = useState(false);

  // Helper function to create date object from YYYY-MM-DD string without timezone issues
  const createDateFromYYYYMMDD = (dateString: string) => {
    if (!dateString) return new Date();
    // Parse the date parts to avoid timezone interpretation issues
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
  };

  // Use state to track the internal date objects and update them when props change
  const [selectionRange, setSelectionRange] = useState({
    startDate: startDate ? createDateFromYYYYMMDD(startDate) : new Date(),
    endDate: endDate ? createDateFromYYYYMMDD(endDate) : new Date(),
    key: 'selection' as const,
  });

  // Update selectionRange when props change
  useEffect(() => {
    setSelectionRange({
      startDate: startDate ? createDateFromYYYYMMDD(startDate) : new Date(),
      endDate: endDate ? createDateFromYYYYMMDD(endDate) : new Date(),
      key: 'selection',
    });
  }, [startDate, endDate]);

  const handleSelect = (ranges: any) => {
    const { selection } = ranges;
    const start = new Date(selection.startDate);
    const end = new Date(selection.endDate);

    // Update internal state immediately
    setSelectionRange(selection);

    // Format dates in local timezone to avoid UTC conversion issues
    const formatDateLocal = (date: Date) => {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    };

    onDateChange(
      formatDateLocal(start),
      formatDateLocal(end)
    );
  };

  const formatDateForDisplay = (date: Date) => {
    return date.toLocaleDateString('en-US', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    });
  };

  const hasSelectedRange = Boolean(startDate || endDate);

  const togglePicker = () => {
    setShowPicker(!showPicker);
  };

  const closePicker = () => {
    setShowPicker(false);
  };

  const clearDateRange = () => {
    const today = new Date();

    setSelectionRange({
      startDate: today,
      endDate: today,
      key: 'selection',
    });
    onDateChange('', '');
    closePicker();
  };



  // Define the static ranges for quick selection
  const staticRanges = [
    {
      label: 'Today',
      range: () => ({
        startDate: new Date(),
        endDate: new Date(),
      }),
      isSelected: () => {
        const today = new Date();
        return (
          selectionRange.startDate.toDateString() === today.toDateString() &&
          selectionRange.endDate.toDateString() === today.toDateString()
        );
      },
    },
    {
      label: 'Yesterday',
      range: () => {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        return { startDate: yesterday, endDate: yesterday };
      },
      isSelected: () => {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        return (
          selectionRange.startDate.toDateString() === yesterday.toDateString() &&
          selectionRange.endDate.toDateString() === yesterday.toDateString()
        );
      },
    },
    {
      label: 'This Week',
      range: () => {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - dayOfWeek);
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        return { startDate: startOfWeek, endDate: endOfWeek };
      },
      isSelected: () => {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - dayOfWeek);
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        return (
          selectionRange.startDate.toDateString() === startOfWeek.toDateString() &&
          selectionRange.endDate.toDateString() === endOfWeek.toDateString()
        );
      },
    },
    {
      label: 'Last Week',
      range: () => {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const startOfLastWeek = new Date(today);
        startOfLastWeek.setDate(today.getDate() - dayOfWeek - 7);
        const endOfLastWeek = new Date(startOfLastWeek);
        endOfLastWeek.setDate(startOfLastWeek.getDate() + 6);
        return { startDate: startOfLastWeek, endDate: endOfLastWeek };
      },
      isSelected: () => {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const startOfLastWeek = new Date(today);
        startOfLastWeek.setDate(today.getDate() - dayOfWeek - 7);
        const endOfLastWeek = new Date(startOfLastWeek);
        endOfLastWeek.setDate(startOfLastWeek.getDate() + 6);
        return (
          selectionRange.startDate.toDateString() === startOfLastWeek.toDateString() &&
          selectionRange.endDate.toDateString() === endOfLastWeek.toDateString()
        );
      },
    },
    {
      label: 'This Month',
      range: () => {
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        return { startDate: startOfMonth, endDate: endOfMonth };
      },
      isSelected: () => {
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        return (
          selectionRange.startDate.toDateString() === startOfMonth.toDateString() &&
          selectionRange.endDate.toDateString() === endOfMonth.toDateString()
        );
      },
    },
    {
      label: 'Last Month',
      range: () => {
        const today = new Date();
        const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        return { startDate: startOfLastMonth, endDate: endOfLastMonth };
      },
      isSelected: () => {
        const today = new Date();
        const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        return (
          selectionRange.startDate.toDateString() === startOfLastMonth.toDateString() &&
          selectionRange.endDate.toDateString() === endOfLastMonth.toDateString()
        );
      },
    },
    {
      label: 'This Year',
      range: () => {
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        const endOfYear = new Date(today.getFullYear(), 11, 31);
        return { startDate: startOfYear, endDate: endOfYear };
      },
      isSelected: () => {
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        const endOfYear = new Date(today.getFullYear(), 11, 31);
        return (
          selectionRange.startDate.toDateString() === startOfYear.toDateString() &&
          selectionRange.endDate.toDateString() === endOfYear.toDateString()
        );
      },
    },
  ];

  return (
    <div className="relative">
      <div className="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center sm:gap-4">
        <div className="flex items-center gap-2">
          <IoCalendar className="w-4 h-4 text-muted" />
          <span className="text-sm font-medium text-foreground">Date Range</span>
        </div>

        <button
          onClick={togglePicker}
          className="card flex w-full items-center justify-between gap-2 rounded-lg border border-border px-4 py-2 transition-colors hover:border-border-hover focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-auto"
        >
          <span className="truncate text-sm">
            {hasSelectedRange
              ? `${formatDateForDisplay(selectionRange.startDate)} – ${formatDateForDisplay(selectionRange.endDate)}`
              : 'All dates'}
          </span>
        </button>
      </div>

      {showPicker && (
        <div className="fixed z-50" style={{
          top: '200px',
          left: '50%',
          transform: 'translateX(-50%)',
          maxWidth: '90vw'
        }}>
          <div className="card border border-border rounded-lg shadow-lg">
            <ReactDateRangePicker
              ranges={[selectionRange]}
              onChange={handleSelect}
              moveRangeOnFirstSelection={false}
              months={2}
              direction="horizontal"
              maxDate={new Date()}
              staticRanges={staticRanges}
              inputRanges={[
                {
                  label: 'days up to today',
                  range: (value: number) => ({
                    startDate: new Date(new Date().getTime() - value * 24 * 60 * 60 * 1000),
                    endDate: new Date(),
                  }),
                  getCurrentValue: (range: any) => {
                    if (!range.startDate || !range.endDate) return '';
                    const today = new Date();
                    const end = new Date(range.endDate);
                    // Only show value if end date is today (within same day)
                    if (end.toDateString() !== today.toDateString()) return '';
                    const start = new Date(range.startDate);
                    // Calculate days more precisely to avoid rounding issues
                    const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                    const startOfStart = new Date(start.getFullYear(), start.getMonth(), start.getDate());
                    const diffTime = startOfToday.getTime() - startOfStart.getTime();
                    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                    return diffDays >= 0 ? diffDays.toString() : '';
                  },
                },
                {
                  label: 'days starting today',
                  range: (value: number) => ({
                    startDate: new Date(),
                    endDate: new Date(new Date().getTime() + value * 24 * 60 * 60 * 1000),
                  }),
                  getCurrentValue: (range: any) => {
                    if (!range.startDate || !range.endDate) return '';
                    const today = new Date();
                    const start = new Date(range.startDate);
                    // Only show value if start date is today (within same day)
                    if (start.toDateString() !== today.toDateString()) return '';
                    const end = new Date(range.endDate);
                    // Calculate days more precisely to avoid rounding issues
                    const startOfEnd = new Date(end.getFullYear(), end.getMonth(), end.getDate());
                    const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                    const diffTime = startOfEnd.getTime() - startOfToday.getTime();
                    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                    return diffDays >= 0 ? diffDays.toString() : '';
                  },
                },
              ]}
            />
            <div className="flex items-center justify-end gap-2 border-t border-border px-4 py-3">
              <button
                type="button"
                onClick={clearDateRange}
                className="rounded-md border border-border px-4 py-2 text-sm font-medium text-muted transition-colors hover:bg-muted/10 hover:text-foreground"
              >
                Clear
              </button>
              <button
                type="button"
                onClick={closePicker}
                className="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Close picker when clicking outside */}
      {showPicker && (
        <div
          className="fixed inset-0 z-40"
          onClick={closePicker}
        />
      )}
    </div>
  );
};

export default DateRangePicker;
