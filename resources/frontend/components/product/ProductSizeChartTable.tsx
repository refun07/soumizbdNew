import { ISizeChartTable } from '../../types/product';
import { cn } from '../../utils/cn';

interface ProductSizeChartTableProps {
    chart: ISizeChartTable;
    className?: string;
}

const ProductSizeChartTable = ({
    chart,
    className,
}: ProductSizeChartTableProps) => {
    if (!chart.columns.length) {
        return null;
    }

    return (
        <div className={cn('overflow-x-auto', className)}>
            <table className="min-w-full border-separate border-spacing-0 overflow-hidden rounded-2xl border border-black/10 bg-white text-sm">
                <thead>
                    <tr className="bg-slate-100 text-left text-slate-700">
                        {chart.columns.map((column, index) => (
                            <th
                                key={`${column}-${index}`}
                                className="border-b border-black/10 px-4 py-3 font-semibold"
                            >
                                {column}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {chart.rows.map((row, rowIndex) => (
                        <tr key={`row-${rowIndex}`} className="odd:bg-white even:bg-slate-50/70">
                            {chart.columns.map((_, columnIndex) => (
                                <td
                                    key={`row-${rowIndex}-col-${columnIndex}`}
                                    className="border-b border-black/5 px-4 py-3 text-slate-700 last:border-b-0"
                                >
                                    {row[columnIndex] || '-'}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default ProductSizeChartTable;
