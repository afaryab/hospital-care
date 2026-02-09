import React from "react";

type CurrencyProps = {
    value: number;            // integer amount
    currency?: string;        // e.g. 'PKR', 'EUR'
    locale?: string;          // e.g. 'en-US', 'de-DE'
    fromMinorUnit?: boolean;  // set true if value is in cents
    className?: string;
};

export function formatCurrency(
    value: number,
    currency: string = "PKR",
    locale?: string,
    fromMinorUnit: boolean = false
): string {
    const amount = fromMinorUnit ? value / 100 : value;

    const formatter = new Intl.NumberFormat(locale || undefined, {
        style: "currency",
        currency,
    });

    return formatter.format(amount);
}

const Currency: React.FC<CurrencyProps> = ({
    value,
    currency = "PKR",
    locale,
    fromMinorUnit = false,
    className,
}) => {
    const formatted = React.useMemo(
        () => formatCurrency(value, currency, locale, fromMinorUnit),
        [value, currency, locale, fromMinorUnit]
    );

    return <span className={className}>{formatted}</span>;
};

export default Currency;