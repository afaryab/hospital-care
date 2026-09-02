import DmsBreadcrumbs from '@/elements/dms/breadcrumbs';
import DmsBrowserTable from '@/elements/dms/browser-table';
import DmsCreateFolderDialog from '@/elements/dms/create-folder-dialog';
import DmsMoveOrCopyDialog from '@/elements/dms/move-or-copy-dialog';
import DmsRenameDialog from '@/elements/dms/rename-dialog';
import DmsShareDialog from '@/elements/dms/share-dialog';
import DmsToolbar from '@/elements/dms/toolbar';
import DmsZipUploadDialog from '@/elements/dms/zip-upload-dialog';
import AppLayout from '@/layouts/app-layout';
import dms from '@/routes/dms';
import {
    type BreadcrumbItem,
    type DmsClassification,
    type DmsDocument,
    type DmsFolder,
    type DmsFolderOption,
} from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState, type DragEvent } from 'react';
import { toast } from 'sonner';

interface DmsPageProps {
    folder: DmsFolder | null;
    breadcrumbs: DmsFolder[];
    folders: DmsFolder[];
    documents: DmsDocument[];
    folderOptions: DmsFolderOption[];
    classifications: DmsClassification[];
    [key: string]: unknown;
}

type DialogState =
    | { type: 'create-folder' }
    | {
          type: 'rename';
          target:
              | { type: 'folder'; item: DmsFolder }
              | { type: 'document'; item: DmsDocument };
      }
    | {
          type: 'move' | 'copy';
          target:
              | { type: 'folder'; item: DmsFolder }
              | { type: 'document'; item: DmsDocument };
      }
    | { type: 'share'; document: DmsDocument }
    | { type: 'upload-zip' }
    | null;

export default function DmsIndex() {
    const {
        folder,
        breadcrumbs,
        folders,
        documents,
        folderOptions,
        classifications,
    } = usePage<DmsPageProps>().props;

    const [dialog, setDialog] = useState<DialogState>(null);
    const [uploadProgress, setUploadProgress] = useState<number | null>(null);
    const [isDragging, setIsDragging] = useState(false);

    const appBreadcrumbs: BreadcrumbItem[] = [
        { title: 'Documents', href: dms.index().url },
        ...breadcrumbs.map((crumb) => ({
            title: crumb.name,
            href: dms.index(crumb).url,
        })),
    ];

    function uploadFiles(files: FileList | File[]) {
        if (!folder) {
            toast.error('Open a folder before uploading.');

            return;
        }

        const queue = Array.from(files);

        const uploadNext = () => {
            const file = queue.shift();

            if (!file) {
                setUploadProgress(null);

                return;
            }

            router.post(
                dms.documents.store().url,
                { file, folder_uuid: folder.uuid },
                {
                    forceFormData: true,
                    preserveScroll: true,
                    onProgress: (event) =>
                        setUploadProgress(event?.percentage ?? null),
                    onSuccess: () => {
                        toast.success('Document uploaded.');
                        uploadNext();
                    },
                    onError: (errors) => {
                        toast.error(
                            Object.values(errors)[0] ?? 'Upload failed.',
                        );
                        uploadNext();
                    },
                },
            );
        };

        uploadNext();
    }

    function handleDrop(event: DragEvent<HTMLDivElement>) {
        event.preventDefault();
        setIsDragging(false);

        if (event.dataTransfer.files.length) {
            uploadFiles(event.dataTransfer.files);
        }
    }

    return (
        <AppLayout breadcrumbs={appBreadcrumbs}>
            <Head title="Documents" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <DmsBreadcrumbs trail={breadcrumbs} />

                <DmsToolbar
                    canUpload={!!folder}
                    uploadProgress={uploadProgress}
                    onCreateFolder={() => setDialog({ type: 'create-folder' })}
                    onUploadFiles={uploadFiles}
                    onUploadZip={() => setDialog({ type: 'upload-zip' })}
                />

                <div
                    className="relative flex flex-1 flex-col"
                    onDragOver={(event) => {
                        event.preventDefault();
                        if (folder) {
                            setIsDragging(true);
                        }
                    }}
                    onDragLeave={() => setIsDragging(false)}
                    onDrop={handleDrop}
                >
                    <DmsBrowserTable
                        folders={folders}
                        documents={documents}
                        onRenameFolder={(item) =>
                            setDialog({
                                type: 'rename',
                                target: { type: 'folder', item },
                            })
                        }
                        onMoveFolder={(item) =>
                            setDialog({
                                type: 'move',
                                target: { type: 'folder', item },
                            })
                        }
                        onCopyFolder={(item) =>
                            setDialog({
                                type: 'copy',
                                target: { type: 'folder', item },
                            })
                        }
                        onRenameDocument={(item) =>
                            setDialog({
                                type: 'rename',
                                target: { type: 'document', item },
                            })
                        }
                        onMoveDocument={(item) =>
                            setDialog({
                                type: 'move',
                                target: { type: 'document', item },
                            })
                        }
                        onCopyDocument={(item) =>
                            setDialog({
                                type: 'copy',
                                target: { type: 'document', item },
                            })
                        }
                        onShareDocument={(document) =>
                            setDialog({ type: 'share', document })
                        }
                    />

                    {isDragging && folder && (
                        <div className="pointer-events-none absolute inset-0 z-10 flex items-center justify-center rounded-xl border-2 border-dashed border-primary bg-primary/5 text-sm font-medium text-primary">
                            Drop files to upload
                        </div>
                    )}
                </div>
            </div>

            {dialog?.type === 'create-folder' && (
                <DmsCreateFolderDialog
                    parentUuid={folder?.uuid ?? null}
                    classifications={classifications}
                    onClose={() => setDialog(null)}
                />
            )}

            {dialog?.type === 'rename' && (
                <DmsRenameDialog
                    target={dialog.target}
                    onClose={() => setDialog(null)}
                />
            )}

            {(dialog?.type === 'move' || dialog?.type === 'copy') && (
                <DmsMoveOrCopyDialog
                    mode={dialog.type}
                    target={dialog.target}
                    folderOptions={folderOptions}
                    onClose={() => setDialog(null)}
                />
            )}

            {dialog?.type === 'share' && (
                <DmsShareDialog
                    document={dialog.document}
                    onClose={() => setDialog(null)}
                />
            )}

            {dialog?.type === 'upload-zip' && folder && (
                <DmsZipUploadDialog
                    folderUuid={folder.uuid}
                    onClose={() => setDialog(null)}
                />
            )}
        </AppLayout>
    );
}
