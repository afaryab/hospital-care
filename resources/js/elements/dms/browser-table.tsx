import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { isOfficeEditable } from '@/elements/dms/utils';
import dms from '@/routes/dms';
import onlyoffice from '@/routes/onlyoffice';
import { type DmsDocument, type DmsFolder } from '@/types';
import { Link, router } from '@inertiajs/react';
import {
    Copy,
    Download,
    ExternalLink,
    File,
    FolderIcon,
    Lock,
    MoreVertical,
    Move as MoveIcon,
    Pencil,
    Share2,
    Trash2,
    Unlock,
} from 'lucide-react';
import { toast } from 'sonner';

interface BrowserTableProps {
    folders: DmsFolder[];
    documents: DmsDocument[];
    onRenameFolder: (folder: DmsFolder) => void;
    onMoveFolder: (folder: DmsFolder) => void;
    onCopyFolder: (folder: DmsFolder) => void;
    onRenameDocument: (document: DmsDocument) => void;
    onMoveDocument: (document: DmsDocument) => void;
    onCopyDocument: (document: DmsDocument) => void;
    onShareDocument: (document: DmsDocument) => void;
}

export default function DmsBrowserTable({
    folders,
    documents,
    onRenameFolder,
    onMoveFolder,
    onCopyFolder,
    onRenameDocument,
    onMoveDocument,
    onCopyDocument,
    onShareDocument,
}: BrowserTableProps) {
    if (folders.length === 0 && documents.length === 0) {
        return (
            <div className="flex flex-1 items-center justify-center rounded-xl border border-dashed p-12 text-sm text-muted-foreground">
                This folder is empty.
            </div>
        );
    }

    function deleteFolder(folder: DmsFolder) {
        if (!confirm(`Delete "${folder.name}"?`)) {
            return;
        }

        router.delete(dms.folders.destroy(folder).url, {
            preserveScroll: true,
            onSuccess: () => toast.success('Folder deleted.'),
            onError: (errors) =>
                toast.error(
                    Object.values(errors)[0] ?? 'Could not delete folder.',
                ),
        });
    }

    function deleteDocument(document: DmsDocument) {
        if (!confirm(`Delete "${document.name}"?`)) {
            return;
        }

        router.delete(dms.documents.destroy(document).url, {
            preserveScroll: true,
            onSuccess: () => toast.success('Document deleted.'),
        });
    }

    function toggleLock(document: DmsDocument) {
        const action = document.is_locked
            ? dms.documents.unlock
            : dms.documents.lock;

        router.post(
            action(document).url,
            {},
            {
                preserveScroll: true,
                onSuccess: () =>
                    toast.success(
                        document.is_locked
                            ? 'Document unlocked.'
                            : 'Document locked for editing.',
                    ),
                onError: (errors) =>
                    toast.error(
                        Object.values(errors)[0] ?? 'Could not update lock.',
                    ),
            },
        );
    }

    return (
        <div className="overflow-x-auto rounded-xl border">
            <table className="w-full text-left text-sm">
                <thead className="bg-muted/50 text-xs text-muted-foreground uppercase">
                    <tr>
                        <th scope="col" className="px-4 py-3">
                            Name
                        </th>
                        <th scope="col" className="px-4 py-3">
                            Classification
                        </th>
                        <th scope="col" className="px-4 py-3">
                            Status
                        </th>
                        <th scope="col" className="w-10 px-4 py-3" />
                    </tr>
                </thead>
                <tbody>
                    {folders.map((folder) => (
                        <tr
                            key={folder.uuid}
                            className="border-t hover:bg-muted/30"
                        >
                            <td className="px-4 py-2.5">
                                <Link
                                    href={dms.index(folder).url}
                                    className="flex items-center gap-2 font-medium"
                                >
                                    <FolderIcon className="size-4 shrink-0 text-muted-foreground" />
                                    {folder.name}
                                </Link>
                            </td>
                            <td className="px-4 py-2.5 text-muted-foreground">
                                {folder.classification?.name ?? '—'}
                            </td>
                            <td className="px-4 py-2.5">
                                {folder.is_system && (
                                    <Badge variant="secondary">System</Badge>
                                )}
                            </td>
                            <td className="px-4 py-2.5 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger className="rounded-md p-1 hover:bg-muted">
                                        <MoreVertical className="size-4" />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem asChild>
                                            <a
                                                href={
                                                    dms.folders.zip(folder).url
                                                }
                                            >
                                                <Download className="size-4" />
                                                Download zip
                                            </a>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() => onCopyFolder(folder)}
                                        >
                                            <Copy className="size-4" />
                                            Copy
                                        </DropdownMenuItem>
                                        {!folder.is_system && (
                                            <>
                                                <DropdownMenuItem
                                                    onClick={() =>
                                                        onRenameFolder(folder)
                                                    }
                                                >
                                                    <Pencil className="size-4" />
                                                    Rename
                                                </DropdownMenuItem>
                                                <DropdownMenuItem
                                                    onClick={() =>
                                                        onMoveFolder(folder)
                                                    }
                                                >
                                                    <MoveIcon className="size-4" />
                                                    Move
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem
                                                    variant="destructive"
                                                    onClick={() =>
                                                        deleteFolder(folder)
                                                    }
                                                >
                                                    <Trash2 className="size-4" />
                                                    Delete
                                                </DropdownMenuItem>
                                            </>
                                        )}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    ))}

                    {documents.map((document) => (
                        <tr
                            key={document.uuid}
                            className="border-t hover:bg-muted/30"
                        >
                            <td className="px-4 py-2.5">
                                <span className="flex items-center gap-2 font-medium">
                                    <File className="size-4 shrink-0 text-muted-foreground" />
                                    {document.name}
                                    <span className="text-xs font-normal text-muted-foreground">
                                        v{document.current_version}
                                    </span>
                                </span>
                            </td>
                            <td className="px-4 py-2.5 text-muted-foreground">
                                {document.classification?.name ?? '—'}
                            </td>
                            <td className="px-4 py-2.5">
                                {document.is_locked && (
                                    <Badge variant="outline" className="gap-1">
                                        <Lock className="size-3" />
                                        {document.lockedBy?.name ?? 'Locked'}
                                    </Badge>
                                )}
                            </td>
                            <td className="px-4 py-2.5 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger className="rounded-md p-1 hover:bg-muted">
                                        <MoreVertical className="size-4" />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem asChild>
                                            <a
                                                href={
                                                    dms.documents.download(
                                                        document,
                                                    ).url
                                                }
                                            >
                                                <Download className="size-4" />
                                                Download
                                            </a>
                                        </DropdownMenuItem>
                                        {isOfficeEditable(document.name) && (
                                            <DropdownMenuItem asChild>
                                                <a
                                                    href={
                                                        onlyoffice.editor(
                                                            document,
                                                        ).url
                                                    }
                                                    target="_blank"
                                                    rel="noreferrer"
                                                >
                                                    <ExternalLink className="size-4" />
                                                    Edit
                                                </a>
                                            </DropdownMenuItem>
                                        )}
                                        <DropdownMenuItem
                                            onClick={() =>
                                                onRenameDocument(document)
                                            }
                                        >
                                            <Pencil className="size-4" />
                                            Rename
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                onMoveDocument(document)
                                            }
                                        >
                                            <MoveIcon className="size-4" />
                                            Move
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                onCopyDocument(document)
                                            }
                                        >
                                            <Copy className="size-4" />
                                            Copy
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                onShareDocument(document)
                                            }
                                        >
                                            <Share2 className="size-4" />
                                            Share
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() => toggleLock(document)}
                                        >
                                            {document.is_locked ? (
                                                <>
                                                    <Unlock className="size-4" />
                                                    Unlock
                                                </>
                                            ) : (
                                                <>
                                                    <Lock className="size-4" />
                                                    Lock
                                                </>
                                            )}
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            variant="destructive"
                                            onClick={() =>
                                                deleteDocument(document)
                                            }
                                        >
                                            <Trash2 className="size-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
