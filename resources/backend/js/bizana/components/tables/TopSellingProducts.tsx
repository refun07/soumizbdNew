import React, { useState } from 'react';
import { IPaginationMeta } from '../../../react/types';

interface TopSellingProduct {
  id: number;
  name: string;
  orders: number;
  revenue: string;
  image?: string;
}

interface TopSellingProductsProps {
  products?: TopSellingProduct[];
  pagination?: IPaginationMeta;
  onPageChange?: (page: { selected: number }) => void;
  loading?: boolean;
  data?: any[];
  currentPage?: number;
}

const TopSellingProducts: React.FC<TopSellingProductsProps> = ({
  products = [],
  pagination,
  onPageChange,
  loading = false,
  data,
  currentPage = 1,
}) => {
  // Transform API data to component format
  const transformApiData = (apiData: any[]): TopSellingProduct[] => {
    if (!apiData || apiData.length === 0) {
      return [];
    }

    return apiData.map((item, index) => ({
      id: item.id || index + 1,
      name: item.name || 'Unknown Product',
      orders: item.quantity || 0,
      revenue: item.revenue ? `${item.revenue.toLocaleString()} Tk` : '0 Tk',
      image: item.image,
    }));
  };

  const displayProducts = data ? transformApiData(data) : products;

  // Show empty state when no products
  const showEmptyState = displayProducts.length === 0 && !loading;

  const handlePageClick = (pageNum: number) => {
    // Prevent page refresh - just call the parent handler
    onPageChange?.({ selected: pageNum });
  };

  return (
    <div className="card rounded-lg shadow-sm">
      <div className="p-6 border-b border-border">
        <h3 className="text-lg font-semibold text-foreground">
          Top Selling Products
        </h3>
      </div>
      <div className="p-6">
        {loading ? (
          <div className="animate-pulse">
            <div className="h-4 bg-muted rounded w-full mb-4"></div>
            <div className="h-4 bg-muted rounded w-full mb-4"></div>
            <div className="h-4 bg-muted rounded w-full mb-4"></div>
          </div>
        ) : showEmptyState ? (
          <div className="text-center py-8">
            <div className="text-muted text-sm">
              No products found for the selected period.
            </div>
          </div>
        ) : (
          <>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="theme-table__head">
                  <tr className="text-left text-xs text-muted uppercase tracking-wider">
                    <th className="pb-3 font-medium">
                      Product Details
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
                  {displayProducts.map((product) => (
                    <tr key={product.id} className="theme-table__row">
                      <td className="py-4">
                        <div className="flex items-center space-x-3">
                          {product.image ? (
                            <img
                              src={product.image}
                              alt={product.name}
                              className="w-10 h-10 object-cover rounded"
                            />
                          ) : (
                            <div className="w-10 h-10 bg-muted rounded"></div>
                          )}
                          <span className="text-foreground">
                            {product.name}
                          </span>
                        </div>
                      </td>
                      <td className="py-4 text-muted">
                        {product.orders}
                      </td>
                      <td className="py-4 text-muted">
                        {product.revenue}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            {!showEmptyState && pagination && pagination.last_page > 1 && (
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
                        onClick={() => handlePageClick(pageNum)}
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

export default TopSellingProducts;