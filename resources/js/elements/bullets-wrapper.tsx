import { Link } from '@inertiajs/react';
import clsx from 'clsx';

export default function BulletsWrapper({ bullets = [], children }: any) {
    return (
        <div className="flex h-full flex-row rounded-xl">
            <div className="flex h-full flex-shrink flex-col items-center justify-start space-x-2 p-0">
                {bullets.map((bullet: any, index: number) => (
                    <Link
                        key={index}
                        href={bullet.url}
                        className={clsx({
                            'text-md cursor-pointer border-2 border-r-0 p-2 whitespace-nowrap text-[#1c398e] uppercase [writing-mode:vertical-lr]': true,
                            'rounded-tl-xl': index === 0,
                            'rounded-bl-xl': index === bullets.length - 1,
                            'border-[#06df72] hover:border-[#1c398e] hover:bg-[#1c398e] hover:text-[#06df72] dark:bg-[#0a0a0a]':
                                !bullet?.active,
                            'border-black bg-[#06df72] font-bold text-black dark:bg-[#262626]':
                                bullet?.active,
                        })}
                    >
                        {bullet.title}
                    </Link>
                ))}
            </div>
            <div className="flex h-full flex-1 flex-col items-center justify-center rounded-tr-xl rounded-br-xl border border-[#06df72] bg-white p-4 pl-8 dark:bg-neutral-800">
                {children}
            </div>
        </div>
    );
}
