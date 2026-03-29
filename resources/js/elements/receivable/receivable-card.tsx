import ReceaveableCard from '@/elements/receaveables/receaveable-card';
import { Receivable } from '@/types';

export type ReceivableCardProps = {
    receivable: Receivable;
    className?: string;
    onClick?: () => void;
};

const ReceivableCard: React.FC<ReceivableCardProps> = ({
    receivable,
    ...props
}) => <ReceaveableCard receaveable={receivable} {...props} />;

export default ReceivableCard;
