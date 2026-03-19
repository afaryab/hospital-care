import ReceaveableListItem from '@/elements/receaveables/receaveable-list-item';
import { Receivable } from '@/types';

export type ReceivableListItemProps = {
    receivable: Receivable;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const ReceivableListItem: React.FC<ReceivableListItemProps> = ({ receivable, ...props }) => (
    <ReceaveableListItem receaveable={receivable} {...props} />
);

export default ReceivableListItem;
