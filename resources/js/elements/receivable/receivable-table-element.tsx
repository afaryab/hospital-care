import ReceaveableTableElement from '@/elements/receaveables/receaveable-table-element';
import { Receivable } from '@/types';

export type ReceivableTableElementProps = {
    receivable: Receivable;
    className?: string;
};

const ReceivableTableElement: React.FC<ReceivableTableElementProps> = ({
    receivable,
    ...props
}) => <ReceaveableTableElement receaveable={receivable} {...props} />;

export default ReceivableTableElement;
