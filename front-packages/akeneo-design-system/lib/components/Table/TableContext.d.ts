declare type TableContextType = {
    isSelectable: boolean;
    displayCheckbox: boolean;
    isDragAndDroppable: boolean;
    onReorder: ((reorderedIndices: number[]) => void) | undefined;
};
declare const TableContext: import("react").Context<TableContextType>;
export { TableContext };
