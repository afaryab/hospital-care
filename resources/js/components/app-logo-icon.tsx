import clsx from 'clsx';

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
        <img
            src={
                direction === 'vertical'
                    ? '/logo-vertical.png'
                    : direction === 'horizontal'
                      ? '/logo-horizontal.png'
                      : '/logo.png'
            }
            alt="Hospital Care application logo featuring medical cross symbol in vertical layout"
            className={clsx({
                'max-h-2 w-auto': size === 2,
                'max-h-3 w-auto': size === 3,
                'max-h-4 w-auto': size === 4,
                'max-h-6 w-auto': size === 6,
                'max-h-8 w-auto': size === 8,
                'max-h-10 w-auto': size === 10,
                'max-h-12 w-auto': size === 12,
                'max-h-16 w-auto': size === 16,
                'max-h-20 w-auto': size === 20,
                'max-h-24 w-auto': size === 24,
                'max-h-28 w-auto': size === 28,
                'max-h-32 w-auto': size === 32,
                'max-h-40 w-auto': size === 40,
                'max-h-48 w-auto': size === 48,
            })}
        />
    );
}
