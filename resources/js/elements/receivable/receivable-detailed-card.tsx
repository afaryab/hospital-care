import ReceaveableDetailedCard from '@/elements/receaveables/receaveable-detailed-card';
import { Receivable } from '@/types';

export type ReceivableDetailedCardProps = {
    receivable: Receivable;
    className?: string;
};

const ReceivableDetailedCard: React.FC<ReceivableDetailedCardProps> = ({
    receivable,
    ...props
}) => <ReceaveableDetailedCard receaveable={receivable} {...props} />;

export default ReceivableDetailedCard;
