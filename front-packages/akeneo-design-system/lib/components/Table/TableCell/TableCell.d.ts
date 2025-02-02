import React, { ReactNode } from 'react';
declare const TableCell: React.ForwardRefExoticComponent<Omit<React.TdHTMLAttributes<HTMLTableCellElement>, "children" | "rowTitle"> & {
    children?: ReactNode;
    rowTitle?: boolean | undefined;
} & React.RefAttributes<HTMLTableCellElement>>;
export { TableCell };
