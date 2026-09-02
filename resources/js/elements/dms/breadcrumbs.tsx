import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import dms from '@/routes/dms';
import { type DmsFolder } from '@/types';
import { Link } from '@inertiajs/react';
import { Home } from 'lucide-react';

export default function DmsBreadcrumbs({ trail }: { trail: DmsFolder[] }) {
    return (
        <Breadcrumb>
            <BreadcrumbList>
                <BreadcrumbItem>
                    {trail.length === 0 ? (
                        <BreadcrumbPage className="flex items-center gap-1">
                            <Home className="size-4" />
                            My Drive
                        </BreadcrumbPage>
                    ) : (
                        <BreadcrumbLink asChild>
                            <Link
                                href={dms.index().url}
                                className="flex items-center gap-1"
                            >
                                <Home className="size-4" />
                                My Drive
                            </Link>
                        </BreadcrumbLink>
                    )}
                </BreadcrumbItem>
                {trail.map((folder, index) => (
                    <span
                        key={folder.uuid}
                        className="flex items-center gap-1.5 sm:gap-2.5"
                    >
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            {index === trail.length - 1 ? (
                                <BreadcrumbPage>{folder.name}</BreadcrumbPage>
                            ) : (
                                <BreadcrumbLink asChild>
                                    <Link href={dms.index(folder).url}>
                                        {folder.name}
                                    </Link>
                                </BreadcrumbLink>
                            )}
                        </BreadcrumbItem>
                    </span>
                ))}
            </BreadcrumbList>
        </Breadcrumb>
    );
}
