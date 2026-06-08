import React, { useState } from 'react';
import { IPaginationMeta } from '../../../react/types';

interface TopSellingCategory {
  id: number;
  subCategory: string;
  parentCategory: string;
  orders: number;
  revenue: string;
}

interface TopSellingCategoriesProps {
  categories?: TopSellingCategory[];
  pagination?: IPaginationMeta;
  onPageChange?: (page: { selected: number }) => void;
  loading?: boolean;
  data?: any[];
  currentPage?: number;
}

const TopSellingCategories: React.FC<TopSellingCategoriesProps> = ({
  categories = [],
  pagination,
  onPageChange,
  loading = false,
  data,
  currentPage = 1,
}) => {
  // Transform API data to component format
  const transformApiData = (apiData: any[]): TopSellingCategory[] => {
    if (!apiData || apiData.length === 0) {
      return [];
    }

    return apiData.map((item, index) => ({
      id: index + 1,
      subCategory: item.category || 'Unknown Category',
      parentCategory: 'All Categories', // Since API doesn't provide parent category
      orders: item.quantity || 0,
      revenue: item.revenue ? `${item.revenue.toLocaleString()} Tk` : '0 Tk',
    }));
  };

  // Default sample data if no data provided
  const defaultCategories: TopSellingCategory[] = [
    {
      id: 1,
      subCategory: 'Sneakers',
      parentCategory: 'Footware',
      orders: 89,
      revenue: '26,700 Tk',
    },
    {
      id: 2,
      subCategory: 'Laptops',
      parentCategory: 'Electronics',
      orders: 76,
      revenue: '228,000 Tk',
    },
    {
      id: 3,
      subCategory: 'T-Shirts',
      parentCategory: 'Clothing',
      orders: 65,
      revenue: '9,750 Tk',
    },
    {
      id: 4,
      subCategory: 'Running Shoes',
      parentCategory: 'Footware',
      orders: 54,
      revenue: '16,200 Tk',
    },
    {
      id: 5,
      subCategory: 'Smartphones',
      parentCategory: 'Electronics',
      orders: 48,
      revenue: '144,000 Tk',
    },
    {
      id: 6,
      subCategory: 'Jeans',
      parentCategory: 'Clothing',
      orders: 42,
      revenue: '8,400 Tk',
    },
  ];

  const displayCategories = data ? transformApiData(data) : (categories.length > 0 ? categories : defaultCategories);

  const handlePageClick = (pageNum: number) => {
    // Prevent page refresh - just call the parent handler
    onPageChange?.({ selected: pageNum });
  };

  return (
    <div className="card rounded-lg shadow-sm">
      <div className="p-6 border-b border-border">
        <h3 className="text-lg font-semibold text-foreground">
          Top Selling Category
        </h3>
      </div>
      <div className="p-6">
        {loading ? (
          <div className="animate-pulse">
            <div className="h-4 bg-muted rounded w-full mb-4"></div>
            <div className="h-4 bg-muted rounded w-full mb-4"></div>
            <div className="h-4 bg-muted rounded w-full mb-4"></div>
          </div>
        ) : (
          <>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="theme-table__head">
                  <tr className="text-left text-xs text-muted uppercase tracking-wider">
                    <th className="pb-3 font-medium">
                      Sub Category
                    </th>
                    <th className="pb-3 font-medium">
                      Parent Category
                    </th>
                    <th className="pb-3 font-medium">
                      Orders
                    </th>
                    <th className="pb-3 font-medium">
                      Revenue
                    </th>
                  </tr>
                </thead>
                <tbody className="theme-table__body text-sm">
                  {displayCategories.map((category) => (
                    <tr key={category.id} className="theme-table__row">
                      <td className="py-5 text-foreground">
                        {category.subCategory}
                      </td>
                      <td className="py-5 text-muted">
                        {category.parentCategory}
                      </td>
                      <td className="py-5 text-muted">
                        {category.orders}
                      </td>
                      <td className="py-5 text-muted">
                        {category.revenue}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            {pagination && pagination.last_page > 1 && (
              <div className="flex justify-end items-center space-x-1 mt-4">
                <button
                  onClick={() => handlePageClick(Math.max(1, currentPage - 1))}
                  disabled={currentPage <= 1}
                  className="p-2 text-sm text-muted hover:bg-muted/50 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  ←
                </button>

                {(() => {
                  // Show pages around current page
                  const delta = 2;
                  const range = [];
                  const rangeWithDots = [];

                  for (
                    let i = Math.max(2, currentPage - delta);
                    i <= Math.min(pagination.last_page - 1, currentPage + delta);
                    i++
                  ) {
                    range.push(i);
                  }

                  if (currentPage - delta > 2) {
                    rangeWithDots.push(1, '...');
                  } else {
                    rangeWithDots.push(1);
                  }

                  rangeWithDots.push(...range);

                  if (currentPage + delta < pagination.last_page - 1) {
                    rangeWithDots.push('...', pagination.last_page);
                  } else if (pagination.last_page > 1) {
                    rangeWithDots.push(pagination.last_page);
                  }

                  return rangeWithDots.map((pageNum, index) =>
                    pageNum === '...' ? (
                      <span key={`dots-${index}`} className="px-3 py-1 text-sm text-muted">
                        ...
                      </span>
                    ) : (
                      <button
                        key={pageNum}
                        onClick={() => handlePageClick(pageNum as number)}
                        className={`px-3 py-1 text-sm rounded ${
                          pageNum === currentPage
                            ? 'bg-blue-600 text-white'
                            : 'text-muted hover:bg-muted/50'
                        }`}
                      >
                        {pageNum.toString().padStart(2, '0')}
                      </button>
                    )
                  );
                })()}

                <button
                  onClick={() => handlePageClick(Math.min(pagination.last_page, currentPage + 1))}
                  disabled={currentPage >= pagination.last_page}
                  className="p-2 text-sm text-muted hover:bg-muted/50 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  →
                </button>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default TopSellingCategories;