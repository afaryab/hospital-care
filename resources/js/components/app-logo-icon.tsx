import clsx from "clsx";


export default function AppLogoIcon({
    size = 24,
    direction = 'vertical',
    className,
}: {
    size?: number;
    direction?: string;
    className?: string;
}) {
    return (
        <img src={
            direction === 'vertical'
                ? '/logo-vertical.png'
                : direction === 'horizontal'
                ? '/logo-horizontal.png'
                : '/logo.png'
        } 
        alt="Hospital Care application logo featuring medical cross symbol in vertical layout"
        className={clsx({
            'w-auto max-h-6': size === 6,
            'w-auto max-h-8': size === 8,
            'w-auto max-h-10': size === 10,
            'w-auto max-h-12': size === 12,
            'w-auto max-h-16': size === 16,
            'w-auto max-h-20': size === 20,
            'w-auto max-h-24': size === 24,
            'w-auto max-h-28': size === 28,
            'w-auto max-h-32': size === 32,
            'w-auto max-h-40': size === 40,
            'w-auto max-h-48': size === 48,
        })} />
    );
}
