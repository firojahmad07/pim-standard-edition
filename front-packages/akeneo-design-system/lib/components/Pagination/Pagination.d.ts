import { FC } from 'react';
declare type PaginationProps = {
    currentPage: number;
    totalItems: number;
    itemsPerPage?: number;
    sticky?: number;
    followPage: (page: number) => void;
};
declare const Pagination: FC<PaginationProps>;
export { Pagination };
