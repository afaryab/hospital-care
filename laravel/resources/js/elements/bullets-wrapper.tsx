import { Link } from "@inertiajs/react";
import clsx from "clsx";



export default function BulletsWrapper({ bullets = [], children}:any) {
    return <div className='h-full flex flex-row rounded-xl'>
        <div className='flex-shrink flex flex-col h-full items-center justify-start space-x-2 p-0'>
            {bullets.map((bullet:any, index:number) => (
                <Link key={index} href={bullet.url} className={
                    clsx({
                        'text-md  text-[#1c398e] [writing-mode:vertical-lr] whitespace-nowrap uppercase p-2 border-2 border-r-0 cursor-pointer' : true,
                        'rounded-tl-xl' : index === 0,
                        'rounded-bl-xl' : index === bullets.length -1,
                        'hover:bg-[#1c398e] dark:bg-[#0a0a0a] hover:text-[#06df72] border-[#06df72] hover:border-[#1c398e]' : !bullet?.active,
                        'bg-[#06df72] dark:bg-[#262626] text-black border-black font-bold' : bullet?.active,
                    })
                }>{bullet.title}</Link>
            ))}
        </div>
        <div className='flex-1 flex flex-col h-full items-center justify-center p-4 pl-8 border-[#06df72] border rounded-tr-xl rounded-br-xl bg-white dark:bg-neutral-800'>
            {children}
        </div>
    </div>
}